<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kasir - {{ App\Models\SiteSetting::get('site_name', 'NedaPOS') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }
        .product-card { transition: all 0.15s ease; }
        .product-card:hover { transform: scale(1.02); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .product-card:active { transform: scale(0.98); }
        .cart-item { animation: slideIn 0.2s ease; }
        @keyframes slideIn { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
        .category-chip.active { background: #4f46e5; color: white; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); }

        /* Mobile cart drawer */
        #cartDrawerOverlay { transition: opacity 0.3s ease; }
        #cartDrawer { transition: transform 0.3s ease; }
        #cartDrawer.translate-y-full { transform: translateY(100%); }
        #cartDrawer.translate-y-0 { transform: translateY(0); }

        @media (min-width: 768px) {
            #cartDrawerOverlay { display: none !important; }
            #cartDrawer {
                transform: none !important;
                position: fixed !important;
                top: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                width: 24rem !important;
                height: 100% !important;
                border-radius: 0 !important;
                z-index: 10 !important;
            }
            #mobileCartBtn { display: none !important; }
        }
    </style>
</head>
<body class="h-full bg-gray-100 overflow-hidden">
    <div class="flex h-full">
        <!-- Left Side - Products -->
        <div class="flex-1 flex flex-col h-full" style="margin-right: 0" id="productSide">
            <!-- POS Header -->
            <div class="bg-white border-b border-gray-200 px-3 sm:px-4 py-3 flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                    <a href="{{ route('dashboard') }}" class="p-2 rounded-lg hover:bg-gray-100 transition">
                        <i data-lucide="arrow-left" class="w-5 h-5 text-gray-600"></i>
                    </a>
                    <div>
                        <h1 class="text-base sm:text-lg font-bold text-gray-800">Kasir POS</h1>
                        <p class="text-xs text-gray-500 hidden sm:block">{{ auth()->user()->name }} &bull; {{ now()->format('d M Y') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-1 max-w-xs sm:max-w-sm md:max-w-md ml-2">
                    <div class="relative flex-1">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="text" id="searchInput" placeholder="Cari produk..."
                               class="pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm w-full focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                               oninput="searchProducts()">
                    </div>
                </div>
            </div>

            <!-- Category Chips -->
            <div class="bg-white border-b border-gray-100 px-3 sm:px-4 py-3 flex gap-2 overflow-x-auto">
                <button onclick="filterCategory(null)" class="category-chip active flex-shrink-0 px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-medium bg-gray-100 text-gray-600 transition" data-category="all">
                    Semua
                </button>
                @foreach($categories as $cat)
                    <button onclick="filterCategory({{ $cat->id }})" class="category-chip flex-shrink-0 px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-medium bg-gray-100 text-gray-600 transition" data-category="{{ $cat->id }}">
                        {{ $cat->name }} <span class="text-xs opacity-60">({{ $cat->active_products_count }})</span>
                    </button>
                @endforeach
            </div>

            <!-- Product Grid -->
            <div class="flex-1 overflow-y-auto p-3 sm:p-4 pb-24 md:pb-4">
                <div id="productGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 sm:gap-3">
                    <!-- Products loaded via JS -->
                </div>
                <div id="loadingProducts" class="flex items-center justify-center py-12">
                    <div class="text-center">
                        <div class="w-8 h-8 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
                        <p class="text-sm text-gray-500">Memuat produk...</p>
                    </div>
                </div>
                <div id="noProducts" class="hidden flex items-center justify-center py-12">
                    <div class="text-center">
                        <i data-lucide="package-x" class="w-12 h-12 text-gray-300 mx-auto mb-2"></i>
                        <p class="text-sm text-gray-400">Tidak ada produk ditemukan</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Cart Button (floating) -->
        <button id="mobileCartBtn" onclick="toggleCartDrawer()"
                class="md:hidden fixed bottom-5 right-5 z-40 bg-indigo-600 text-white w-16 h-16 rounded-full shadow-2xl shadow-indigo-500/40 flex items-center justify-center transition-transform active:scale-95">
            <i data-lucide="shopping-cart" class="w-6 h-6"></i>
            <span id="cartBadge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-6 h-6 rounded-full flex items-center justify-center font-bold hidden">0</span>
        </button>

        <!-- Cart Drawer Overlay (mobile) -->
        <div id="cartDrawerOverlay" class="md:hidden fixed inset-0 bg-black/50 z-40 hidden" onclick="toggleCartDrawer()"></div>

        <!-- Right Side - Cart (responsive: drawer on mobile, sidebar on desktop) -->
        <div id="cartDrawer" class="fixed right-0 bottom-0 z-50 w-full md:w-96 h-[85vh] md:h-full bg-white border-t md:border-t-0 md:border-l border-gray-200 flex flex-col shadow-xl rounded-t-3xl md:rounded-none translate-y-full">
            <!-- Drag Handle (mobile) -->
            <div class="md:hidden flex justify-center pt-3 pb-1">
                <div class="w-10 h-1.5 bg-gray-300 rounded-full"></div>
            </div>

            <!-- Cart Header -->
            <div class="px-4 py-3 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold text-gray-800">Keranjang Belanja</h2>
                    <div class="flex items-center gap-2">
                        <button onclick="clearCart()" class="text-xs text-red-500 hover:text-red-600 font-medium flex items-center gap-1">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Kosongkan
                        </button>
                        <button onclick="toggleCartDrawer()" class="md:hidden p-1.5 rounded-lg hover:bg-gray-100 text-gray-400">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
                <!-- Customer Select -->
                <div class="mt-2 flex gap-2">
                    <select id="customerSelect" class="flex-1 text-sm border border-gray-200 rounded-lg px-3 py-2 bg-gray-50 outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Pelanggan Umum</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                    <button onclick="showNewCustomerModal()" class="p-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <!-- Cart Items -->
            <div id="cartItems" class="flex-1 overflow-y-auto px-4 py-2">
                <div id="emptyCart" class="flex flex-col items-center justify-center h-full text-center">
                    <i data-lucide="shopping-bag" class="w-16 h-16 text-gray-200 mb-3"></i>
                    <p class="text-sm text-gray-400">Keranjang kosong</p>
                    <p class="text-xs text-gray-300 mt-1">Klik produk untuk menambahkan</p>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="border-t border-gray-100 px-4 py-3 space-y-2 bg-gray-50">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Subtotal</span>
                    <span id="subtotalDisplay" class="font-medium text-gray-700">Rp 0</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Diskon</span>
                    <div class="flex items-center gap-1">
                        <input type="number" id="discountInput" value="0" min="0"
                               class="w-20 text-right text-sm border border-gray-200 rounded-lg px-2 py-1 bg-white outline-none focus:ring-1 focus:ring-indigo-500"
                               oninput="calculateTotal()">
                        <select id="discountType" class="text-xs border border-gray-200 rounded-lg px-1 py-1 bg-white outline-none" onchange="calculateTotal()">
                            <option value="fixed">Rp</option>
                            <option value="percentage">%</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Pajak</span>
                    <div class="flex items-center gap-1">
                        <input type="number" id="taxInput" value="0" min="0" max="100"
                               class="w-20 text-right text-sm border border-gray-200 rounded-lg px-2 py-1 bg-white outline-none focus:ring-1 focus:ring-indigo-500"
                               oninput="calculateTotal()">
                        <span class="text-xs text-gray-400">%</span>
                    </div>
                </div>
                <div class="border-t border-gray-200 pt-2 flex justify-between">
                    <span class="font-bold text-gray-800">Total</span>
                    <span id="totalDisplay" class="text-xl font-bold text-indigo-600">Rp 0</span>
                </div>
            </div>

            <!-- Payment -->
            <div class="border-t border-gray-200 px-4 py-3 space-y-3">
                <div class="grid grid-cols-4 gap-1.5">
                    <button onclick="setPayment('cash')" class="payment-btn active px-2 py-2 text-xs font-medium rounded-lg border-2 border-indigo-500 bg-indigo-50 text-indigo-700 transition" data-method="cash">
                        <i data-lucide="banknote" class="w-4 h-4 mx-auto mb-0.5"></i>Cash
                    </button>
                    <button onclick="setPayment('card')" class="payment-btn px-2 py-2 text-xs font-medium rounded-lg border-2 border-gray-200 bg-white text-gray-600 transition" data-method="card">
                        <i data-lucide="credit-card" class="w-4 h-4 mx-auto mb-0.5"></i>Kartu
                    </button>
                    <button onclick="setPayment('ewallet')" class="payment-btn px-2 py-2 text-xs font-medium rounded-lg border-2 border-gray-200 bg-white text-gray-600 transition" data-method="ewallet">
                        <i data-lucide="smartphone" class="w-4 h-4 mx-auto mb-0.5"></i>E-Wallet
                    </button>
                    <button onclick="setPayment('transfer')" class="payment-btn px-2 py-2 text-xs font-medium rounded-lg border-2 border-gray-200 bg-white text-gray-600 transition" data-method="transfer">
                        <i data-lucide="building-2" class="w-4 h-4 mx-auto mb-0.5"></i>Transfer
                    </button>
                </div>

                <div id="cashPaymentSection">
                    <label class="text-xs text-gray-500 font-medium">Jumlah Bayar</label>
                    <input type="number" id="paidInput" min="0"
                           class="w-full mt-1 text-right text-lg font-bold border border-gray-200 rounded-xl px-4 py-2.5 bg-white outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="0" oninput="calculateChange()">
                    <div class="flex gap-1.5 mt-2">
                        <button onclick="quickPay('exact')" class="flex-1 text-xs py-2 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">Uang Pas</button>
                        <button onclick="quickPay(50000)" class="flex-1 text-xs py-2 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">50K</button>
                        <button onclick="quickPay(100000)" class="flex-1 text-xs py-2 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">100K</button>
                        <button onclick="quickPay(200000)" class="flex-1 text-xs py-2 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">200K</button>
                    </div>
                    <div class="mt-2 flex justify-between text-sm">
                        <span class="text-gray-500">Kembalian</span>
                        <span id="changeDisplay" class="font-bold text-green-600">Rp 0</span>
                    </div>
                </div>

                <button onclick="processOrder()" id="processBtn"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white py-3.5 rounded-xl text-sm font-bold transition shadow-lg shadow-indigo-500/25 flex items-center justify-center gap-2">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    Proses Pembayaran
                </button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
        <div class="bg-white rounded-3xl p-8 max-w-sm w-full mx-4 text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="check" class="w-10 h-10 text-green-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-1">Transaksi Berhasil!</h3>
            <p id="successInvoice" class="text-sm text-gray-500 mb-2"></p>
            <p id="successChange" class="text-lg font-bold text-green-600 mb-6"></p>
            <div class="flex gap-2">
                <button onclick="printReceipt()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-xl text-sm font-medium transition flex items-center justify-center gap-2">
                    <i data-lucide="printer" class="w-4 h-4"></i> Cetak Struk
                </button>
                <button onclick="newTransaction()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl text-sm font-medium transition flex items-center justify-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i> Transaksi Baru
                </button>
            </div>
        </div>
    </div>

    <!-- New Customer Modal -->
    <div id="customerModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
        <div class="bg-white rounded-2xl p-6 max-w-sm w-full mx-4">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Tambah Pelanggan Baru</h3>
            <div class="space-y-3">
                <input type="text" id="newCustName" placeholder="Nama Pelanggan *" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                <input type="text" id="newCustPhone" placeholder="No. HP" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-indigo-500">
                <input type="email" id="newCustEmail" placeholder="Email" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="flex gap-2 mt-4">
                <button onclick="closeCustomerModal()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2.5 rounded-xl text-sm font-medium transition">Batal</button>
                <button onclick="saveCustomer()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 rounded-xl text-sm font-medium transition">Simpan</button>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        let products = [];
        let cart = [];
        let currentCategory = null;
        let paymentMethod = 'cash';
        let lastOrderId = null;
        let cartOpen = false;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Toggle cart drawer on mobile
        function toggleCartDrawer() {
            const drawer = document.getElementById('cartDrawer');
            const overlay = document.getElementById('cartDrawerOverlay');
            cartOpen = !cartOpen;
            if (cartOpen) {
                drawer.classList.remove('translate-y-full');
                drawer.classList.add('translate-y-0');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                drawer.classList.add('translate-y-full');
                drawer.classList.remove('translate-y-0');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        // Update cart badge on mobile
        function updateCartBadge() {
            const badge = document.getElementById('cartBadge');
            const count = cart.reduce((sum, item) => sum + item.quantity, 0);
            if (count > 0) {
                badge.textContent = count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }

        // Format currency
        function formatRp(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(amount));
        }

        // Load products
        async function loadProducts(categoryId = null, search = '') {
            const grid = document.getElementById('productGrid');
            const loading = document.getElementById('loadingProducts');
            const noProducts = document.getElementById('noProducts');

            loading.classList.remove('hidden');
            noProducts.classList.add('hidden');
            grid.innerHTML = '';

            let url = '{{ route("pos.products") }}?';
            if (categoryId) url += `category_id=${categoryId}&`;
            if (search) url += `search=${encodeURIComponent(search)}&`;

            try {
                const res = await fetch(url);
                products = await res.json();
                loading.classList.add('hidden');

                if (products.length === 0) {
                    noProducts.classList.remove('hidden');
                    return;
                }

                grid.innerHTML = products.map(p => `
                    <div onclick="addToCart(${p.id})" class="product-card bg-white rounded-xl border border-gray-100 overflow-hidden cursor-pointer">
                        <div class="aspect-square bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center">
                            ${p.image
                                ? `<img src="/storage/${p.image}" class="w-full h-full object-cover" alt="${p.name}">`
                                : `<i data-lucide="package" class="w-8 h-8 text-gray-300"></i>`
                            }
                        </div>
                        <div class="p-2 sm:p-2.5">
                            <p class="text-xs font-medium text-gray-700 truncate">${p.name}</p>
                            <p class="text-xs text-gray-400 mt-0.5 hidden sm:block">Stok: ${p.stock} ${p.unit}</p>
                            <p class="text-xs sm:text-sm font-bold text-indigo-600 mt-0.5 sm:mt-1">${formatRp(p.selling_price)}</p>
                        </div>
                    </div>
                `).join('');

                lucide.createIcons();
            } catch (e) {
                loading.classList.add('hidden');
                console.error('Error loading products:', e);
            }
        }

        // Filter by category
        function filterCategory(categoryId) {
            currentCategory = categoryId;
            document.querySelectorAll('.category-chip').forEach(c => c.classList.remove('active'));
            const target = categoryId ? `[data-category="${categoryId}"]` : '[data-category="all"]';
            document.querySelector(target)?.classList.add('active');
            loadProducts(categoryId, document.getElementById('searchInput').value);
        }

        // Search products
        let searchTimeout;
        function searchProducts() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                loadProducts(currentCategory, document.getElementById('searchInput').value);
            }, 300);
        }

        // Add to cart
        function addToCart(productId) {
            const product = products.find(p => p.id === productId);
            if (!product) return;

            const existing = cart.find(item => item.product_id === productId);
            if (existing) {
                if (existing.quantity >= product.stock) {
                    Swal.fire({ icon: 'warning', title: 'Stok Habis', text: 'Stok tidak mencukupi!', confirmButtonColor: '#4f46e5' });
                    return;
                }
                existing.quantity++;
                existing.subtotal = existing.quantity * existing.price;
            } else {
                cart.push({
                    product_id: product.id,
                    name: product.name,
                    price: parseFloat(product.selling_price),
                    quantity: 1,
                    subtotal: parseFloat(product.selling_price),
                    stock: product.stock,
                    unit: product.unit,
                });
            }

            renderCart();
            calculateTotal();
            updateCartBadge();
        }

        // Remove from cart
        function removeFromCart(index) {
            cart.splice(index, 1);
            renderCart();
            calculateTotal();
            updateCartBadge();
        }

        // Update quantity
        function updateQuantity(index, delta) {
            const item = cart[index];
            if (!item) return;
            const newQty = item.quantity + delta;

            if (newQty <= 0) {
                removeFromCart(index);
                return;
            }

            if (newQty > item.stock) {
                Swal.fire({ icon: 'warning', title: 'Stok Habis', text: 'Stok tidak mencukupi!', confirmButtonColor: '#4f46e5' });
                return;
            }

            item.quantity = newQty;
            item.subtotal = item.quantity * item.price;
            renderCart();
            calculateTotal();
            updateCartBadge();
        }

        // Set quantity directly (from input)
        function setQuantity(index, value) {
            const item = cart[index];
            if (!item) return;
            let newQty = parseInt(value) || 0;

            if (newQty <= 0) {
                removeFromCart(index);
                return;
            }

            if (newQty > item.stock) {
                Swal.fire({ icon: 'warning', title: 'Stok Habis', text: 'Stok tidak mencukupi! Maksimal: ' + item.stock, confirmButtonColor: '#4f46e5' });
                newQty = item.stock;
            }

            item.quantity = newQty;
            item.subtotal = item.quantity * item.price;
            renderCart();
            calculateTotal();
            updateCartBadge();
        }

        // Render cart
        function renderCart() {
            const container = document.getElementById('cartItems');

            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="flex flex-col items-center justify-center h-full text-center">
                        <i data-lucide="shopping-bag" class="w-16 h-16 text-gray-200 mb-3"></i>
                        <p class="text-sm text-gray-400">Keranjang kosong</p>
                        <p class="text-xs text-gray-300 mt-1">Klik produk untuk menambahkan</p>
                    </div>`;
                lucide.createIcons();
                return;
            }

            container.innerHTML = cart.map((item, i) => `
                <div class="cart-item flex items-start gap-3 py-3 border-b border-gray-50">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-700 truncate">${item.name}</p>
                        <p class="text-xs text-gray-400">${formatRp(item.price)} / ${item.unit}</p>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button onclick="updateQuantity(${i}, -1)" class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition text-sm font-medium">-</button>
                        <input type="number" value="${item.quantity}" min="1" max="${item.stock}"
                               onchange="setQuantity(${i}, this.value)"
                               class="w-12 text-center text-sm font-bold text-gray-700 border border-gray-200 rounded-lg py-0.5 outline-none focus:ring-1 focus:ring-indigo-500 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                        <button onclick="updateQuantity(${i}, 1)" class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition text-sm font-medium">+</button>
                    </div>
                    <div class="text-right w-24">
                        <p class="text-sm font-bold text-gray-700">${formatRp(item.subtotal)}</p>
                        <button onclick="removeFromCart(${i})" class="text-xs text-red-400 hover:text-red-600 mt-0.5">Hapus</button>
                    </div>
                </div>
            `).join('');
        }

        // Clear cart
        function clearCart() {
            if (cart.length === 0) return;
            Swal.fire({
                title: 'Kosongkan Keranjang?',
                text: 'Semua item di keranjang akan dihapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, kosongkan!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    cart = [];
                    renderCart();
                    calculateTotal();
                    updateCartBadge();
                }
            });
        }

        // Calculate total
        function calculateTotal() {
            const subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
            const discount = parseFloat(document.getElementById('discountInput').value) || 0;
            const discountType = document.getElementById('discountType').value;
            const taxRate = parseFloat(document.getElementById('taxInput').value) || 0;

            const discountAmount = discountType === 'percentage' ? (subtotal * discount / 100) : discount;
            const afterDiscount = subtotal - discountAmount;
            const tax = afterDiscount * taxRate / 100;
            const total = afterDiscount + tax;

            document.getElementById('subtotalDisplay').textContent = formatRp(subtotal);
            document.getElementById('totalDisplay').textContent = formatRp(total);

            calculateChange();
        }

        // Calculate change
        function calculateChange() {
            const total = getTotal();
            const paid = parseFloat(document.getElementById('paidInput').value) || 0;
            const change = paid - total;
            document.getElementById('changeDisplay').textContent = formatRp(Math.max(0, change));
        }

        function getTotal() {
            const subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
            const discount = parseFloat(document.getElementById('discountInput').value) || 0;
            const discountType = document.getElementById('discountType').value;
            const taxRate = parseFloat(document.getElementById('taxInput').value) || 0;

            const discountAmount = discountType === 'percentage' ? (subtotal * discount / 100) : discount;
            const afterDiscount = subtotal - discountAmount;
            const tax = afterDiscount * taxRate / 100;
            return afterDiscount + tax;
        }

        // Set payment method
        function setPayment(method) {
            paymentMethod = method;
            document.querySelectorAll('.payment-btn').forEach(btn => {
                btn.classList.remove('border-indigo-500', 'bg-indigo-50', 'text-indigo-700');
                btn.classList.add('border-gray-200', 'bg-white', 'text-gray-600');
            });
            const active = document.querySelector(`[data-method="${method}"]`);
            active.classList.remove('border-gray-200', 'bg-white', 'text-gray-600');
            active.classList.add('border-indigo-500', 'bg-indigo-50', 'text-indigo-700');

            const cashSection = document.getElementById('cashPaymentSection');
            if (method === 'cash') {
                cashSection.style.display = 'block';
            } else {
                cashSection.style.display = 'block'; // Show for all methods to enter amount
            }
        }

        // Quick pay
        function quickPay(amount) {
            if (amount === 'exact') {
                document.getElementById('paidInput').value = Math.ceil(getTotal());
            } else {
                document.getElementById('paidInput').value = amount;
            }
            calculateChange();
        }

        // Process order
        async function processOrder() {
            if (cart.length === 0) {
                Swal.fire({ icon: 'info', title: 'Keranjang Kosong', text: 'Tambahkan produk ke keranjang terlebih dahulu.', confirmButtonColor: '#4f46e5' });
                return;
            }

            const total = getTotal();
            const paid = parseFloat(document.getElementById('paidInput').value) || 0;

            if (paid < total) {
                Swal.fire({ icon: 'error', title: 'Pembayaran Kurang', text: 'Jumlah bayar tidak mencukupi!', confirmButtonColor: '#4f46e5' });
                return;
            }

            const btn = document.getElementById('processBtn');
            btn.disabled = true;
            btn.innerHTML = '<div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Memproses...';

            try {
                const res = await fetch('{{ route("pos.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        items: cart.map(item => ({ product_id: item.product_id, quantity: item.quantity })),
                        customer_id: document.getElementById('customerSelect').value || null,
                        discount: parseFloat(document.getElementById('discountInput').value) || 0,
                        discount_type: document.getElementById('discountType').value,
                        tax_rate: parseFloat(document.getElementById('taxInput').value) || 0,
                        paid: paid,
                        payment_method: paymentMethod,
                        notes: '',
                    })
                });

                const data = await res.json();

                if (data.success) {
                    lastOrderId = data.order.id;
                    document.getElementById('successInvoice').textContent = data.order.invoice_number;
                    document.getElementById('successChange').textContent = 'Kembalian: ' + formatRp(data.order.change);
                    document.getElementById('successModal').classList.remove('hidden');
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message || 'Terjadi kesalahan!', confirmButtonColor: '#4f46e5' });
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan koneksi!', confirmButtonColor: '#4f46e5' });
                console.error(e);
            }

            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="check-circle" class="w-5 h-5"></i> Proses Pembayaran';
            lucide.createIcons();
        }

        // New transaction
        function newTransaction() {
            cart = [];
            renderCart();
            calculateTotal();
            updateCartBadge();
            document.getElementById('discountInput').value = 0;
            document.getElementById('taxInput').value = 0;
            document.getElementById('paidInput').value = '';
            document.getElementById('customerSelect').value = '';
            document.getElementById('successModal').classList.add('hidden');
            // Close cart drawer on mobile
            if (cartOpen) toggleCartDrawer();
            loadProducts();
        }

        // Print receipt
        function printReceipt() {
            if (lastOrderId) {
                window.open(`/pos/receipt/${lastOrderId}`, '_blank');
            }
        }

        // Customer modal
        function showNewCustomerModal() {
            document.getElementById('customerModal').classList.remove('hidden');
        }

        function closeCustomerModal() {
            document.getElementById('customerModal').classList.add('hidden');
        }

        async function saveCustomer() {
            const name = document.getElementById('newCustName').value;
            if (!name) { Swal.fire({ icon: 'warning', title: 'Nama Kosong', text: 'Nama pelanggan wajib diisi!', confirmButtonColor: '#4f46e5' }); return; }

            try {
                const res = await fetch('{{ route("customers.store.ajax") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        name: name,
                        phone: document.getElementById('newCustPhone').value,
                        email: document.getElementById('newCustEmail').value,
                    })
                });

                const data = await res.json();
                if (data.success) {
                    const select = document.getElementById('customerSelect');
                    const option = new Option(data.customer.name, data.customer.id, true, true);
                    select.add(option);
                    closeCustomerModal();
                    document.getElementById('newCustName').value = '';
                    document.getElementById('newCustPhone').value = '';
                    document.getElementById('newCustEmail').value = '';
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Gagal menyimpan pelanggan!', confirmButtonColor: '#4f46e5' });
            }
        }

        // Keyboard shortcut
        document.addEventListener('keydown', (e) => {
            if (e.key === 'F2') { e.preventDefault(); document.getElementById('searchInput').focus(); }
            if (e.key === 'F9') { e.preventDefault(); processOrder(); }
        });

        // Handle desktop layout - add margin to product side when cart is visible
        function handleResize() {
            const productSide = document.getElementById('productSide');
            if (window.innerWidth >= 768) {
                productSide.style.marginRight = '24rem';
            } else {
                productSide.style.marginRight = '0';
            }
        }
        window.addEventListener('resize', handleResize);
        handleResize();

        // Initial load
        loadProducts();
    </script>
</body>
</html>
