<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $orders = Order::where('status', 'completed')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->get();

        $totalSales = $orders->sum('total');
        $totalOrders = $orders->count();
        $totalTax = $orders->sum('tax');
        $totalDiscount = $orders->sum('discount');
        $averageOrder = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        $dailySales = Order::where('status', 'completed')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw('DATE(created_at) as date, SUM(total) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $paymentMethods = Order::where('status', 'completed')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw('payment_method, SUM(total) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get();

        return view('reports.sales', compact(
            'startDate', 'endDate', 'totalSales', 'totalOrders',
            'totalTax', 'totalDiscount', 'averageOrder', 'dailySales', 'paymentMethods'
        ));
    }

    public function products(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $topProducts = OrderItem::whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'completed')
                  ->whereDate('created_at', '>=', $startDate)
                  ->whereDate('created_at', '<=', $endDate);
            })
            ->selectRaw('product_id, product_name, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_qty')
            ->limit(20)
            ->get();

        return view('reports.products', compact('startDate', 'endDate', 'topProducts'));
    }
}
