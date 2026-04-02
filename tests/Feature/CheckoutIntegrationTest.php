<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckoutIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_creates_order_with_addresses_payment_and_tracker_sync(): void
    {
        config()->set('services.xendit.key', 'test-xendit-key');
        config()->set('services.tracker.url', 'http://tracker.test/api');
        config()->set('services.tracker.token', 'tracker-token');

        Http::fake([
            'https://api.xendit.co/v2/invoices' => Http::response([
                'id' => 'inv-test-001',
                'external_id' => 'storix-order-1',
                'invoice_url' => 'https://pay.xendit.test/inv-test-001',
                'payment_method' => 'GCASH',
                'expiry_date' => now()->addDay()->toIso8601String(),
            ], 200),
            'http://tracker.test/api/orders/sync' => Http::response(['message' => 'ok'], 201),
        ]);

        $user = User::factory()->create();
        $category = Category::create(['name' => 'Apparel']);
        $product = Product::create([
            'name' => 'Chrome Heart Tee',
            'sku' => 'TEE-001',
            'category_id' => $category->id,
            'quantity' => 10,
            'price' => 50.50,
        ]);

        Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user)->post(route('user.orders.store'), $this->checkoutPayload());

        $response->assertRedirect('https://pay.xendit.test/inv-test-001');

        $order = Order::query()->firstOrFail();

        $this->assertSame('ANY', $order->payment_method);
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame('inv-test-001', $order->xendit_invoice_id);
        $this->assertSame('GCASH', $order->xendit_payment_method);
        $this->assertSame('Quezon City', $order->delivery_address['city_name']);
        $this->assertSame('Baguio City', $order->pickup_address['city_name']);
        $this->assertDatabaseMissing('carts', ['user_id' => $user->id]);

        Http::assertSent(fn ($request) => $request->url() === 'http://tracker.test/api/orders/sync'
            && $request['payment_method'] === 'ANY'
            && $request['payment_status'] === 'pending'
            && str_contains((string) $request['prgc_ref'], 'PU:140000000-140100000-140604000-140604001')
            && $request['pickup_address']['city_name'] === 'Baguio City'
            && $request['delivery_address']['city_name'] === 'Quezon City');
    }

    public function test_xendit_webhook_updates_payment_status_and_notifies_tracker(): void
    {
        config()->set('services.tracker.url', 'http://tracker.test/api');
        config()->set('services.tracker.token', 'tracker-token');
        config()->set('services.xendit.webhook_token', 'callback-secret');

        Http::fake([
            'http://tracker.test/api/orders/*/status' => Http::response(['message' => 'ok'], 200),
        ]);

        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => 101.00,
            'status' => 'pending',
            'payment_method' => 'GCASH',
            'payment_status' => 'pending',
            'payment_amount' => 101.00,
            'xendit_invoice_id' => 'inv-test-001',
            'xendit_reference_id' => 'storix-order-99',
            'pickup_address' => ['city_name' => 'Baguio City'],
            'delivery_address' => ['city_name' => 'Quezon City'],
            'placed_at' => now(),
        ]);

        $response = $this->postJson(route('xendit.webhook'), [
            'id' => 'inv-test-001',
            'external_id' => 'storix-order-99',
            'status' => 'PAID',
            'payment_method' => 'GCASH',
            'paid_amount' => 101,
        ], ['x-callback-token' => 'callback-secret']);

        $response->assertNoContent();

        $order->refresh();

        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('GCASH', $order->xendit_payment_method);
        $this->assertNotNull($order->payment_paid_at);

        Http::assertSent(fn ($request) => str_contains($request->url(), "/orders/{$order->id}/status")
            && $request['payment_status'] === 'paid');
    }

    public function test_checkout_still_creates_order_when_xendit_is_not_configured(): void
    {
        config()->set('services.xendit.key', null);
        config()->set('services.tracker.url', 'http://tracker.test/api');
        config()->set('services.tracker.token', 'tracker-token');

        Http::fake([
            'http://tracker.test/api/orders/sync' => Http::response(['message' => 'ok'], 201),
        ]);

        $user = User::factory()->create();
        $category = Category::create(['name' => 'Apparel']);
        $product = Product::create([
            'name' => 'Fallback Tee',
            'sku' => 'TEE-002',
            'category_id' => $category->id,
            'quantity' => 10,
            'price' => 50.50,
        ]);

        Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)->post(route('user.orders.store'), $this->checkoutPayload());

        $order = Order::query()->firstOrFail();

        $response->assertRedirect(route('user.orders.show', $order));
        $response->assertSessionHas('success');

        $this->assertSame(sprintf('STORIX-XENDIT-%06d', $order->id), $order->xendit_invoice_id);
        $this->assertSame('ANY', $order->payment_method);
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame('ANY', $order->xendit_payment_method);
        $this->assertStringContainsString('Xendit API key is not configured yet', $order->notes);

        Http::assertSent(fn ($request) => $request->url() === 'http://tracker.test/api/orders/sync'
            && $request['payment_status'] === 'pending'
            && $request['xendit_invoice_id'] === sprintf('STORIX-XENDIT-%06d', $order->id));
    }

    public function test_admin_can_confirm_payment_and_resync_tracker(): void
    {
        config()->set('services.tracker.url', 'http://tracker.test/api');
        config()->set('services.tracker.token', 'tracker-token');

        Http::fake([
            'http://tracker.test/api/orders/*/status' => Http::response(['message' => 'ok'], 200),
            'http://tracker.test/api/orders/sync' => Http::response(['message' => 'ok'], 201),
        ]);

        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => 50.50,
            'status' => 'completed',
            'payment_method' => 'GCASH',
            'payment_status' => 'pending',
            'payment_amount' => 50.50,
            'pickup_address' => [
                'region_code' => '030000000',
                'province_code' => '035400000',
                'city_code' => '035403000',
                'barangay_code' => 'ARAYAT-POBLACION',
                'city_name' => 'Arayat',
            ],
            'delivery_address' => [
                'region_code' => '030000000',
                'province_code' => '035400000',
                'city_code' => '035403000',
                'barangay_code' => 'ARAYAT-POBLACION',
                'city_name' => 'Arayat',
            ],
            'placed_at' => now(),
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.orders.confirm-payment', $order))
            ->assertSessionHas('success');

        $order->refresh();

        $this->assertSame('paid', $order->payment_status);
        $this->assertNotNull($order->payment_paid_at);

        $this->actingAs($admin)
            ->post(route('admin.orders.resync-tracker', $order))
            ->assertSessionHas('success');

        Http::assertSent(fn ($request) => str_contains($request->url(), "/orders/{$order->id}/status")
            && $request['payment_status'] === 'paid');

        Http::assertSent(fn ($request) => $request->url() === 'http://tracker.test/api/orders/sync'
            && str_contains((string) $request['prgc_ref'], 'PU:030000000-035400000-035403000-ARAYAT-POBLACION'));
    }

    protected function checkoutPayload(): array
    {
        return [
            'payment_method' => 'ANY',
            'pickup_address' => [
                'region_code' => '140000000',
                'region_name' => 'CAR',
                'province_code' => '140100000',
                'province_name' => 'Abra',
                'city_code' => '140604000',
                'city_name' => 'Baguio City',
                'barangay_code' => '140604001',
                'barangay_name' => 'Session Road',
                'street_address' => 'Upper Session Road',
                'contact_number' => '09171234567',
            ],
            'delivery_address' => [
                'region_code' => '130000000',
                'region_name' => 'NCR',
                'province_code' => '',
                'province_name' => '',
                'city_code' => '137404000',
                'city_name' => 'Quezon City',
                'barangay_code' => '137404123',
                'barangay_name' => 'Bagumbayan',
                'street_address' => 'Commonwealth Avenue',
                'contact_number' => '09999888777',
            ],
            'notes' => 'Leave at lobby reception.',
        ];
    }
}
