@extends('layouts.app')

@section('title', 'Riwayat Stok')
@section('page-title', 'Riwayat Pergerakan Stok')
@section('page-description', 'Semua pergerakan stok barang')

@section('content')
<div class="flex flex-wrap items-center gap-2 mb-6">
    <form method="GET" class="flex flex-wrap items-center gap-2">
        <select name="product_id" class="px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm outline-none" onchange="this.form.submit()">
            <option value="">Semua Produk</option>
            @foreach($products as $p)
                <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
            @endforeach
        </select>
        <select name="type" class="px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm outline-none" onchange="this.form.submit()">
            <option value="">Semua Tipe</option>
            <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Masuk</option>
            <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Keluar</option>
            <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Penyesuaian</option>
        </select>
    </form>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Produk</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Tipe</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Qty</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Stok Sebelum</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Stok Sesudah</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ref</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">User</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($movements as $mv)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $mv->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-3 text-sm font-medium text-gray-700">{{ $mv->product->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $mv->type === 'in' ? 'bg-green-50 text-green-600' : ($mv->type === 'out' ? 'bg-red-50 text-red-600' : 'bg-blue-50 text-blue-600') }}">
                                {{ $mv->type === 'in' ? 'Masuk' : ($mv->type === 'out' ? 'Keluar' : 'Sesuaikan') }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center text-sm font-bold {{ $mv->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $mv->type === 'in' ? '+' : '-' }}{{ $mv->quantity }}
                        </td>
                        <td class="px-5 py-3 text-center text-sm text-gray-500">{{ $mv->stock_before }}</td>
                        <td class="px-5 py-3 text-center text-sm font-medium text-gray-700">{{ $mv->stock_after }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $mv->reference ?? '-' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $mv->user->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">Belum ada riwayat pergerakan stok</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">{{ $movements->links() }}</div>
@endsection
