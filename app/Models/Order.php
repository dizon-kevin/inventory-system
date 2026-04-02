<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'status',
        'payment_method',
        'payment_status',
        'payment_amount',
        'xendit_invoice_id',
        'xendit_invoice_url',
        'xendit_payment_method',
        'xendit_reference_id',
        'notes',
        'pickup_address',
        'delivery_address',
        'placed_at',
        'approved_at',
        'completed_at',
        'payment_paid_at',
        'payment_expires_at',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'payment_amount' => 'decimal:2',
        'pickup_address' => 'array',
        'delivery_address' => 'array',
        'placed_at' => 'datetime',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'payment_paid_at' => 'datetime',
        'payment_expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
