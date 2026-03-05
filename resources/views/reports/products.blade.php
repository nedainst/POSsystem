@extends('layouts.app')

@section('title', 'Laporan Produk')
@section('page-title', 'Laporan Produk')
@section('page-description', 'Analisis penjualan per produk')

@section('content')
<form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 mb-6">
    <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500">
    <span class="text-sm text-gray-400 hidden sm:block">sampai</span>
    <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500">
    <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition">
        <i data-lucide="filter" class="w-4 h-4 inline"></i> Filter
    </button>
</form>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Top 10 Produk Terlaris</h3>
            <canvas id="productChart" height="150"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">Ranking Produk</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($topProducts as $i => $product)
                <div class="px-5 py-3 flex items-center gap-3 hover:bg-gray-50 transition">
                    <span class="w-8 h-8 bg-primary-50 text-primary-600 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0">{{ $i + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-700 truncate">{{ $product->product_name }}</p>
                        <p class="text-xs text-gray-400">{{ $product->total_qty }} unit terjual</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-700">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</p>
                    </div>
                </div>
            @empty
                <div class="px-5 py-8 text-center text-sm text-gray-400">Belum ada data penjualan</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const products = @json($topProducts->take(10));

    new Chart(document.getElementById('productChart'), {
        type: 'bar',
        data: {
            labels: products.map(p => p.product_name.length > 15 ? p.product_name.substring(0, 15) + '...' : p.product_name),
            datasets: [{
                label: 'Qty Terjual',
                data: products.map(p => p.total_qty),
                backgroundColor: 'rgba(99, 102, 241, 0.2)',
                borderColor: 'rgba(99, 102, 241, 0.8)',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                y: { grid: { display: false }, ticks: { font: { size: 11 } } }
            }
        }
    });
</script>
@endpush
