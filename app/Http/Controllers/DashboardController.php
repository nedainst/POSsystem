<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        $todaySales = Order::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('total');

        $todayOrders = Order::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->count();

        $monthlySales = Order::whereBetween('created_at', [$startOfMonth, Carbon::now()])
            ->where('status', 'completed')
            ->sum('total');

        $monthlyOrders = Order::whereBetween('created_at', [$startOfMonth, Carbon::now()])
            ->where('status', 'completed')
            ->count();

        $totalProducts = Product::where('is_active', true)->count();
        $lowStockProducts = Product::where('is_active', true)
            ->whereColumn('stock', '<=', 'min_stock')
            ->count();

        $totalCustomers = Customer::count();

        // Last 7 days chart data
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartData[] = [
                'date' => $date->format('d M'),
                'sales' => Order::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->sum('total'),
                'orders' => Order::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->count(),
            ];
        }

        $recentOrders = Order::with(['customer', 'user'])
            ->latest()
            ->limit(10)
            ->get();

        $topProducts = Product::withCount(['orderItems as total_sold' => function ($query) {
                $query->select(\DB::raw('COALESCE(SUM(quantity), 0)'));
            }])
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $lowStockList = Product::where('is_active', true)
            ->whereColumn('stock', '<=', 'min_stock')
            ->with('category')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'todaySales', 'todayOrders', 'monthlySales', 'monthlyOrders',
            'totalProducts', 'lowStockProducts', 'totalCustomers',
            'chartData', 'recentOrders', 'topProducts', 'lowStockList'
        ));
    }
}
