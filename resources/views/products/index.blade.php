@extends('layouts.app')

@section('title', 'Produk')
@section('page-title', 'Produk')
@section('page-description', 'Kelola semua produk toko Anda')

@section('content')
<div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 mb-6">
    <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
        <div class="relative">
            <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..."
                   class="pl-9 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm w-full sm:w-56 focus:ring-2 focus:ring-primary-500 outline-none">
        </div>
        <div class="flex gap-2">
            <select name="category" class="flex-1 sm:flex-none px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="stock_filter" class="flex-1 sm:flex-none px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm outline-none" onchange="this.form.submit()">
                <option value="">Semua Stok</option>
                <option value="low" {{ request('stock_filter') === 'low' ? 'selected' : '' }}>Stok Menipis</option>
                <option value="out" {{ request('stock_filter') === 'out' ? 'selected' : '' }}>Habis</option>
            </select>
        </div>
    </form>
    <a href="{{ route('products.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium flex items-center justify-center gap-2 transition shadow-lg shadow-primary-500/25">
        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Produk
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-4 sm:px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Produk</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Kategori</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">SKU</th>
                    <th class="px-4 sm:px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Harga</th>
                    <th class="px-4 sm:px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Stok</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Status</th>
                    <th class="px-4 sm:px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 sm:px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                                    @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}" class="w-full h-full object-cover">
                                    @else
                                        <i data-lucide="package" class="w-5 h-5 text-gray-400"></i>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-700 truncate">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-400 md:hidden">{{ $product->category->name }}</p>
                                    @if($product->barcode)
                                        <p class="text-xs text-gray-400 hidden sm:block">{{ $product->barcode }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 hidden md:table-cell">
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background-color: {{ $product->category->color }}15; color: {{ $product->category->color }}">
                                {{ $product->category->name }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-500 font-mono hidden lg:table-cell">{{ $product->sku }}</td>
                        <td class="px-4 sm:px-5 py-3 text-sm font-semibold text-gray-700 text-right whitespace-nowrap">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                        <td class="px-4 sm:px-5 py-3 text-center">
                            <span class="text-sm font-bold {{ $product->stock == 0 ? 'text-red-600' : ($product->isLowStock() ? 'text-orange-500' : 'text-gray-700') }}">
                                {{ $product->stock }}
                            </span>
                            <span class="text-xs text-gray-400">{{ $product->unit }}</span>
                        </td>
                        <td class="px-5 py-3 text-center hidden sm:table-cell">
                            @if($product->is_active)
                                <span class="text-xs bg-green-50 text-green-600 px-2.5 py-1 rounded-full font-medium">Aktif</span>
                            @else
                                <span class="text-xs bg-gray-50 text-gray-500 px-2.5 py-1 rounded-full font-medium">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 sm:px-5 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('products.show', $product) }}" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('products.edit', $product) }}" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                                <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="event.preventDefault(); confirmAction(this, 'Hapus Produk?', 'Produk ini akan dihapus secara permanen.', 'Ya, hapus!')">
                                    @csrf @method('DELETE')
                                    <button class="p-1.5 rounded-lg hover:bg-red-50 text-gray-400 hover:text-red-600 transition">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center">
                            <i data-lucide="package" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                            <p class="text-sm text-gray-400">Belum ada produk</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">{{ $products->links() }}</div>
@endsection
