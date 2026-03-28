<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function index(): View
    {
        $products = Product::with('category')->paginate(10);

        return view('user.dashboard', compact('products'));
    }
}
