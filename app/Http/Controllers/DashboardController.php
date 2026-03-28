<?php
namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function reports(): View
    {
        $totalValue = Product::sum(\DB::raw('quantity * price'));
        $lowStockProducts = Product::where('quantity', '<', 10)->get();

        return view('reports', compact('totalValue', 'lowStockProducts'));
    }
}