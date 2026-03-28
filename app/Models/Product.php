<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'sku', 'category_id', 'quantity', 'price', 'image', 'description'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Helper for stock status
    public function getStockStatusAttribute()
    {
        if ($this->quantity <= 0) return 'Out of Stock';
        if ($this->quantity < 10) return 'Low Stock'; // threshold = 10
        return 'In Stock';
    }
}