<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Notification;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $totalProducts = Product::count();
        $lowStock = Product::where('quantity', '<', 10)->count();
        $totalValue = Product::sum(\DB::raw('quantity * price'));

        $totalOrders = \App\Models\Order::count();
        $pendingOrders = \App\Models\Order::where('status', 'pending')->count();
        $completedOrders = \App\Models\Order::where('status', 'completed')->count();
        $recentProducts = Product::with('category')->latest()->take(5)->get();
        $recentOrders = \App\Models\Order::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'lowStock',
            'totalValue',
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'recentProducts',
            'recentOrders'
        ));
    }
}
