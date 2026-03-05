@extends('layouts.app')

@section('title', 'Detail Produk')
@section('page-title', $product->name)
@section('page-description', $product->sku)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="aspect-square bg-gray-50 flex items-center justify-center">
                @if($product->image)
                    <img src="{{ Storage::url($product->image) }}" class="w-full h-full object-cover">
                @else
                    <i data-lucide="package" class="w-16 h-16 text-gray-300"></i>
                @endif
            </div>
            <div class="p-5">
                <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background-color: {{ $product->category->color }}15; color: {{ $product->category->color }}">{{ $product->category->name }}</span>
                <h2 class="text-lg font-bold text-gray-800 mt-2">{{ $product->name }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $product->description ?? 'Tidak ada deskripsi' }}</p>

                <div class="grid grid-cols-2 gap-3 mt-4">
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400">Harga Modal</p>
                        <p class="text-sm font-bold text-gray-700">Rp {{ number_format($product->cost_price, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-primary-50 rounded-xl p-3">
                        <p class="text-xs text-primary-400">Harga Jual</p>
                        <p class="text-sm font-bold text-primary-700">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400">Stok</p>
                        <p class="text-sm font-bold {{ $product->isLowStock() ? 'text-orange-500' : 'text-gray-700' }}">{{ $product->stock }} {{ $product->unit }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400">Profit</p>
                        <p class="text-sm font-bold text-green-600">Rp {{ number_format($product->getProfit(), 0, ',', '.') }}</p>
                    </div>
                </div>

                <a href="{{ route('products.edit', $product) }}" class="mt-4 w-full bg-primary-600 hover:bg-primary-700 text-white py-2.5 rounded-xl text-sm font-medium transition flex items-center justify-center gap-2">
                    <i data-lucide="edit-3" class="w-4 h-4"></i> Edit Produk
                </a>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-800">Riwayat Pergerakan Stok</h3>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($product->stockMovements as $movement)
                    <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center {{ $movement->type === 'in' ? 'bg-green-50' : ($movement->type === 'out' ? 'bg-red-50' : 'bg-blue-50') }}">
                                <i data-lucide="{{ $movement->type === 'in' ? 'arrow-down' : ($movement->type === 'out' ? 'arrow-up' : 'refresh-cw') }}"
                                   class="w-4 h-4 {{ $movement->type === 'in' ? 'text-green-600' : ($movement->type === 'out' ? 'text-red-600' : 'text-blue-600') }}"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">
                                    {{ $movement->type === 'in' ? 'Masuk' : ($movement->type === 'out' ? 'Keluar' : 'Penyesuaian') }}
                                    <span class="text-gray-400 font-normal">{{ $movement->quantity }} {{ $product->unit }}</span>
                                </p>
                                <p class="text-xs text-gray-400">{{ $movement->reference }} &bull; {{ $movement->user->name }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">{{ $movement->stock_before }} → {{ $movement->stock_after }}</p>
                            <p class="text-xs text-gray-400">{{ $movement->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-400">Belum ada riwayat stok</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
