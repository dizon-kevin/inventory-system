<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            return; // No categories to assign
        }

        $products = [
            [
                'name' => 'Wireless Headphones',
                'sku' => 'WH-001',
                'category_id' => $categories->where('name', 'Electronics')->first()?->id ?? $categories->first()->id,
                'quantity' => 25,
                'price' => 99.99,
            ],
            [
                'name' => 'Cotton T-Shirt',
                'sku' => 'TS-001',
                'category_id' => $categories->where('name', 'Clothing')->first()?->id ?? $categories->first()->id,
                'quantity' => 50,
                'price' => 19.99,
            ],
            [
                'name' => 'Programming Book',
                'sku' => 'BK-001',
                'category_id' => $categories->where('name', 'Books')->first()?->id ?? $categories->first()->id,
                'quantity' => 8,
                'price' => 49.99,
            ],
            [
                'name' => 'Garden Hose',
                'sku' => 'GH-001',
                'category_id' => $categories->where('name', 'Home & Garden')->first()?->id ?? $categories->first()->id,
                'quantity' => 15,
                'price' => 29.99,
            ],
            [
                'name' => 'Basketball',
                'sku' => 'BB-001',
                'category_id' => $categories->where('name', 'Sports')->first()?->id ?? $categories->first()->id,
                'quantity' => 3,
                'price' => 39.99,
            ],
            [
                'name' => 'Smartphone Case',
                'sku' => 'SC-001',
                'category_id' => $categories->where('name', 'Electronics')->first()?->id ?? $categories->first()->id,
                'quantity' => 100,
                'price' => 14.99,
            ],
            [
                'name' => 'Jeans',
                'sku' => 'JN-001',
                'category_id' => $categories->where('name', 'Clothing')->first()?->id ?? $categories->first()->id,
                'quantity' => 30,
                'price' => 59.99,
            ],
            [
                'name' => 'Cookbook',
                'sku' => 'CB-001',
                'category_id' => $categories->where('name', 'Books')->first()?->id ?? $categories->first()->id,
                'quantity' => 12,
                'price' => 24.99,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(['sku' => $product['sku']], $product);
        }
    }
}
