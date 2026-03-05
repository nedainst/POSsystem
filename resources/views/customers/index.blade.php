@extends('layouts.app')

@section('title', 'Pelanggan')
@section('page-title', 'Pelanggan')
@section('page-description', 'Kelola data pelanggan toko')

@section('content')
<div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 mb-6">
    <form method="GET" class="relative">
        <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pelanggan..."
               class="pl-9 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm w-full sm:w-64 focus:ring-2 focus:ring-primary-500 outline-none">
    </form>
    <button onclick="document.getElementById('addCustomerModal').classList.remove('hidden')"
            class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium flex items-center justify-center gap-2 transition shadow-lg shadow-primary-500/25">
        <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah Pelanggan
    </button>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-4 sm:px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Telepon</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Email</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Pesanan</th>
                    <th class="px-4 sm:px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total Belanja</th>
                    <th class="px-4 sm:px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($customers as $customer)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 sm:px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-primary-50 rounded-full flex items-center justify-center text-sm font-bold text-primary-600 flex-shrink-0">
                                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <span class="text-sm font-medium text-gray-700 block truncate">{{ $customer->name }}</span>
                                    <span class="text-xs text-gray-400 sm:hidden">{{ $customer->phone ?? '-' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-500 hidden sm:table-cell">{{ $customer->phone ?? '-' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500 hidden md:table-cell">{{ $customer->email ?? '-' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-600 text-center hidden lg:table-cell">{{ $customer->orders_count }}</td>
                        <td class="px-4 sm:px-5 py-3 text-sm font-semibold text-gray-700 text-right whitespace-nowrap">Rp {{ number_format($customer->total_purchases, 0, ',', '.') }}</td>
                        <td class="px-4 sm:px-5 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="editCustomer({{ json_encode($customer) }})" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </button>
                                <form method="POST" action="{{ route('customers.destroy', $customer) }}" onsubmit="event.preventDefault(); confirmAction(this, 'Hapus Pelanggan?', 'Data pelanggan ini akan dihapus secara permanen.', 'Ya, hapus!')">
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
                        <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">Belum ada pelanggan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">{{ $customers->links() }}</div>

<!-- Add/Edit Customer Modal -->
<div id="addCustomerModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-800 mb-4" id="customerModalTitle">Tambah Pelanggan</h3>
        <form id="customerForm" method="POST" action="{{ route('customers.store') }}" class="space-y-3">
            @csrf
            <div id="customerMethodField"></div>
            <input type="text" name="name" id="custName" required placeholder="Nama Pelanggan *"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500">
            <input type="text" name="phone" id="custPhone" placeholder="No. Telepon"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500">
            <input type="email" name="email" id="custEmail" placeholder="Email"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500">
            <textarea name="address" id="custAddress" rows="2" placeholder="Alamat (opsional)"
                      class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('addCustomerModal').classList.add('hidden')"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2.5 rounded-xl text-sm font-medium transition">Batal</button>
                <button type="submit" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white py-2.5 rounded-xl text-sm font-medium transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function editCustomer(customer) {
        document.getElementById('customerModalTitle').textContent = 'Edit Pelanggan';
        document.getElementById('customerForm').action = `/customers/${customer.id}`;
        document.getElementById('customerMethodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('custName').value = customer.name;
        document.getElementById('custPhone').value = customer.phone || '';
        document.getElementById('custEmail').value = customer.email || '';
        document.getElementById('custAddress').value = customer.address || '';
        document.getElementById('addCustomerModal').classList.remove('hidden');
    }
</script>
@endpush
@endsection
