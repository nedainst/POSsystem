@extends('layouts.app')

@section('title', 'Detail Pesanan')
@section('page-title', 'Detail Pesanan')
@section('page-description', $order->invoice_number)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 sm:px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-800">Item Pesanan</h3>
                <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $order->status === 'completed' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                    {{ $order->status === 'completed' ? 'Selesai' : 'Batal' }}
                </span>
            </div>
            <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="bg-gray-50"><th class="px-4 sm:px-5 py-2 text-left text-xs font-semibold text-gray-500">Produk</th><th class="px-4 sm:px-5 py-2 text-right text-xs font-semibold text-gray-500">Harga</th><th class="px-4 sm:px-5 py-2 text-center text-xs font-semibold text-gray-500">Qty</th><th class="px-4 sm:px-5 py-2 text-right text-xs font-semibold text-gray-500">Subtotal</th></tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="px-4 sm:px-5 py-3 text-sm font-medium text-gray-700">{{ $item->product_name }}</td>
                            <td class="px-4 sm:px-5 py-3 text-sm text-gray-600 text-right whitespace-nowrap">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="px-4 sm:px-5 py-3 text-sm text-gray-600 text-center">{{ $item->quantity }}</td>
                            <td class="px-4 sm:px-5 py-3 text-sm font-semibold text-gray-700 text-right whitespace-nowrap">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
            <h3 class="text-sm font-semibold text-gray-800 mb-2">Ringkasan</h3>
            <div class="flex justify-between text-sm"><span class="text-gray-500">Subtotal</span><span class="font-medium">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-gray-500">Diskon</span><span class="font-medium text-red-500">-Rp {{ number_format($order->discount, 0, ',', '.') }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-gray-500">Pajak</span><span class="font-medium">Rp {{ number_format($order->tax, 0, ',', '.') }}</span></div>
            <div class="border-t border-gray-100 pt-2 flex justify-between"><span class="font-bold text-gray-800">Total</span><span class="text-lg font-bold text-primary-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-gray-500">Dibayar</span><span class="font-medium">Rp {{ number_format($order->paid, 0, ',', '.') }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-gray-500">Kembalian</span><span class="font-medium text-green-600">Rp {{ number_format($order->change, 0, ',', '.') }}</span></div>

            <div class="border-t border-gray-100 pt-3 space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Pembayaran</span><span class="font-medium">{{ ucfirst($order->payment_method) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Kasir</span><span class="font-medium">{{ $order->user->name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Pelanggan</span><span class="font-medium">{{ $order->customer->name ?? 'Umum' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Tanggal</span><span class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</span></div>
            </div>

            <div class="flex gap-2 pt-2">
                <a href="{{ route('pos.receipt', $order) }}" target="_blank" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2.5 rounded-xl text-sm font-medium text-center transition flex items-center justify-center gap-1">
                    <i data-lucide="printer" class="w-4 h-4"></i> Cetak
                </a>
                @if($order->status === 'completed')
                    <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="event.preventDefault(); confirmAction(this, 'Batalkan Pesanan?', 'Pesanan ini akan dibatalkan dan stok akan dikembalikan.', 'Ya, batalkan!', 'warning')" class="flex-1">
                        @csrf
                        <button class="w-full bg-red-50 hover:bg-red-100 text-red-600 py-2.5 rounded-xl text-sm font-medium transition flex items-center justify-center gap-1">
                            <i data-lucide="x-circle" class="w-4 h-4"></i> Batalkan
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
