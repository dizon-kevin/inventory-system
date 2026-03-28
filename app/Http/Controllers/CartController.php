<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::with('product.category')
            ->where('user_id', auth()->id())
            ->get();

        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        return view('user.cart', compact('cartItems', 'total'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($data['product_id']);

        if ($product->quantity <= 0) {
            return back()->with('error', 'Product is out of stock.');
        }

        $orderQuantity = min($data['quantity'], $product->quantity);

        $cart = Cart::firstOrNew([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
        ]);

        $cart->quantity = min($product->quantity, $cart->quantity + $orderQuantity);
        $cart->save();

        return back()->with('success', 'Product added to cart.');
    }

    public function update(Request $request, Cart $cart)
    {
        abort_unless($cart->user_id === auth()->id(), 403);

        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $product = $cart->product;

        if ($data['quantity'] > $product->quantity) {
            return back()->with('error', 'Cannot set quantity higher than available stock.');
        }

        $cart->quantity = $data['quantity'];
        $cart->save();

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(Cart $cart)
    {
        abort_unless($cart->user_id === auth()->id(), 403);

        $cart->delete();

        return back()->with('success', 'Item removed from cart.');
    }
}
