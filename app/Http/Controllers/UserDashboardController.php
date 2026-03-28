<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Notification;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function index(): View
    {
        $products = Product::with('category')->paginate(10);
        $notifications = Notification::where('user_id', auth()->id())->whereNull('read_at')->get();

        return view('user.dashboard', compact('products', 'notifications'));
    }

    public function notifications(): View
    {
        $notifications = Notification::where('user_id', auth()->id())->latest()->paginate(15);

        return view('user.notifications.index', compact('notifications'));
    }

    public function markNotificationsRead(): \Illuminate\Http\RedirectResponse
    {
        Notification::where('user_id', auth()->id())->whereNull('read_at')->update(['read_at' => now()]);

        return back();
    }
}

