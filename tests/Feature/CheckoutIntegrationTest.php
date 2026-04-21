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

        $this->assertSame('XENDIT', $order->payment_method);
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame('inv-test-001', $order->xendit_invoice_id);
        $this->assertSame('GCASH', $order->xendit_payment_method);
        $this->assertSame('Quezon City', $order->delivery_address['city_name']);
        $this->assertSame('Baguio City', $order->pickup_address['city_name']);
        $this->assertDatabaseMissing('carts', ['user_id' => $user->id]);

        Http::assertSent(fn ($request) => $request->url() === 'http://tracker.test/api/orders/sync'
            && $request['payment_method'] === 'XENDIT'
            && $request['payment_status'] === 'pending'
            && str_contains((string) $request['prgc_ref'], 'PU:140000000-140100000-140604000-140604001')
            && $request['pickup_address']['city_name'] === 'Baguio City'
            && $request['delivery_address']['city_name'] === 'Quezon City');

        Http::assertSent(fn ($request) => str_contains($request->url(), 'https://api.xendit.co/v2/invoices'));
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

        $this->assertSame('approved', $order->status);
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('GCASH', $order->xendit_payment_method);
        $this->assertNotNull($order->approved_at);
        $this->assertNotNull($order->payment_paid_at);

        Http::assertSent(fn ($request) => str_contains($request->url(), "/orders/{$order->id}/status")
            && $request['status'] === 'approved'
            && $request['payment_status'] === 'paid');
    }

    public function test_xendit_webhook_keeps_expired_payments_unapproved_and_notifies_tracker(): void
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
            'xendit_invoice_id' => 'inv-test-002',
            'xendit_reference_id' => 'storix-order-100',
            'pickup_address' => ['city_name' => 'Baguio City'],
            'delivery_address' => ['city_name' => 'Quezon City'],
            'placed_at' => now(),
        ]);

        $response = $this->postJson(route('xendit.webhook'), [
            'id' => 'inv-test-002',
            'external_id' => 'storix-order-100',
            'status' => 'EXPIRED',
            'payment_method' => 'GCASH',
            'expiry_date' => now()->addHour()->toIso8601String(),
        ], ['x-callback-token' => 'callback-secret']);

        $response->assertNoContent();

        $order->refresh();

        $this->assertSame('pending', $order->status);
        $this->assertSame('expired', $order->payment_status);
        $this->assertNull($order->approved_at);
        $this->assertNull($order->payment_paid_at);
        $this->assertNotNull($order->payment_expires_at);

        Http::assertSent(fn ($request) => str_contains($request->url(), "/orders/{$order->id}/status")
            && $request['status'] === 'pending'
            && $request['payment_status'] === 'expired');
    }

    public function test_user_can_confirm_paid_xendit_payment_and_sync_tracker(): void
    {
        config()->set('services.xendit.key', 'test-xendit-key');
        config()->set('services.tracker.url', 'http://tracker.test/api');
        config()->set('services.tracker.token', 'tracker-token');

        Http::fake([
            'https://api.xendit.co/v2/invoices/*' => Http::response([
                'id' => 'inv-test-300',
                'external_id' => 'storix-order-300',
                'status' => 'PAID',
                'amount' => 88.00,
                'paid_amount' => 88.00,
                'payment_method' => 'GCASH',
                'expiry_date' => now()->addDay()->toIso8601String(),
            ], 200),
            'http://tracker.test/api/orders/*/status' => Http::response(['message' => 'ok'], 200),
        ]);

        $user = User::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => 88.00,
            'status' => 'pending',
            'payment_method' => 'GCASH',
            'payment_status' => 'pending',
            'payment_amount' => 88.00,
            'xendit_invoice_id' => 'inv-test-300',
            'xendit_invoice_url' => 'https://pay.xendit.test/inv-test-300',
            'xendit_payment_method' => 'GCASH',
            'xendit_reference_id' => 'storix-order-300',
            'pickup_address' => ['city_name' => 'Arayat'],
            'delivery_address' => ['city_name' => 'Arayat'],
            'placed_at' => now(),
        ]);

        $this->actingAs($user)
            ->post(route('user.orders.confirm-payment', $order))
            ->assertSessionHas('success');

        $order->refresh();

        $this->assertSame('approved', $order->status);
        $this->assertSame('paid', $order->payment_status);
        $this->assertNotNull($order->approved_at);
        $this->assertNotNull($order->payment_paid_at);

        Http::assertSent(fn ($request) => str_contains($request->url(), "/orders/{$order->id}/status")
            && $request['status'] === 'approved'
            && $request['payment_status'] === 'paid');

        Http::assertSent(fn ($request) => str_contains($request->url(), 'https://api.xendit.co/v2/invoices/'));
    }

    public function test_user_confirm_payment_keeps_pending_when_xendit_payment_not_yet_paid(): void
    {
        config()->set('services.xendit.key', 'test-xendit-key');
        config()->set('services.tracker.url', 'http://tracker.test/api');
        config()->set('services.tracker.token', 'tracker-token');

        Http::fake([
            'https://api.xendit.co/v2/invoices/*' => Http::response([
                'id' => 'inv-test-301',
                'external_id' => 'storix-order-301',
                'status' => 'PENDING',
                'amount' => 99.00,
                'payment_method' => 'GCASH',
                'expiry_date' => now()->addDay()->toIso8601String(),
            ], 200),
            'http://tracker.test/api/orders/*/status' => Http::response(['message' => 'ok'], 200),
        ]);

        $user = User::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => 99.00,
            'status' => 'pending',
            'payment_method' => 'GCASH',
            'payment_status' => 'pending',
            'payment_amount' => 99.00,
            'xendit_invoice_id' => 'inv-test-301',
            'xendit_invoice_url' => 'https://pay.xendit.test/inv-test-301',
            'xendit_payment_method' => 'GCASH',
            'xendit_reference_id' => 'storix-order-301',
            'pickup_address' => ['city_name' => 'Arayat'],
            'delivery_address' => ['city_name' => 'Arayat'],
            'placed_at' => now(),
        ]);

        $this->actingAs($user)
            ->post(route('user.orders.confirm-payment', $order))
            ->assertSessionHas('info');

        $order->refresh();

        $this->assertSame('pending', $order->status);
        $this->assertSame('pending', $order->payment_status);
        $this->assertNull($order->approved_at);
        $this->assertNull($order->payment_paid_at);

        Http::assertSent(fn ($request) => str_contains($request->url(), "/orders/{$order->id}/status")
            && $request['status'] === 'pending'
            && $request['payment_status'] === 'pending');
    }

    public function test_user_can_fetch_order_status_snapshot_after_xendit_webhook_updates_payment(): void
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
            'xendit_invoice_id' => 'inv-test-777',
            'xendit_invoice_url' => 'https://pay.xendit.test/inv-test-777',
            'xendit_reference_id' => 'storix-order-777',
            'pickup_address' => ['city_name' => 'Baguio City'],
            'delivery_address' => ['city_name' => 'Quezon City'],
            'placed_at' => now(),
        ]);

        $this->postJson(route('xendit.webhook'), [
            'id' => 'inv-test-777',
            'external_id' => 'storix-order-777',
            'status' => 'PAID',
            'payment_method' => 'GCASH',
            'paid_amount' => 101,
        ], ['x-callback-token' => 'callback-secret'])->assertNoContent();

        $this->actingAs($user)
            ->getJson(route('user.orders.status-snapshot', $order))
            ->assertOk()
            ->assertJson([
                'id' => $order->id,
                'status' => 'approved',
                'payment_status' => 'paid',
                'xendit_invoice_url' => 'https://pay.xendit.test/inv-test-777',
            ])
            ->assertJsonPath('payment_paid_at', fn ($value) => filled($value));
    }

    public function test_user_is_auto_approved_when_xendit_redirects_back_after_paid_payment(): void
    {
        config()->set('services.xendit.key', 'test-xendit-key');
        config()->set('services.tracker.url', 'http://tracker.test/api');
        config()->set('services.tracker.token', 'tracker-token');

        Http::fake([
            'https://api.xendit.co/v2/invoices/*' => Http::response([
                'id' => 'inv-test-880',
                'external_id' => 'storix-order-880',
                'status' => 'PAID',
                'amount' => 120.00,
                'paid_amount' => 120.00,
                'payment_method' => 'GCASH',
            ], 200),
            'http://tracker.test/api/orders/*/status' => Http::response(['message' => 'ok'], 200),
        ]);

        $user = User::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => 120.00,
            'status' => 'pending',
            'payment_method' => 'XENDIT',
            'payment_status' => 'pending',
            'payment_amount' => 120.00,
            'xendit_invoice_id' => 'inv-test-880',
            'xendit_invoice_url' => 'https://pay.xendit.test/inv-test-880',
            'xendit_payment_method' => 'GCASH',
            'xendit_reference_id' => 'storix-order-880',
            'pickup_address' => ['city_name' => 'Baguio City'],
            'delivery_address' => ['city_name' => 'Quezon City'],
            'placed_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('user.orders.payment-return', ['order' => $order, 'status' => 'success']))
            ->assertRedirect(route('user.orders.show', $order))
            ->assertSessionHas('success');

        $order->refresh();

        $this->assertSame('approved', $order->status);
        $this->assertSame('paid', $order->payment_status);
        $this->assertNotNull($order->approved_at);
        $this->assertNotNull($order->payment_paid_at);
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
        $this->assertSame('XENDIT', $order->payment_method);
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame('XENDIT', $order->xendit_payment_method);
        $this->assertStringContainsString('Xendit API key is not configured yet', $order->notes);

        Http::assertSent(fn ($request) => $request->url() === 'http://tracker.test/api/orders/sync'
            && $request['payment_status'] === 'pending'
            && $request['xendit_invoice_id'] === sprintf('STORIX-XENDIT-%06d', $order->id));
    }

    public function test_checkout_does_not_crash_when_tracker_api_is_unreachable(): void
    {
        config()->set('services.xendit.key', null);
        config()->set('services.tracker.url', 'http://127.0.0.1:8001/api');
        config()->set('services.tracker.token', 'tracker-token');

        Http::fake([
            'http://127.0.0.1:8001/api/orders/sync' => Http::failedConnection(),
        ]);

        $user = User::factory()->create();
        $category = Category::create(['name' => 'Apparel']);
        $product = Product::create([
            'name' => 'Tracker Offline Tee',
            'sku' => 'TEE-004',
            'category_id' => $category->id,
            'quantity' => 10,
            'price' => 60.00,
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
        $this->assertSame('pending', $order->payment_status);
    }

    public function test_checkout_keeps_pending_when_xendit_is_not_configured_even_with_manual_flag(): void
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
            'name' => 'Manual Confirm Tee',
            'sku' => 'TEE-003',
            'category_id' => $category->id,
            'quantity' => 10,
            'price' => 55.00,
        ]);

        Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $payload = $this->checkoutPayload();
        $payload['manual_payment_confirmed'] = '1';

        $response = $this->actingAs($user)->post(route('user.orders.store'), $payload);

        $order = Order::query()->firstOrFail();

        $response->assertRedirect(route('user.orders.show', $order));
        $response->assertSessionHas('success');

        $this->assertSame('pending', $order->status);
        $this->assertSame('pending', $order->payment_status);
        $this->assertNull($order->approved_at);
        $this->assertNull($order->payment_paid_at);
        $this->assertStringContainsString('Xendit API key is not configured yet', $order->notes);

        Http::assertSent(fn ($request) => $request->url() === 'http://tracker.test/api/orders/sync'
            && $request['status'] === 'pending'
            && $request['payment_status'] === 'pending');
    }

    public function test_admin_can_resync_tracker_without_manual_payment_confirmation(): void
    {
        config()->set('services.tracker.url', 'http://tracker.test/api');
        config()->set('services.tracker.token', 'tracker-token');

        Http::fake([
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
            ->post(route('admin.orders.resync-tracker', $order))
            ->assertSessionHas('success');

        $order->refresh();

        $this->assertSame('completed', $order->status);
        $this->assertSame('pending', $order->payment_status);
        $this->assertNull($order->approved_at);
        $this->assertNull($order->payment_paid_at);

        Http::assertSent(fn ($request) => $request->url() === 'http://tracker.test/api/orders/sync'
            && str_contains((string) $request['prgc_ref'], 'PU:030000000-035400000-035403000-ARAYAT-POBLACION'));
    }

    public function test_admin_confirm_payment_endpoint_is_not_available(): void
    {
        Http::fake();

        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => 50.50,
            'status' => 'pending',
            'payment_method' => 'GCASH',
            'payment_status' => 'pending',
            'payment_amount' => 50.50,
            'pickup_address' => ['city_name' => 'Arayat'],
            'delivery_address' => ['city_name' => 'Arayat'],
            'placed_at' => now(),
        ]);

        $this->actingAs($admin)
            ->patch("/admin/orders/{$order->id}/confirm-payment")
            ->assertNotFound();

        $order->refresh();

        $this->assertSame('pending', $order->status);
        $this->assertSame('pending', $order->payment_status);
        $this->assertNull($order->approved_at);
        $this->assertNull($order->payment_paid_at);

        Http::assertNothingSent();
    }

    public function test_admin_cannot_mark_hosted_xendit_order_as_approved_before_payment_is_paid(): void
    {
        config()->set('services.tracker.url', 'http://tracker.test/api');
        config()->set('services.tracker.token', 'tracker-token');

        Http::fake();

        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => 50.50,
            'status' => 'pending',
            'payment_method' => 'GCASH',
            'payment_status' => 'pending',
            'payment_amount' => 50.50,
            'xendit_invoice_id' => 'inv-test-501',
            'xendit_invoice_url' => 'https://pay.xendit.test/inv-test-501',
            'xendit_payment_method' => 'GCASH',
            'xendit_reference_id' => 'storix-order-501',
            'pickup_address' => ['city_name' => 'Arayat'],
            'delivery_address' => ['city_name' => 'Arayat'],
            'placed_at' => now(),
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.orders.update', $order), ['status' => 'approved'])
            ->assertSessionHas('error');

        $order->refresh();

        $this->assertSame('pending', $order->status);
        $this->assertSame('pending', $order->payment_status);
        $this->assertNull($order->approved_at);

        Http::assertNothingSent();
    }

    protected function checkoutPayload(): array
    {
        return [
            'payment_method' => 'XENDIT',
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
