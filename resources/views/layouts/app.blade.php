<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'POS System') - {{ App\Models\SiteSetting::get('site_name', 'NedaPOS') }}</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '{{ App\Models\SiteSetting::get("color_primary_50", "#eef2ff") }}',
                            100: '{{ App\Models\SiteSetting::get("color_primary_100", "#e0e7ff") }}',
                            200: '{{ App\Models\SiteSetting::get("color_primary_200", "#c7d2fe") }}',
                            300: '{{ App\Models\SiteSetting::get("color_primary_300", "#a5b4fc") }}',
                            400: '{{ App\Models\SiteSetting::get("color_primary_400", "#818cf8") }}',
                            500: '{{ App\Models\SiteSetting::get("color_primary_500", "#6366f1") }}',
                            600: '{{ App\Models\SiteSetting::get("color_primary_600", "#4f46e5") }}',
                            700: '{{ App\Models\SiteSetting::get("color_primary_700", "#4338ca") }}',
                            800: '{{ App\Models\SiteSetting::get("color_primary_800", "#3730a3") }}',
                            900: '{{ App\Models\SiteSetting::get("color_primary_900", "#312e81") }}',
                        },
                        accent: {
                            50: '#f0fdf4',
                            500: '{{ App\Models\SiteSetting::get("color_accent", "#10b981") }}',
                            600: '#059669',
                        },
                        sidebar: '{{ App\Models\SiteSetting::get("sidebar_color", "#1e1b4b") }}',
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }
        .sidebar-link.active { background: rgba(255,255,255,0.15); border-right: 3px solid white; }
        .sidebar-link:hover { background: rgba(255,255,255,0.1); }
        .glass { backdrop-filter: blur(12px); background: rgba(255,255,255,0.8); }
        .stat-card { transition: all 0.3s ease; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 3px; }
        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .pulse-dot { animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-gray-50">
    <div class="flex h-full">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-sidebar text-white transform transition-transform duration-300 lg:translate-x-0 -translate-x-full">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i data-lucide="shopping-bag" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold">{{ App\Models\SiteSetting::get('site_name', 'NedaPOS') }}</h1>
                        <p class="text-xs text-white/60">Point of Sale</p>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                    <p class="px-3 text-xs font-semibold text-white/40 uppercase tracking-wider mb-2">Menu Utama</p>

                    <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white/80 hover:text-white transition {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                        Dashboard
                    </a>

                    <a href="{{ route('pos.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white/80 hover:text-white transition {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                        <i data-lucide="monitor" class="w-5 h-5"></i>
                        Kasir (POS)
                        <span class="ml-auto bg-green-500 text-xs px-2 py-0.5 rounded-full">LIVE</span>
                    </a>

                    <p class="px-3 pt-4 text-xs font-semibold text-white/40 uppercase tracking-wider mb-2">Manajemen</p>

                    <a href="{{ route('categories.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white/80 hover:text-white transition {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <i data-lucide="grid-3x3" class="w-5 h-5"></i>
                        Kategori
                    </a>

                    <a href="{{ route('products.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white/80 hover:text-white transition {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <i data-lucide="package" class="w-5 h-5"></i>
                        Produk
                    </a>

                    <a href="{{ route('orders.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white/80 hover:text-white transition {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                        <i data-lucide="receipt" class="w-5 h-5"></i>
                        Pesanan
                    </a>

                    <a href="{{ route('customers.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white/80 hover:text-white transition {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                        <i data-lucide="users" class="w-5 h-5"></i>
                        Pelanggan
                    </a>

                    <p class="px-3 pt-4 text-xs font-semibold text-white/40 uppercase tracking-wider mb-2">Inventaris</p>

                    <a href="{{ route('inventory.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white/80 hover:text-white transition {{ request()->routeIs('inventory.index') ? 'active' : '' }}">
                        <i data-lucide="warehouse" class="w-5 h-5"></i>
                        Stok Barang
                    </a>

                    <a href="{{ route('inventory.movements') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white/80 hover:text-white transition {{ request()->routeIs('inventory.movements') ? 'active' : '' }}">
                        <i data-lucide="arrow-left-right" class="w-5 h-5"></i>
                        Riwayat Stok
                    </a>

                    <p class="px-3 pt-4 text-xs font-semibold text-white/40 uppercase tracking-wider mb-2">Laporan</p>

                    <a href="{{ route('reports.sales') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white/80 hover:text-white transition {{ request()->routeIs('reports.sales') ? 'active' : '' }}">
                        <i data-lucide="trending-up" class="w-5 h-5"></i>
                        Laporan Penjualan
                    </a>

                    <a href="{{ route('reports.products') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white/80 hover:text-white transition {{ request()->routeIs('reports.products') ? 'active' : '' }}">
                        <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                        Laporan Produk
                    </a>

                    <p class="px-3 pt-4 text-xs font-semibold text-white/40 uppercase tracking-wider mb-2">Pengaturan</p>

                    <a href="{{ route('settings.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white/80 hover:text-white transition {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i data-lucide="settings" class="w-5 h-5"></i>
                        Kustomisasi
                    </a>
                </nav>

                <!-- User Info -->
                <div class="px-4 py-3 border-t border-white/10">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center text-sm font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-white/50 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="p-1.5 rounded-lg hover:bg-white/10 transition" title="Logout">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 lg:ml-64">
            <!-- Top Bar -->
            <header class="sticky top-0 z-40 glass border-b border-gray-200">
                <div class="flex items-center justify-between px-4 lg:px-6 py-3">
                    <div class="flex items-center gap-3">
                        <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg hover:bg-gray-100">
                            <i data-lucide="menu" class="w-5 h-5 text-gray-600"></i>
                        </button>
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                            <p class="text-xs text-gray-500">@yield('page-description', '')</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500 hidden sm:block">{{ now()->translatedFormat('l, d F Y') }}</span>
                        <a href="{{ route('pos.index') }}" class="ml-2 bg-primary-600 hover:bg-primary-700 text-white px-3 sm:px-4 py-2 rounded-xl text-sm font-medium flex items-center gap-2 transition shadow-lg shadow-primary-500/25">
                            <i data-lucide="monitor" class="w-4 h-4"></i>
                            <span class="hidden sm:inline">Buka Kasir</span>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-4 lg:p-6 fade-in">
                <!-- Flash Messages via SweetAlert2 -->
                @if(session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: @json(session('success')),
                                timer: 3000,
                                timerProgressBar: true,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end',
                            });
                        });
                    </script>
                @endif

                @if(session('error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: @json(session('error')),
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#4f46e5',
                            });
                        });
                    </script>
                @endif

                @if($errors->any())
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Terdapat Kesalahan',
                                html: '<ul style="text-align:left;margin-top:8px;list-style:disc;padding-left:20px;">' +
                                    @json($errors->all()).map(e => '<li>' + e + '</li>').join('') +
                                    '</ul>',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#4f46e5',
                            });
                        });
                    </script>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // SweetAlert2 Delete/Action Confirmation
        function confirmAction(formElement, title = 'Yakin?', text = 'Data yang dihapus tidak bisa dikembalikan!', confirmText = 'Ya, hapus!', icon = 'warning') {
            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    formElement.submit();
                }
            });
        }

        // Format currency helper
        function formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
        }
    </script>
    @stack('scripts')
</body>
</html>
