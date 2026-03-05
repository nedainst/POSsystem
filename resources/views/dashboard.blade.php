@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Ringkasan bisnis Anda hari ini')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center">
                <i data-lucide="wallet" class="w-5 h-5 text-blue-600"></i>
            </div>
            <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">Hari Ini</span>
        </div>
        <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($todaySales, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-500 mt-1">Penjualan hari ini</p>
    </div>

    <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 bg-green-50 rounded-xl flex items-center justify-center">
                <i data-lucide="shopping-cart" class="w-5 h-5 text-green-600"></i>
            </div>
            <span class="text-xs font-medium text-green-600 bg-green-50 px-2.5 py-1 rounded-full">{{ $todayOrders }} order</span>
        </div>
        <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($monthlySales, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-500 mt-1">Penjualan bulan ini ({{ $monthlyOrders }} order)</p>
    </div>

    <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 bg-purple-50 rounded-xl flex items-center justify-center">
                <i data-lucide="package" class="w-5 h-5 text-purple-600"></i>
            </div>
            <span class="text-xs font-medium text-purple-600 bg-purple-50 px-2.5 py-1 rounded-full">{{ $totalProducts }} item</span>
        </div>
        <p class="text-2xl font-bold text-gray-800">{{ $totalProducts }}</p>
        <p class="text-xs text-gray-500 mt-1">Total produk aktif</p>
    </div>

    <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 bg-orange-50 rounded-xl flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-orange-600"></i>
            </div>
            @if($lowStockProducts > 0)
                <span class="text-xs font-medium text-orange-600 bg-orange-50 px-2.5 py-1 rounded-full pulse-dot">Perhatian!</span>
            @endif
        </div>
        <p class="text-2xl font-bold text-gray-800">{{ $lowStockProducts }}</p>
        <p class="text-xs text-gray-500 mt-1">Produk stok menipis</p>
    </div>
</div>

<!-- Charts & Recent -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Sales Chart -->
    <div class="lg:col-span-2 bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-800">Penjualan 7 Hari Terakhir</h3>
        </div>
        <canvas id="salesChart" height="120"></canvas>
    </div>

    <!-- Top Products -->
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <h3 class="text-sm font-semibold text-gray-800 mb-4">Produk Terlaris</h3>
        <div class="space-y-3">
            @forelse($topProducts as $i => $product)
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 bg-primary-50 text-primary-600 rounded-lg flex items-center justify-center text-xs font-bold">{{ $i + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-700 truncate">{{ $product->name }}</p>
                        <p class="text-xs text-gray-400">{{ $product->total_sold ?? 0 }} terjual</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-4">Belum ada data penjualan</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Recent Orders & Low Stock -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Orders -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800">Pesanan Terbaru</h3>
            <a href="{{ route('orders.index') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Lihat Semua</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($recentOrders as $order)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50 transition">
                    <div>
                        <p class="text-sm font-medium text-gray-700">{{ $order->invoice_number }}</p>
                        <p class="text-xs text-gray-400">{{ $order->created_at->diffForHumans() }} &bull; {{ $order->user->name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-800">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $order->status === 'completed' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                            {{ $order->status === 'completed' ? 'Selesai' : 'Dibatalkan' }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="px-5 py-8 text-center text-sm text-gray-400">Belum ada pesanan</div>
            @endforelse
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800">Peringatan Stok Menipis</h3>
            <a href="{{ route('inventory.index') }}?filter=low" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Lihat Semua</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($lowStockList as $item)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50 transition">
                    <div>
                        <p class="text-sm font-medium text-gray-700">{{ $item->name }}</p>
                        <p class="text-xs text-gray-400">{{ $item->category->name ?? '-' }} &bull; Min: {{ $item->min_stock }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold {{ $item->stock == 0 ? 'text-red-600' : 'text-orange-500' }}">{{ $item->stock }}</p>
                        <p class="text-xs text-gray-400">{{ $item->unit }}</p>
                    </div>
                </div>
            @empty
                <div class="px-5 py-8 text-center text-sm text-gray-400">
                    <i data-lucide="check-circle" class="w-8 h-8 mx-auto mb-2 text-green-400"></i>
                    Semua stok aman!
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const chartData = @json($chartData);

    new Chart(document.getElementById('salesChart'), {
        type: 'bar',
        data: {
            labels: chartData.map(d => d.date),
            datasets: [{
                label: 'Penjualan',
                data: chartData.map(d => d.sales),
                backgroundColor: 'rgba(99, 102, 241, 0.15)',
                borderColor: 'rgba(99, 102, 241, 0.8)',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => formatRupiah(ctx.parsed.y)
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: v => 'Rp ' + (v/1000) + 'k',
                        font: { size: 11 }
                    },
                    grid: { color: '#f1f5f9' }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
</script>
@endpush
