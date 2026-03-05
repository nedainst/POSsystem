@extends('layouts.app')

@section('title', 'Inventaris')
@section('page-title', 'Stok Barang')
@section('page-description', 'Kelola stok dan inventaris produk')

@section('content')
<!-- Stats -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                <i data-lucide="package" class="w-5 h-5 text-blue-600"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800">{{ $totalProducts }}</p>
                <p class="text-xs text-gray-500">Total Produk</p>
            </div>
        </div>
    </div>
    <div class="stat-card bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-orange-600"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-orange-500">{{ $lowStock }}</p>
                <p class="text-xs text-gray-500">Stok Menipis</p>
            </div>
        </div>
    </div>
    <div class="stat-card bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center">
                <i data-lucide="x-circle" class="w-5 h-5 text-red-600"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-red-600">{{ $outOfStock }}</p>
                <p class="text-xs text-gray-500">Stok Habis</p>
            </div>
        </div>
    </div>
    <div class="stat-card bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center">
                <i data-lucide="coins" class="w-5 h-5 text-green-600"></i>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800">Rp {{ number_format($totalValue, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500">Nilai Inventaris</p>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Search -->
<div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 mb-6">
    <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
        <div class="relative">
            <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..."
                   class="pl-9 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm w-full sm:w-56 focus:ring-2 focus:ring-primary-500 outline-none">
        </div>
        <div class="flex gap-1 overflow-x-auto">
            <a href="{{ route('inventory.index') }}" class="flex-shrink-0 px-3 py-2.5 rounded-xl text-xs sm:text-sm font-medium {{ !request('filter') ? 'bg-primary-600 text-white' : 'bg-white text-gray-600 border border-gray-200' }} transition">Semua</a>
            <a href="{{ route('inventory.index', ['filter' => 'low']) }}" class="flex-shrink-0 px-3 py-2.5 rounded-xl text-xs sm:text-sm font-medium {{ request('filter') === 'low' ? 'bg-orange-500 text-white' : 'bg-white text-gray-600 border border-gray-200' }} transition">Menipis</a>
            <a href="{{ route('inventory.index', ['filter' => 'out']) }}" class="flex-shrink-0 px-3 py-2.5 rounded-xl text-xs sm:text-sm font-medium {{ request('filter') === 'out' ? 'bg-red-500 text-white' : 'bg-white text-gray-600 border border-gray-200' }} transition">Habis</a>
            <a href="{{ route('inventory.index', ['filter' => 'normal']) }}" class="flex-shrink-0 px-3 py-2.5 rounded-xl text-xs sm:text-sm font-medium {{ request('filter') === 'normal' ? 'bg-green-500 text-white' : 'bg-white text-gray-600 border border-gray-200' }} transition">Normal</a>
        </div>
    </form>
</div>

<!-- Product Table -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-4 sm:px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Produk</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Kategori</th>
                    <th class="px-4 sm:px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Stok</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Min. Stok</th>
                    <th class="px-4 sm:px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Nilai</th>
                    <th class="px-4 sm:px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 sm:px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0">
                                    @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}" class="w-full h-full object-cover">
                                    @else
                                        <i data-lucide="package" class="w-5 h-5 text-gray-400"></i>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-700 truncate">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-400 md:hidden">{{ $product->category->name }}</p>
                                    <p class="text-xs text-gray-400 font-mono hidden md:block">{{ $product->sku }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-600 hidden md:table-cell">{{ $product->category->name }}</td>
                        <td class="px-4 sm:px-5 py-3 text-center">
                            <span class="text-lg font-bold {{ $product->stock == 0 ? 'text-red-600' : ($product->isLowStock() ? 'text-orange-500' : 'text-gray-700') }}">
                                {{ $product->stock }}
                            </span>
                            <span class="text-xs text-gray-400 block">{{ $product->unit }}</span>
                        </td>
                        <td class="px-5 py-3 text-center text-sm text-gray-500 hidden sm:table-cell">{{ $product->min_stock }}</td>
                        <td class="px-4 sm:px-5 py-3 text-center">
                            @if($product->stock == 0)
                                <span class="text-xs bg-red-50 text-red-600 px-2.5 py-1 rounded-full font-medium">Habis</span>
                            @elseif($product->isLowStock())
                                <span class="text-xs bg-orange-50 text-orange-600 px-2.5 py-1 rounded-full font-medium">Menipis</span>
                            @else
                                <span class="text-xs bg-green-50 text-green-600 px-2.5 py-1 rounded-full font-medium">Normal</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-600 text-right hidden lg:table-cell">Rp {{ number_format($product->stock * $product->cost_price, 0, ',', '.') }}</td>
                        <td class="px-4 sm:px-5 py-3 text-center">
                            <button onclick="showAdjustModal({{ $product->id }}, '{{ $product->name }}', {{ $product->stock }})"
                                    class="p-2 rounded-lg bg-primary-50 text-primary-600 hover:bg-primary-100 transition" title="Sesuaikan Stok">
                                <i data-lucide="settings-2" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-sm text-gray-400">Tidak ada produk</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">{{ $products->links() }}</div>

<!-- Adjust Stock Modal -->
<div id="adjustModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
    <div class="bg-white rounded-2xl p-6 max-w-sm w-full mx-4">
        <h3 class="text-lg font-bold text-gray-800 mb-1">Sesuaikan Stok</h3>
        <p id="adjustProductName" class="text-sm text-gray-500 mb-4"></p>
        <p class="text-xs text-gray-400 mb-3">Stok saat ini: <span id="adjustCurrentStock" class="font-bold text-gray-700"></span></p>

        <div class="space-y-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipe</label>
                <select id="adjustType" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="in">Stok Masuk (+)</option>
                    <option value="out">Stok Keluar (-)</option>
                    <option value="adjustment">Set Stok Baru</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah</label>
                <input type="number" id="adjustQuantity" min="1" value="1" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan</label>
                <input type="text" id="adjustNotes" placeholder="Alasan perubahan (opsional)" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500">
            </div>
        </div>

        <div class="flex gap-2 mt-5">
            <button onclick="closeAdjustModal()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2.5 rounded-xl text-sm font-medium transition">Batal</button>
            <button onclick="submitAdjust()" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white py-2.5 rounded-xl text-sm font-medium transition">Simpan</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let adjustProductId = null;

    function showAdjustModal(id, name, stock) {
        adjustProductId = id;
        document.getElementById('adjustProductName').textContent = name;
        document.getElementById('adjustCurrentStock').textContent = stock;
        document.getElementById('adjustModal').classList.remove('hidden');
    }

    function closeAdjustModal() {
        document.getElementById('adjustModal').classList.add('hidden');
    }

    async function submitAdjust() {
        const type = document.getElementById('adjustType').value;
        const quantity = parseInt(document.getElementById('adjustQuantity').value);
        const notes = document.getElementById('adjustNotes').value;

        if (!quantity || quantity < 1) { alert('Jumlah harus minimal 1!'); return; }

        try {
            const res = await fetch(`/inventory/${adjustProductId}/adjust`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ type, quantity, notes })
            });

            const data = await res.json();
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message || 'Gagal menyesuaikan stok!');
            }
        } catch (e) {
            alert('Terjadi kesalahan!');
        }
    }
</script>
@endpush
@endsection
