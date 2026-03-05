@extends('layouts.app')

@section('title', isset($product) ? 'Edit Produk' : 'Tambah Produk')
@section('page-title', isset($product) ? 'Edit Produk' : 'Tambah Produk')

@section('content')
<div class="max-w-3xl">
    <form method="POST" action="{{ isset($product) ? route('products.update', $product) : route('products.store') }}" enctype="multipart/form-data">
        @csrf
        @if(isset($product)) @method('PUT') @endif

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4 text-primary-500"></i> Informasi Produk
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Produk *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500"
                           placeholder="Nama produk">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori *</label>
                    <select name="category_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">SKU *</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}" required
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500"
                           placeholder="PRD-001">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Barcode</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $product->barcode ?? '') }}"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500"
                           placeholder="Kode barcode (opsional)">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Unit</label>
                    <select name="unit" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500">
                        @php $units = ['pcs', 'kg', 'liter', 'meter', 'box', 'pack', 'lusin', 'botol', 'sachet']; @endphp
                        @foreach($units as $unit)
                            <option value="{{ $unit }}" {{ old('unit', $product->unit ?? 'pcs') === $unit ? 'selected' : '' }}>{{ ucfirst($unit) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                    <textarea name="description" rows="3"
                              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500 resize-none"
                              placeholder="Deskripsi produk (opsional)">{{ old('description', $product->description ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mt-4">
            <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i data-lucide="dollar-sign" class="w-4 h-4 text-primary-500"></i> Harga & Stok
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Harga Modal *</label>
                    <input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price ?? 0) }}" required min="0" step="100"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500"
                           placeholder="0">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Harga Jual *</label>
                    <input type="number" name="selling_price" value="{{ old('selling_price', $product->selling_price ?? 0) }}" required min="0" step="100"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500"
                           placeholder="0">
                </div>

                @if(!isset($product))
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Stok Awal *</label>
                    <input type="number" name="stock" value="{{ old('stock', 0) }}" required min="0"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500"
                           placeholder="0">
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Minimum Stok *</label>
                    <input type="number" name="min_stock" value="{{ old('min_stock', $product->min_stock ?? 5) }}" required min="0"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500"
                           placeholder="5">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mt-4">
            <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i data-lucide="image" class="w-4 h-4 text-primary-500"></i> Gambar & Status
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Gambar Produk</label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-600 hover:file:bg-primary-100">
                    @if(isset($product) && $product->image)
                        <div class="mt-2">
                            <img src="{{ Storage::url($product->image) }}" class="w-20 h-20 rounded-xl object-cover">
                        </div>
                    @endif
                </div>

                <div class="flex items-end">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-700">Produk Aktif</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 mt-4">
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium transition shadow-lg shadow-primary-500/25">
                {{ isset($product) ? 'Simpan Perubahan' : 'Tambah Produk' }}
            </button>
            <a href="{{ route('products.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
