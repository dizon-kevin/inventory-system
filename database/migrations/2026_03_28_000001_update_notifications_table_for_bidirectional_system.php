<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (! Schema::hasColumn('notifications', 'title')) {
                $table->string('title')->nullable()->after('type');
            }

            if (! Schema::hasColumn('notifications', 'description')) {
                $table->text('description')->nullable()->after('title');
            }

            if (! Schema::hasColumn('notifications', 'order_id')) {
                $table->foreignId('order_id')->nullable()->after('description')->constrained()->nullOnDelete();
            }
        });

        if (Schema::hasColumn('notifications', 'data')) {
            DB::table('notifications')
                ->orderBy('id')
                ->get()
                ->each(function ($notification) {
                    $payload = json_decode($notification->data ?? '', true);
                    $message = is_array($payload) ? ($payload['message'] ?? null) : null;
                    $orderId = is_array($payload) ? ($payload['order_id'] ?? null) : null;

                    DB::table('notifications')
                        ->where('id', $notification->id)
                        ->update([
                            'title' => $notification->title ?: match ($notification->type) {
                                'order', 'order_placed' => 'New order',
                                'order_status_updated' => 'Order status updated',
                                'product_added' => 'Product added',
                                'product_updated' => 'Product updated',
                                'product_low_stock', 'low_stock', 'stock' => 'Stock alert',
                                default => ucwords(str_replace('_', ' ', $notification->type)),
                            },
                            'description' => $notification->description ?: ($message ?: 'Notification received'),
                            'order_id' => $notification->order_id ?: $orderId,
                            'type' => match ($notification->type) {
                                'order', 'order_placed' => 'new_order',
                                default => $notification->type,
                            },
                        ]);
                });

            Schema::table('notifications', function (Blueprint $table) {
                $table->dropColumn('data');
            });
        }
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (! Schema::hasColumn('notifications', 'data')) {
                $table->text('data')->nullable();
            }
        });

        DB::table('notifications')
            ->orderBy('id')
            ->get()
            ->each(function ($notification) {
                DB::table('notifications')
                    ->where('id', $notification->id)
                    ->update([
                        'data' => json_encode([
                            'message' => $notification->description,
                            'order_id' => $notification->order_id,
                        ]),
                    ]);
            });

        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'order_id')) {
                $table->dropConstrainedForeignId('order_id');
            }

            if (Schema::hasColumn('notifications', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('notifications', 'title')) {
                $table->dropColumn('title');
            }
        });
    }
};
