<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category')
            ->when(request('search'), fn($q) => $q->where('name', 'like', '%'.request('search').'%')
                ->orWhere('sku', 'like', '%'.request('search').'%'))
            ->when(request('category_id'), fn($q) => $q->where('category_id', request('category_id')))
            ->paginate(10);

        $categories = Category::all();
        if (request()->routeIs('user.products.index')) {
            return view('user.products.index', compact('products', 'categories'));
        }

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products',
            'category_id' => 'required|exists:categories,id',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
        ]);

        $data = $request->except('image');
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        event(new \App\Events\ProductAdded($product));

        if ($product->quantity < 10) {
            event(new \App\Events\ProductLowStock($product));
        }

        // Create notifications for admins
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'type' => 'product_added',
                'title' => 'Product added',
                'description' => 'New product added: ' . $product->name,
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        if (request()->routeIs('user.products.show')) {
            return view('user.products.show', compact('product'));
        }

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => ['required', 'string', 'max:255', Rule::unique('products')->ignore($product->id)],
            'category_id' => 'required|exists:categories,id',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
        ]);

        $data = $request->except('image');
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && \Storage::disk('public')->exists($product->image)) {
                \Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        event(new \App\Events\ProductUpdated($product));

        if ($product->quantity < 10) {
            event(new \App\Events\ProductLowStock($product));
        }

        // Create notifications for admins
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'type' => 'product_updated',
                'title' => 'Product updated',
                'description' => 'Product updated: ' . $product->name,
            ]);
            if ($product->quantity < 10) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'product_low_stock',
                    'title' => 'Stock alert',
                    'description' => 'Product low stock: ' . $product->name,
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Delete image if exists
        if ($product->image && \Storage::disk('public')->exists($product->image)) {
            \Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
    }
}
