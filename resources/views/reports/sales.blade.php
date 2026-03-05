@extends('layouts.app')

@section('title', 'Laporan Penjualan')
@section('page-title', 'Laporan Penjualan')
@section('page-description', 'Analisis penjualan toko')

@section('content')
<!-- Date Filter -->
<form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 mb-6">
    <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500">
    <span class="text-sm text-gray-400 hidden sm:block">sampai</span>
    <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500">
    <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition">
        <i data-lucide="filter" class="w-4 h-4 inline"></i> Filter
    </button>
</form>

<!-- Stats -->
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-6">
    <div class="stat-card bg-white rounded-2xl p-3 sm:p-4 border border-gray-100 shadow-sm">
        <p class="text-xs text-gray-500 mb-1">Total Penjualan</p>
        <p class="text-base sm:text-xl font-bold text-gray-800">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
    </div>
    <div class="stat-card bg-white rounded-2xl p-3 sm:p-4 border border-gray-100 shadow-sm">
        <p class="text-xs text-gray-500 mb-1">Jumlah Transaksi</p>
        <p class="text-base sm:text-xl font-bold text-gray-800">{{ $totalOrders }}</p>
    </div>
    <div class="stat-card bg-white rounded-2xl p-3 sm:p-4 border border-gray-100 shadow-sm">
        <p class="text-xs text-gray-500 mb-1">Rata-rata</p>
        <p class="text-base sm:text-xl font-bold text-gray-800">Rp {{ number_format($averageOrder, 0, ',', '.') }}</p>
    </div>
    <div class="stat-card bg-white rounded-2xl p-3 sm:p-4 border border-gray-100 shadow-sm">
        <p class="text-xs text-gray-500 mb-1">Total Pajak</p>
        <p class="text-base sm:text-xl font-bold text-gray-800">Rp {{ number_format($totalTax, 0, ',', '.') }}</p>
    </div>
    <div class="stat-card bg-white rounded-2xl p-3 sm:p-4 border border-gray-100 shadow-sm col-span-2 sm:col-span-1">
        <p class="text-xs text-gray-500 mb-1">Total Diskon</p>
        <p class="text-base sm:text-xl font-bold text-red-500">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Daily Sales Chart -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-800 mb-4">Penjualan Harian</h3>
        <canvas id="dailySalesChart" height="120"></canvas>
    </div>

    <!-- Payment Methods -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-800 mb-4">Metode Pembayaran</h3>
        <canvas id="paymentChart" height="200"></canvas>
        <div class="mt-4 space-y-2">
            @foreach($paymentMethods as $pm)
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">{{ ucfirst($pm->payment_method) }}</span>
                    <div class="text-right">
                        <span class="font-bold text-gray-700">Rp {{ number_format($pm->total, 0, ',', '.') }}</span>
                        <span class="text-xs text-gray-400 block">{{ $pm->count }} transaksi</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Daily Sales Table -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-4 sm:px-5 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-800">Detail Penjualan Harian</h3>
    </div>
    <div class="overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="bg-gray-50"><th class="px-5 py-2 text-left text-xs font-semibold text-gray-500">Tanggal</th><th class="px-5 py-2 text-center text-xs font-semibold text-gray-500">Transaksi</th><th class="px-5 py-2 text-right text-xs font-semibold text-gray-500">Total</th></tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($dailySales as $day)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 text-sm text-gray-600">{{ \Carbon\Carbon::parse($day->date)->format('d M Y') }}</td>
                    <td class="px-5 py-3 text-sm text-gray-600 text-center">{{ $day->count }}</td>
                    <td class="px-5 py-3 text-sm font-semibold text-gray-700 text-right">Rp {{ number_format($day->total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const dailyData = @json($dailySales);
    const paymentData = @json($paymentMethods);

    new Chart(document.getElementById('dailySalesChart'), {
        type: 'line',
        data: {
            labels: dailyData.map(d => d.date),
            datasets: [{
                label: 'Penjualan',
                data: dailyData.map(d => d.total),
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99,102,241,0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 4,
                pointBackgroundColor: '#6366f1',
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: v => 'Rp ' + (v/1000) + 'k', font: { size: 11 } },
                    grid: { color: '#f1f5f9' }
                },
                x: { grid: { display: false }, ticks: { font: { size: 11 } } }
            }
        }
    });

    const colors = ['#6366f1', '#10b981', '#f59e0b', '#ef4444'];
    new Chart(document.getElementById('paymentChart'), {
        type: 'doughnut',
        data: {
            labels: paymentData.map(d => d.payment_method),
            datasets: [{
                data: paymentData.map(d => d.total),
                backgroundColor: colors.slice(0, paymentData.length),
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } },
            cutout: '65%',
        }
    });
</script>
@endpush
