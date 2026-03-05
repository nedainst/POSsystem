@extends('layouts.app')

@section('title', 'Pesanan')
@section('page-title', 'Riwayat Pesanan')
@section('page-description', 'Semua transaksi penjualan')

@section('content')
<div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 mb-6">
    <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
        <div class="relative">
            <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari invoice..."
                   class="pl-9 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm w-full sm:w-52 focus:ring-2 focus:ring-primary-500 outline-none">
        </div>
        <div class="flex gap-2">
            <select name="status" class="flex-1 sm:flex-none px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm outline-none" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="flex-1 sm:flex-none px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm outline-none" onchange="this.form.submit()">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="flex-1 sm:flex-none px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm outline-none hidden sm:block" onchange="this.form.submit()">
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-4 sm:px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Invoice</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Pelanggan</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Kasir</th>
                    <th class="px-4 sm:px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Bayar</th>
                    <th class="px-4 sm:px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Tanggal</th>
                    <th class="px-4 sm:px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 sm:px-5 py-3">
                            <a href="{{ route('orders.show', $order) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">{{ $order->invoice_number }}</a>
                            <p class="text-xs text-gray-400 sm:hidden">{{ $order->created_at->format('d/m/Y') }}</p>
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-600 hidden lg:table-cell">{{ $order->customer->name ?? 'Umum' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-600 hidden md:table-cell">{{ $order->user->name }}</td>
                        <td class="px-4 sm:px-5 py-3 text-sm font-semibold text-gray-700 text-right whitespace-nowrap">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-sm text-gray-600 text-center hidden sm:table-cell">
                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100">{{ ucfirst($order->payment_method) }}</span>
                        </td>
                        <td class="px-4 sm:px-5 py-3 text-center">
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $order->status === 'completed' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                                {{ $order->status === 'completed' ? 'Selesai' : 'Batal' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500 text-center hidden sm:table-cell">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 sm:px-5 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('orders.show', $order) }}" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('pos.receipt', $order) }}" target="_blank" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition">
                                    <i data-lucide="printer" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">Belum ada pesanan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">{{ $orders->links() }}</div>
@endsection
