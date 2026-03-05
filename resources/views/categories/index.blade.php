@extends('layouts.app')

@section('title', 'Kategori')
@section('page-title', 'Kategori Produk')
@section('page-description', 'Kelola kategori produk toko Anda')

@section('content')
<div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 mb-6">
    <form method="GET" class="relative">
        <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kategori..."
               class="pl-9 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm w-full sm:w-64 focus:ring-2 focus:ring-primary-500 outline-none">
    </form>
    <a href="{{ route('categories.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium flex items-center justify-center gap-2 transition shadow-lg shadow-primary-500/25">
        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Kategori
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @forelse($categories as $category)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden stat-card">
            <div class="h-2" style="background-color: {{ $category->color }}"></div>
            <div class="p-5">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background-color: {{ $category->color }}20">
                        <i data-lucide="{{ $category->icon }}" class="w-5 h-5" style="color: {{ $category->color }}"></i>
                    </div>
                    <div class="flex items-center gap-1">
                        @if($category->is_active)
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        @else
                            <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
                        @endif
                    </div>
                </div>
                <h3 class="text-base font-bold text-gray-800">{{ $category->name }}</h3>
                <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ $category->description ?? 'Tidak ada deskripsi' }}</p>
                <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-50">
                    <span class="text-xs text-gray-500">{{ $category->products_count }} produk</span>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('categories.edit', $category) }}" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </a>
                        <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="event.preventDefault(); confirmAction(this, 'Hapus Kategori?', 'Kategori ini akan dihapus secara permanen.', 'Ya, hapus!')">
                            @csrf @method('DELETE')
                            <button class="p-1.5 rounded-lg hover:bg-red-50 text-gray-400 hover:text-red-600 transition">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-12">
            <i data-lucide="grid-3x3" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
            <p class="text-sm text-gray-400">Belum ada kategori</p>
            <a href="{{ route('categories.create') }}" class="inline-block mt-3 text-sm text-primary-600 font-medium hover:text-primary-700">+ Tambah Kategori Pertama</a>
        </div>
    @endforelse
</div>

<div class="mt-6">{{ $categories->links() }}</div>
@endsection
