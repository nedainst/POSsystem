@extends('layouts.app')

@section('title', isset($category) ? 'Edit Kategori' : 'Tambah Kategori')
@section('page-title', isset($category) ? 'Edit Kategori' : 'Tambah Kategori')
@section('page-description', isset($category) ? 'Ubah data kategori' : 'Buat kategori produk baru')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ isset($category) ? route('categories.update', $category) : route('categories.store') }}">
        @csrf
        @if(isset($category)) @method('PUT') @endif

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Kategori</label>
                <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                       placeholder="Contoh: Makanan, Minuman">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500 resize-none"
                          placeholder="Deskripsi kategori (opsional)">{{ old('description', $category->description ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Warna</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="color" value="{{ old('color', $category->color ?? '#6366f1') }}"
                               class="w-10 h-10 rounded-lg border border-gray-200 cursor-pointer">
                        <input type="text" value="{{ old('color', $category->color ?? '#6366f1') }}"
                               class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none bg-gray-50"
                               readonly>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Icon</label>
                    <select name="icon" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500">
                        @php
                            $icons = ['folder','package','coffee','utensils','shirt','smartphone','heart','star','zap','gift','book','music','camera','home','car','plane'];
                        @endphp
                        @foreach($icons as $icon)
                            <option value="{{ $icon }}" {{ old('icon', $category->icon ?? 'folder') === $icon ? 'selected' : '' }}>{{ ucfirst($icon) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span class="text-sm text-gray-700">Aktif</span>
                </label>
            </div>
        </div>

        <div class="flex items-center gap-3 mt-4">
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium transition shadow-lg shadow-primary-500/25">
                {{ isset($category) ? 'Simpan Perubahan' : 'Tambah Kategori' }}
            </button>
            <a href="{{ route('categories.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition">
                Batal
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.querySelector('input[type="color"]').addEventListener('input', function(e) {
        this.nextElementSibling.nextElementSibling.value = e.target.value;
    });
</script>
@endpush
@endsection
