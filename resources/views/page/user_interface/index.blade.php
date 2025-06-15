<x-app-layout>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Warung Seblak Ajnira - Pemesanan</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <style>
            /* Style untuk dropdown */
            details summary {
                padding: 0.5rem;
                background-color: #f9fafb;
                border-radius: 0.5rem;
                cursor: pointer;
            }

            details[open] summary {
                background-color: #e5e7eb;
            }

            details div {
                padding: 0.5rem;
            }

            .hide-scrollbar {
                scrollbar-width: none;
                -ms-overflow-style: none;
            }

            .hide-scrollbar::-webkit-scrollbar {
                display: none;
            }

            #orderConfirmationModal {
                z-index: 1002;
            }

            @media (max-width: 768px) {
                #orderConfirmationModal {
                    align-items: flex-start;
                    padding-top: 20%;
                }

                #orderConfirmationModal>div {
                    width: 95vw;
                    margin: 0 auto;
                    max-height: 80vh;
                }
            }

            .scroll-hide::-webkit-scrollbar {
                display: none;
            }

            .horizontal-scroll {
                scroll-snap-type: x mandatory;
                scroll-behavior: smooth;
                -webkit-overflow-scrolling: touch;
            }

            .mobile-card {
                min-width: 85vw;
                scroll-snap-align: start;
            }

            .mobile-cart-panel {
                max-width: 280px;
                top: 100%;
                left: auto;
                right: 0;
                transform-origin: top right;
                margin-top: 4px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                z-index: 50;
            }

            @media (max-width: 768px) {
                .mobile-cart-panel {
                    width: 90vw !important;
                    left: 5vw !important;
                    right: 5vw !important;
                    max-width: 100vw !important;
                    transform-origin: top left;
                    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
                }

                .mobile-cart-panel .overflow-y-auto {
                    max-height: 65vh;
                }

                .mobile-cart-panel button[type="button"] {
                    width: 100%;
                    font-size: 0.9rem;
                    padding: 0.6rem;
                }
            }

            #cartDropdown {
                transition: all 0.3s ease;
            }

            @media (max-width: 768px) {
                #cartDropdown {
                    transform: translateY(0);
                    bottom: 1rem;
                    left: 2.5vw;
                    right: 2.5vw;
                    width: 95vw;
                    z-index: 1001;
                    border-radius: 1rem;
                }
            }

            .horizontal-scroll::-webkit-scrollbar {
                display: none;
            }

            .mobile-card {
                min-width: 80vw;
            }

            #mobileCartDropdown {
                box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
                max-height: 80vh;
                bottom: 80px;
                left: 1rem;
                right: 1rem;
                width: auto;
            }

            #mobileCartItems::-webkit-scrollbar {
                width: 4px;
            }

            #mobileCartItems::-webkit-scrollbar-track {
                background: #f1f1f1;
            }

            #mobileCartItems::-webkit-scrollbar-thumb {
                background: #888;
                border-radius: 4px;
            }

            @media (min-width: 768px) {
                #mobileCartDropdown {
                    display: none !important;
                }
            }

            @keyframes bounceIn {
                0% {
                    transform: scale(0.9);
                    opacity: 0;
                }

                50% {
                    transform: scale(1.05);
                }

                100% {
                    transform: scale(1);
                    opacity: 1;
                }
            }

            .animate-bounce-in {
                animation: bounceIn 0.3s ease;
            }

            .payment-detail {
                transition: all 0.2s ease;
            }

            .copy-number:hover {
                background-color: #f3f4f6;
                cursor: pointer;
            }

            .modal-scrollable {
                scrollbar-width: none;
                -ms-overflow-style: none;
            }

            .modal-scrollable::-webkit-scrollbar {
                display: none;
            }

            .category-btn {
                transition: all 0.2s ease;
                flex-shrink: 0;
            }

            .category-btn.active {
                background-color: #ef4444;
                color: white;
            }

            .cart-badge {
                top: -2px;
                right: -2px;
                background-color: #dc2626;
                color: white;
                border-radius: 9999px;
                width: 1.5rem;
                height: 1.5rem;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.75rem;
            }

            .cart-item-details {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 0.75rem;
            }

            .cart-item-card {
                flex-shrink: 0;
                min-width: 120px;
                max-width: 20rem;
            }

            .cart-item-qty {
                font-size: 0.75rem;
                color: #6b7280;
            }

            .cart-item-price {
                font-weight: bold;
                color: #ef4444;
            }

            #imageModal {
                backdrop-filter: blur(5px);
            }

            #imageModal img {
                box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
            }

            #imagePreview {
                max-width: 100%;
                max-height: 200px;
                display: block;
                margin: 0 auto;
            }

            @media (min-width: 768px) {
                #imagePreview {
                    max-height: 300px;
                }
            }

            #imagePreview:hover {
                transform: scale(1.02);
            }
        </style>
    </head>

    <body class="bg-gray-100">
        <!-- Checkout Modal -->
        <div id="checkoutModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md animate-bounce-in mx-4">
                <div class="flex justify-between items-center mb-4 border-b pb-2 relative">
                    <h3 class="text-xl font-bold text-red-500">Konfirmasi Pesanan</h3>
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-gray-500 hover:text-gray-700 cursor-pointer" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
                            onclick="showTableInfoAlert()" aria-label="Informasi tentang status meja">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <button onclick="closeCheckoutModal()" class="text-gray-500 hover:text-gray-700">×</button>
                    </div>
                </div>
                <form id="checkoutForm" onsubmit="processPayment(event)">
                    @csrf
                    <div class="max-h-[70vh] overflow-y-auto modal-scrollable space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Pelanggan</label>
                            <input type="text" value="{{ Auth::user()->name }}" disabled
                                class="mt-1 block w-full rounded-md bg-gray-100 p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor Meja</label>
                            <select id="tableNumber" class="mt-1 block w-full rounded-md border p-2"
                                onchange="checkTableStatus(this.value)">
                                <option value="">Pilih Meja</option>
                                @foreach ($tables as $table)
                                    <option value="{{ $table->id }}" data-status="{{ $table->status }}"
                                        {{ $table->status === 'occupied' ? 'disabled' : '' }}>
                                        Meja {{ $table->number }} -
                                        {{ $table->status === 'occupied' ? ' (Terisi)' : ' (Tersedia)' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="flex items-center space-x-2 mt-2">
                                <label class="block text-sm font-medium text-gray-700">Take Away</label>
                                <input type="checkbox" id="takeAwayToggle" onchange="toggleTakeAway(this.checked)">
                            </div>
                            <div id="tableStatusMessage" class="text-sm mt-1"></div>
                        </div>
                        <div class="border rounded-lg p-3">
                            <h4 class="font-medium mb-2">Detail Pesanan:</h4>
                            <div id="orderItems" class="space-y-2"></div>
                            <div class="mt-3 pt-2 border-t">
                                <p class="flex justify-between font-bold">
                                    <span>Total:</span>
                                    <span id="modalTotal" class="text-red-500">Rp0</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 mt-4">
                            <button type="button" onclick="closeCheckoutModal()"
                                class="px-4 py-2 border rounded-md hover:bg-gray-50">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">Bayar
                                Sekarang</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Payment Confirmation Modal -->
        <div id="paymentConfirmationModal"
            class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md animate-bounce-in mx-4">
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h3 class="text-xl font-bold text-red-500">Pembayaran</h3>
                    <button onclick="closePaymentModal()" class="text-gray-500 hover:text-gray-700">×</button>
                </div>
                <div class="space-y-4 max-h-[70vh] modal-scrollable overflow-y-auto">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                        <select id="paymentMethod" onchange="showPaymentDetails()"
                            class="mt-1 block w-full rounded-md border p-2">
                            <option value="">Pilih Pembayaran</option>
                            @foreach ($paymentProviders->where('is_active', true) as $provider)
                                <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="paymentDetails" class="hidden space-y-3">
                        <div class="flex items-center space-x-3">
                            <img id="providerLogo" src="" class="w-12 h-12 object-contain rounded-lg">
                            <div>
                                <p class="font-bold" id="providerName"></p>
                                <p class="text-sm text-gray-500" id="providerType"></p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Nomor Rekening</p>
                                <div class="copy-number bg-gray-50 rounded-lg p-2" onclick="copyToClipboard(this)">
                                    <span class="font-mono text-gray-800" id="accountNumber"></span>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Atas Nama</p>
                                <p class="font-medium text-gray-800" id="accountName"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Petunjuk Pembayaran</p>
                                <p class="text-sm text-gray-800" id="instructions"></p>
                            </div>
                        </div>
                    </div>
                    <div class="border-t pt-4">
                        <div class="border rounded-lg p-3">
                            <h4 class="font-medium mb-2">Detail Pesanan:</h4>
                            <div id="paymentOrderItems" class="space-y-2 max-h-40 modal-scrollable overflow-y-auto">
                            </div>
                            <div class="mt-3 pt-2 border-t">
                                <p class="flex justify-between font-bold">
                                    <span>Total:</span>
                                    <span id="paymentTotal" class="text-red-500">Rp0</span>
                                </p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Upload Bukti Transfer</label>
                            <input type="file" id="paymentProof" accept="image/*"
                                class="mt-1 block w-full rounded-md border p-2 hidden" onchange="previewImage(event)">
                            <label for="paymentProof" class="cursor-pointer">
                                <div id="uploadLabel"
                                    class="border-2 border-dashed rounded-md p-4 text-center mt-2 hover:bg-gray-50 transition-colors">
                                    <span class="text-blue-500">Klik untuk Upload Bukti Transfer</span>
                                </div>
                            </label>
                            <div id="imagePreviewContainer" class="mt-3 hidden">
                                <div class="relative group">
                                    <img id="imagePreview" class="max-h-40 rounded-lg shadow-sm cursor-zoom-in"
                                        onclick="showImageModal(this.src)">
                                    <div
                                        class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button onclick="removeImagePreview()"
                                            class="bg-red-500 text-white rounded-full p-1 hover:bg-red-600">✕</button>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Klik gambar untuk memperbesar</p>
                            </div>
                        </div>
                    </div>
                    <button onclick="submitPayment()"
                        class="w-full bg-red-500 text-white py-2 rounded-md hover:bg-red-600 transition-colors">Konfirmasi
                        Pembayaran</button>
                </div>
            </div>
        </div>
        <!-- Image Modal -->
        <div id="imageModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-90 flex items-center justify-center"
            onclick="closeImageModalIfBackground(event)">
            <div class="max-w-4xl max-h-[90vh]">
                <img id="modalImage" class="max-h-[80vh] rounded-lg">
                <button onclick="closeImageModal()"
                    class="absolute top-4 right-4 text-white text-3xl hover:text-gray-200">×</button>
            </div>
        </div>
        <!-- Order Confirmation Modal -->
        <div id="orderConfirmationModal"
            class="fixed inset-0 z-[1002] hidden flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md animate-bounce-in mx-4">
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h3 class="text-xl font-bold text-red-500">Pesanan Berhasil</h3>
                    <button onclick="closeOrderModal()" class="text-gray-500 hover:text-gray-700">×</button>
                </div>
                <div class="space-y-4 max-h-[70vh] modal-scrollable overflow-y-auto">
                    <div class="space-y-2">
                        <p>Nama: <span class="font-bold">{{ Auth::user()->name }}</span></p>
                        <p>Tanggal: <span id="orderDate">{{ now()->format('d/m/Y H:i') }}</span></p>
                        <p>Status: <span class="font-bold status-text"></span></p>
                    </div>
                    <button id="printButton" style="display: none;" onclick="printOrder()"
                        class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600">Cetak Pesanan</button>
                </div>
            </div>
        </div>
        <!-- Floating Mobile Cart -->
        <div class="fixed top-16 right-4 z-[1001]">
            <div class="fixed bottom-4 right-4 z-50 md:hidden">
                <button id="mobileCartButton" onclick="toggleMobileCart()"
                    class="bg-red-500 text-white p-4 rounded-full shadow-xl relative hover:bg-red-600 transition-transform transform hover:scale-110">
                    🛒
                    <span id="mobileCartBadge"
                        class="absolute -top-1 -right-1 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">0</span>
                </button>
            </div>
            <div id="mobileCartDropdown"
                class="fixed bottom-20 right-4 left-4 z-50 hidden bg-white rounded-xl shadow-2xl transition-all duration-300 origin-bottom-right transform">
                <div class="p-4 max-h-[70vh] flex flex-col">
                    <div class="flex justify-between items-center mb-4 pb-2 border-b">
                        <h3 class="text-lg font-bold">Keranjang Belanja</h3>
                        <div class="flex items-center space-x-4">
                            <button onclick="addNewPerson()" aria-label="Tambah Menu" class="p-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="text-blue-500">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                            </button>
                            <button onclick="clearCart()" aria-label="Hapus Semua" class="p-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="text-red-500">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path
                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                    </path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                            </button>
                            <button onclick="toggleMobileCart()" class="text-gray-500 hover:text-gray-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div id="mobileCartItems" class="flex-1 overflow-y-auto space-y-3 mb-4 hide-scrollbar"></div>
                    <div class="pt-4 border-t">
                        <div class="flex justify-between mb-3">
                            <span class="font-medium">Total Item:</span>
                            <span id="mobileCartItemTotal" class="font-bold text-red-500">0 item</span>
                        </div>
                        <div class="flex justify-between mb-4">
                            <span class="font-medium">Total Harga:</span>
                            <span id="mobileCartTotal" class="font-bold text-red-500">Rp0</span>
                        </div>
                        <button onclick="openCheckoutModal()"
                            class="w-full bg-red-500 text-white py-3 rounded-xl hover:bg-red-600 transition-colors font-medium">Checkout
                            Sekarang</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Main Content -->
        <div class="container mx-auto p-4">
            <div class="flex justify-between items-center mb-4 md:mb-8">
                <div class="text-left">
                    <h1 class="text-2xl md:text-3xl font-bold text-red-500 mb-1">🍲 Warung Seblak Ajnira</h1>
                    <p class="text-sm md:text-base text-gray-600">Pilih topping seblak favoritmu</p>
                </div>
                <div class="hidden md:block relative">
                    <button onclick="toggleCart()"
                        class="bg-red-500 text-white p-2 md:p-3 rounded-full shadow-lg relative hover:bg-red-600">
                        🛒
                        <span id="cartBadge"
                            class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">0</span>
                    </button>
                    <div id="cartDropdown"
                        class="hidden mt-2 w-72 bg-white rounded-lg shadow-xl absolute right-0 z-[1001]">
                        <div class="p-4">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold">Keranjang</h3>
                                <div class="flex items-center space-x-4">
                                    <button onclick="addNewPerson()" aria-label="Tambah Menu" class="p-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="text-blue-500">
                                            <line x1="12" y1="5" x2="12" y2="19">
                                            </line>
                                            <line x1="5" y1="12" x2="19" y2="12">
                                            </line>
                                        </svg>
                                    </button>
                                    <button onclick="clearCart()" aria-label="Hapus Semua" class="p-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="text-red-500">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path
                                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                            </path>
                                            <line x1="10" y1="11" x2="10" y2="17">
                                            </line>
                                            <line x1="14" y1="11" x2="14" y2="17">
                                            </line>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div id="cartItems" class="space-y-3 max-h-60 overflow-y-auto hide-scrollbar"></div>
                            <div class="mt-4 pt-4 border-t">
                                <div class="flex justify-between mb-3">
                                    <span class="font-medium">Total Item:</span>
                                    <span id="cartItemTotal" class="font-bold text-red-500">0 item</span>
                                </div>
                                <div class="flex justify-between mb-3">
                                    <span class="font-medium">Total Harga:</span>
                                    <span id="cartTotal" class="font-bold text-red-500">Rp0</span>
                                </div>
                                <button onclick="openCheckoutModal()"
                                    class="w-full bg-red-500 text-white py-2 rounded-md hover:bg-red-600">Checkout</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Mobile Categories Scroll -->
            <div class="md:hidden mb-4">
                <div class="flex-1 overflow-x-auto scroll-hide horizontal-scroll">
                    <div class="flex space-x-3 pb-3">
                        <button onclick="filterByCategory('all')"
                            class="category-btn active px-4 py-2 bg-red-500 text-white rounded-full whitespace-nowrap">Semua
                            Topping</button>
                        @foreach ($categories as $category)
                            <button onclick="filterByCategory('{{ $category->id }}')"
                                class="category-btn px-4 py-2 bg-gray-100 text-gray-600 rounded-full whitespace-nowrap">{{ $category->name }}</button>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- Desktop Categories -->
            <div class="hidden md:flex mb-6 space-x-3">
                <button onclick="filterByCategory('all')"
                    class="category-btn active px-4 py-2 bg-red-500 text-white rounded-md">Semua Topping</button>
                @foreach ($categories as $category)
                    <button onclick="filterByCategory('{{ $category->id }}')"
                        class="category-btn px-4 py-2 bg-gray-100 text-gray-600 rounded-md">{{ $category->name }}</button>
                @endforeach
            </div>
            <!-- Mobile Cart Scroll -->
            <div class="md:hidden mb-6">
                <div id="mobileCart" class="flex overflow-x-auto scroll-hide gap-3 pb-3 horizontal-scroll"></div>
            </div>
            <!-- Topping Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3" id="non-minuman-toppings">
                @php
                    $minumanCategory = $categories->where('name', 'Minuman')->first();
                    $minumanCategoryId = $minumanCategory ? $minumanCategory->id : null;
                @endphp
                @foreach ($topings->where('category_id', '!=', $minumanCategoryId) as $toping)
<div class="bg-white rounded-xl p-2 md:p-3 shadow-md hover:shadow-lg transition-transform transform hover:scale-[1.02]" data-category-id="{{ $toping->category_id }}">
                    <div class="relative h-28 md:h-36 rounded-lg overflow-hidden mb-2">
                        @if ($toping->image)
<img src="{{ asset($toping->image) }}" alt="{{ $toping->name }}" class="w-full h-full object-cover">
@else
<div class="w-full h-full bg-gray-200 animate-pulse"></div>
@endif
                        <div class="absolute bottom-1 right-1 px-2 py-0.5 bg-black bg-opacity-50 text-white rounded-full text-xs">
                            Stok: <span id="stock-{{ $toping->id }}">{{ $toping->stock }}</span>
                        </div>
                    </div>
                    <div class="text-gray-800 px-1">
                        <h3 class="text-sm md:text-base font-bold mb-1 truncate">{{ $toping->name }}</h3>
                        <div class="flex justify-between items-center">
                            <p class="text-base md:text-lg font-bold text-red-500">Rp{{ number_format($toping->price_buy, 0, ',', '.') }}</p>
                            <div class="flex items-center space-x-2">
                                <button onclick="updateQuantity('{{ $toping->id }}', -1, {{ $toping->price_buy }})" class="bg-gray-200 text-gray-700 px-2 py-1 rounded-md hover:bg-gray-300 text-xs md:text-sm">-</button>
                                <span id="qty-{{ $toping->id }}" class="px-1 text-sm">0</span>
                                <button onclick="updateQuantity('{{ $toping->id }}', 1, {{ $toping->price_buy }})" class="bg-red-500 text-white px-2 py-1 rounded-md hover:bg-red-600 text-xs md:text-sm">+</button>
                            </div>
                        </div>
                    </div>
                </div>
 @endforeach
                    </div>
                    <!-- Minuman Toppings -->
                    <div class="mt-6" id="minuman-toppings">
                    <h2 class="text-xl font-bold text-red-500 mb-4">Minuman</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach ($topings->where('category_id', $minumanCategoryId) as $toping)
                    <div
                    class="bg-white rounded-xl p-2 md:p-3 shadow-md hover:shadow-lg transition-transform transform hover:scale-[1.02]"
                    data-category-id="{{ $toping->category_id }}">
                    <div class="relative h-28 md:h-36 rounded-lg overflow-hidden mb-2">
                    @if ($toping->image)
                    <img src="{{ asset($toping->image) }}" alt="{{ $toping->name }}"
                    class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gray-200 animate-pulse"></div>
                @endif
                <div
                class="absolute bottom-1 right-1 px-2 py-0.5 bg-black bg-opacity-50 text-white rounded-full text-xs">
                Stok: <span id="stock-{{ $toping->id }}">{{ $toping->stock }}</span>
                </div>
                </div>
                <div class="text-gray-800 px-1">
                <h3 class="text-sm md:text-base font-bold mb-1 truncate">{{ $toping->name }}</h3>
                <div class="flex justify-between items-center">
                <p
                class="text-base md:text-lg font-bold text-red-500">Rp{{ number_format($toping->price_buy, 0, ',', '.') }}</p>
                <div class="flex items-center space-x-2">
                <button onclick="updateQuantity('{{ $toping->id }}', -1, {{ $toping->price_buy }})"
                class="bg-gray-200 text-gray-700 px-2 py-1 rounded-md hover:bg-gray-300 text-xs md:text-sm">-</button>
                <span id="qty-{{ $toping->id }}" class="px-1 text-sm">0</span>
                <button onclick="updateQuantity('{{ $toping->id }}', 1, {{ $toping->price_buy }})"
                class="bg-red-500 text-white px-2 py-1 rounded-md hover:bg-red-600 text-xs md:text-sm">+</button>
                </div>
                </div>
                </div>
                </div>
                @endforeach
                </div>
                </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        checkPendingOrder();
                        document.addEventListener('click', function(event) {
                            const cartButton = document.getElementById('mobileCartButton');
                            const cartDropdown = document.getElementById('mobileCartDropdown');
                            if (!cartButton.contains(event.target) && !cartDropdown.contains(event.target)) {
                                cartDropdown.classList.add('hidden');
                            }
                            const mobilePanel = document.getElementById('mobileCartPanel');
                            const mobileButton = document.getElementById('mobileCartButton');
                            if (mobilePanel && mobileButton) {
                                if (!mobilePanel.contains(event.target) && !mobileButton.contains(event.target)) {
                                    mobilePanel.classList.add('hidden');
                                    mobilePanel.classList.remove('animate-bounce-in');
                                }
                            }
                        });
                        window.toggleCart = function() {
                            const dropdown = document.getElementById('cartDropdown');
                            if (window.innerWidth >= 768) {
                                dropdown.classList.toggle('hidden');
                            }
                        };
                    });

                    function closeOrderModal() {
                        document.getElementById('orderConfirmationModal').classList.add('hidden');
                        sessionStorage.removeItem('pendingOrderStatus');
                    }

                    let cart = [{
                        person: "Orang 1",
                        items: [],
                        spiciness_level: "",
                        bowl_size: ""
                    }];
                    let cartVisible = false;
                    let currentPersonIndex = 0;
                    const paymentProviders = @json($paymentProviders->where('is_active', true));

                    function updateCartDisplay() {
                        const mobileItems = document.getElementById('mobileCartItems');
                        const desktopItems = document.getElementById('cartItems');

                        // Calculate totals per person and overall
                        const personTotals = cart.map(person => {
                            const totalQty = person.items.reduce((sum, item) => sum + item.quantity, 0);
                            const totalPrice = person.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                            return {
                                person: person.person,
                                totalQty,
                                totalPrice
                            };
                        });

                        const overallTotalQty = personTotals.reduce((acc, person) => acc + person.totalQty, 0);
                        const overallTotalPrice = personTotals.reduce((acc, person) => acc + person.totalPrice, 0);

                        // Update badges and totals
                        document.getElementById('cartBadge').textContent = overallTotalQty;
                        document.getElementById('mobileCartBadge').textContent = overallTotalQty;
                        document.getElementById('cartItemTotal').textContent = `${overallTotalQty} item`;
                        document.getElementById('mobileCartItemTotal').textContent = `${overallTotalQty} item`;
                        document.getElementById('cartTotal').textContent = `Rp${overallTotalPrice.toLocaleString('id-ID')}`;
                        document.getElementById('mobileCartTotal').textContent = `Rp${overallTotalPrice.toLocaleString('id-ID')}`;

                        // Generate person buttons
                        const personButtons = cart.map((person, index) => `
                <button onclick="selectPerson(${index})" class="px-4 py-2 ${currentPersonIndex === index ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-600'} rounded-md mr-2">${person.person}</button>
            `).join('');

                        // Generate cart HTML with per-person totals
                        const cartHTML = `
                <div class="flex space-x-3 mb-4">${personButtons}</div>
                <div class="space-y-3">
                    <div class="flex space-x-2">
                        <select onchange="updateSpicinessLevel(this.value)" class="rounded-md border p-1 text-sm">
                            <option value="">Pilih Level Pedas</option>
                            <option value="mild" ${cart[currentPersonIndex].spiciness_level === 'mild' ? 'selected' : ''}>Pedas Level 1</option>
                            <option value="medium" ${cart[currentPersonIndex].spiciness_level === 'medium' ? 'selected' : ''}>Pedas Level 2</option>
                            <option value="hot" ${cart[currentPersonIndex].spiciness_level === 'hot' ? 'selected' : ''}>Pedas Level 3</option>
                            <option value="extreme" ${cart[currentPersonIndex].spiciness_level === 'extreme' ? 'selected' : ''}>Pedas Level 4</option>
                        </select>
                        <select onchange="updateBowlSize(this.value)" class="rounded-md border p-1 text-sm">
                            <option value="">Pilih Ukuran Mangkok</option>
                            <option value="small" ${cart[currentPersonIndex].bowl_size === 'small' ? 'selected' : ''}>Kecil</option>
                            <option value="medium" ${cart[currentPersonIndex].bowl_size === 'medium' ? 'selected' : ''}>Sedang</option>
                            <option value="large" ${cart[currentPersonIndex].bowl_size === 'large' ? 'selected' : ''}>Besar</option>
                        </select>
                    </div>
                    ${cart[currentPersonIndex].items.length > 0 ? cart[currentPersonIndex].items.map(item => `
                                                        <div class="flex justify-between items-start bg-gray-50 rounded-lg p-3">
                                                            <div class="flex-1">
                                                                <p class="font-medium text-sm">${item.name}</p>
                                                                <div class="flex items-center space-x-2 mt-1">
                                                                    <button onclick="updateQuantity('${item.id}', -1, ${item.price})" class="bg-gray-200 text-gray-700 px-2 py-1 rounded-lg text-xs">−</button>
                                                                    <span class="text-sm font-medium">${item.quantity}</span>
                                                                    <button onclick="updateQuantity('${item.id}', 1, ${item.price})" class="bg-red-500 text-white px-2 py-1 rounded-lg text-xs">+</button>
                                                                </div>
                                                            </div>
                                                            <div class="text-right">
                                                                <p class="text-sm font-medium text-red-500">Rp${(item.price * item.quantity).toLocaleString('id-ID')}</p>
                                                                <button onclick="removeItem('${item.id}')" class="text-gray-400 hover:text-red-500 text-xs mt-1">Hapus</button>
                                                            </div>
                                                        </div>
                                                    `).join('') : '<p class="text-sm text-gray-500">Belum ada item</p>'}
                    <div class="border-t pt-2 mt-2">
                        <p class="flex justify-between font-bold">
                            <span>Total ${cart[currentPersonIndex].person}:</span>
                            <span class="text-red-500">Rp${personTotals[currentPersonIndex].totalPrice.toLocaleString('id-ID')}</span>
                        </p>
                    </div>
                </div>
            `;

                        mobileItems.innerHTML = cartHTML;
                        desktopItems.innerHTML = cartHTML;

                        resetMenuSelections();
                    }

                    function updateSpicinessLevel(level) {
                        cart[currentPersonIndex].spiciness_level = level;
                    }

                    function updateBowlSize(size) {
                        cart[currentPersonIndex].bowl_size = size;
                    }

                    function selectPerson(index) {
                        currentPersonIndex = index;
                        updateCartDisplay();
                    }

                    function resetMenuSelections() {
                        document.querySelectorAll('[id^="qty-"]').forEach(el => {
                            const id = el.id.replace('qty-', '');
                            const qtyForCurrentPerson = cart[currentPersonIndex].items.filter(item => item.id === id).reduce((
                                sum, item) => sum + item.quantity, 0);
                            el.textContent = qtyForCurrentPerson;
                        });
                    }

                    function addNewPerson() {
                        const newPersonNumber = cart.length + 1;
                        cart.push({
                            person: `Orang ${newPersonNumber}`,
                            items: [],
                            spiciness_level: "",
                            bowl_size: ""
                        });
                        currentPersonIndex = cart.length - 1;
                        updateCartDisplay();
                    }

                    document.addEventListener('click', function(event) {
                        const cartDropdown = document.getElementById('cartDropdown');
                        const cartButton = document.querySelector('.hidden.md\\:block .relative button');
                        if (cartDropdown && cartButton) {
                            if (!cartDropdown.contains(event.target) && !cartButton.contains(event.target)) {
                                cartDropdown.classList.add('hidden');
                            }
                        }
                        const checkoutButton = document.querySelector('[onclick="openCheckoutModal()"]');
                        if (checkoutButton && event.target.closest('[onclick="openCheckoutModal()"]')) {
                            if (cartDropdown) cartDropdown.classList.add('hidden');
                            if (mobilePanel) mobilePanel.classList.add('hidden');
                        }
                    });

                    window.filterByCategory = function(categoryId) {
                        const minumanCategory = @json($categories->where('name', 'Minuman')->first());
                        const minumanId = minumanCategory ? minumanCategory.id : null;
                        document.querySelectorAll('.category-btn').forEach(btn => {
                            btn.classList.remove('active', 'bg-red-500', 'text-white');
                            btn.classList.add('bg-gray-100', 'text-gray-600');
                        });
                        const activeBtn = [...document.querySelectorAll('.category-btn')].find(btn => btn.textContent.trim() === (
                            categoryId === 'all' ? 'Semua Topping' : document.querySelector(
                                `[onclick="filterByCategory('${categoryId}')"]`)?.textContent?.trim()));
                        if (activeBtn) {
                            activeBtn.classList.remove('bg-gray-100', 'text-gray-600');
                            activeBtn.classList.add('active', 'bg-red-500', 'text-white');
                        }
                        const nonMinumanSection = document.getElementById('non-minuman-toppings');
                        const minumanSection = document.getElementById('minuman-toppings');
                        const nonMinumanItems = document.querySelectorAll('#non-minuman-toppings [data-category-id]');
                        const minumanItems = document.querySelectorAll('#minuman-toppings [data-category-id]');
                        if (categoryId === 'all') {
                            nonMinumanSection.classList.remove('hidden');
                            minumanSection.classList.remove('hidden');
                            nonMinumanItems.forEach(item => item.classList.remove('hidden'));
                            minumanItems.forEach(item => item.classList.remove('hidden'));
                        } else if (categoryId == minumanId) {
                            nonMinumanSection.classList.add('hidden');
                            minumanSection.classList.remove('hidden');
                            minumanItems.forEach(item => item.classList.remove('hidden'));
                        } else {
                            nonMinumanSection.classList.remove('hidden');
                            minumanSection.classList.add('hidden');
                            nonMinumanItems.forEach(item => {
                                if (item.dataset.categoryId === categoryId) {
                                    item.classList.remove('hidden');
                                } else {
                                    item.classList.add('hidden');
                                }
                            });
                        }
                    };

                    window.updateQuantity = function(id, change, price) {
                        price = parseFloat(price);
                        if (isNaN(price)) {
                            console.error(`Harga untuk item ${id} bukan angka: ${price}`);
                            Swal.fire('Error!', 'Harga item tidak valid', 'error');
                            return;
                        }
                        const person = cart[currentPersonIndex];
                        const itemIndex = person.items.findIndex(item => item.id === id);
                        const stockElement = document.getElementById(`stock-${id}`);
                        if (!stockElement) {
                            console.error(`Stock element for item ${id} not found`);
                            Swal.fire('Error!', 'Stock item tidak ditemukan', 'error');
                            return;
                        }
                        let currentStock = parseInt(stockElement.textContent);
                        if (itemIndex > -1) {
                            const newQty = person.items[itemIndex].quantity + change;
                            if (newQty < 0) return;
                            if (change > 0 && currentStock < change) {
                                Swal.fire('Stok tidak cukup!', `Stok tersisa hanya ${currentStock} item`, 'warning');
                                return;
                            }
                            if (newQty === 0) {
                                stockElement.textContent = currentStock + person.items[itemIndex].quantity;
                                person.items.splice(itemIndex, 1);
                            } else {
                                person.items[itemIndex].quantity = newQty;
                                stockElement.textContent = currentStock - change;
                            }
                        } else if (change === 1) {
                            if (currentStock < 1) {
                                Swal.fire('Stok habis!', '', 'warning');
                                return;
                            }
                            const nameElement = document.querySelector(`#qty-${id}`).parentElement.parentElement.parentElement
                                .querySelector('h3');
                            if (!nameElement) {
                                console.error(`Name element for item ${id} not found`);
                                Swal.fire('Error!', 'Nama item tidak ditemukan', 'error');
                                return;
                            }
                            person.items.push({
                                id: id,
                                name: nameElement.textContent,
                                price: price,
                                quantity: 1
                            });
                            stockElement.textContent = currentStock - 1;
                        }
                        updateCartDisplay();
                    };

                    function removeItem(id) {
                        const person = cart[currentPersonIndex];
                        const itemIndex = person.items.findIndex(item => item.id === id);
                        if (itemIndex === -1) return;
                        const removedItem = person.items[itemIndex];
                        const stockElement = document.getElementById(`stock-${id}`);
                        if (stockElement) {
                            stockElement.textContent = parseInt(stockElement.textContent) + removedItem.quantity;
                        }
                        person.items.splice(itemIndex, 1);
                        updateCartDisplay();
                    }

                    function clearCart() {
                        cart.forEach(person => {
                            person.items.forEach(item => {
                                const stockElement = document.getElementById(`stock-${item.id}`);
                                if (stockElement) {
                                    stockElement.textContent = parseInt(stockElement.textContent) + item.quantity;
                                }
                            });
                        });
                        cart = [{
                            person: "Orang 1",
                            items: [],
                            spiciness_level: "",
                            bowl_size: ""
                        }];
                        currentPersonIndex = 0;
                        updateCartDisplay();
                    }

                    function toggleCart() {
                        if (window.innerWidth >= 768) {
                            const dropdown = document.getElementById('cartDropdown');
                            dropdown.classList.toggle('hidden');
                        }
                    }

                    function toggleMobileCart() {
                        const dropdown = document.getElementById('mobileCartDropdown');
                        dropdown.classList.toggle('hidden');
                        dropdown.classList.toggle('animate-bounce-in');
                        if (!dropdown.classList.contains('hidden')) {
                            document.activeElement.blur();
                            window.scrollTo({
                                top: document.body.scrollHeight,
                                behavior: 'smooth'
                            });
                        }
                    }

                    function openCheckoutModal() {
                        for (let person of cart) {
                            if (person.items.length > 0 && (!person.spiciness_level || !person.bowl_size)) {
                                Swal.fire('Error!', `Harap pilih tingkat kepedasan dan ukuran mangkuk untuk ${person.person}`, 'error');
                                return;
                            }
                        }
                        if (cart.every(person => person.items.length === 0)) {
                            Swal.fire('Keranjang kosong!', 'Silakan tambahkan item terlebih dahulu', 'warning');
                            return;
                        }
                        showModal('checkoutModal');
                        const orderItemsHTML = cart.map(person => `
                <div class="border-b pb-2 mb-2">
                    <p class="font-bold">${person.person}</p>
                    ${person.items.map(item => `
                                                        <div class="flex justify-between">
                                                            <span>${item.name} (Qty: ${item.quantity})</span>
                                                            <span>Rp${(item.price * item.quantity).toLocaleString('id-ID')}</span>
                                                        </div>
                                                    `).join('')}
                    <p class="flex justify-between font-bold mt-2">
                        <span>Total ${person.person}:</span>
                        <span>Rp${person.items.reduce((sum, item) => sum + (item.price * item.quantity), 0).toLocaleString('id-ID')}</span>
                    </p>
                </div>
            `).join('');
                        document.getElementById('orderItems').innerHTML = orderItemsHTML;
                        const total = cart.reduce((acc, person) => acc + person.items.reduce((sum, item) => sum + (parseFloat(item
                            .price) * item.quantity), 0), 0);
                        document.getElementById('modalTotal').textContent = `Rp${total.toLocaleString('id-ID')}`;
                        document.getElementById('cartDropdown').classList.add('hidden');
                        document.getElementById('mobileCartPanel')?.classList.add('hidden');
                        document.getElementById('checkoutModal').classList.remove('hidden');
                        document.getElementById('mobileCartDropdown').classList.add('hidden');
                        refreshTableStatus();
                        refreshInterval = setInterval(() => {
                            if (!document.getElementById('checkoutModal').classList.contains('hidden')) {
                                refreshTableStatus();
                            } else {
                                clearInterval(refreshInterval);
                            }
                        }, 5000);
                    }

                    function closeCheckoutModal() {
                        document.getElementById('checkoutModal').classList.add('hidden');
                        clearInterval(refreshInterval);
                    }

                    function closePaymentModal() {
                        document.getElementById('paymentConfirmationModal').classList.add('hidden');
                    }

                    function copyToClipboard(element) {
                        const text = element.querySelector('span').innerText;
                        try {
                            navigator.clipboard.writeText(text).then(() => {
                                element.style.backgroundColor = '#DCFCE7';
                                setTimeout(() => {
                                    element.style.backgroundColor = '#F3F4F6';
                                }, 1000);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Tersalin!',
                                    text: 'Nomor rekening berhasil disalin',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            });
                        } catch (err) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal menyalin',
                                text: 'Browser tidak mendukung clipboard API',
                            });
                        }
                    }

                    function showPaymentDetails() {
                        const providerId = document.getElementById('paymentMethod').value;
                        const provider = paymentProviders.find(p => p.id == providerId);
                        const paymentDetails = document.getElementById('paymentDetails');
                        if (provider) {
                            paymentDetails.classList.remove('hidden');
                            document.getElementById('providerLogo').src = provider.logo;
                            document.getElementById('providerName').textContent = provider.name;
                            document.getElementById('providerType').textContent = provider.type;
                            document.getElementById('accountNumber').textContent = provider.account_number;
                            document.getElementById('accountName').textContent = provider.account_name;
                            document.getElementById('instructions').textContent = provider.instructions;
                        } else {
                            paymentDetails.classList.add('hidden');
                        }
                    }

                    function toggleTakeAway(isTakeAway) {
                        const tableSelect = document.getElementById('tableNumber');
                        const statusMessage = document.getElementById('tableStatusMessage');
                        if (isTakeAway) {
                            tableSelect.value = '';
                            tableSelect.disabled = true;
                            statusMessage.innerHTML = '<span class="text-blue-500">ℹ️ Take Away dipilih</span>';
                        } else {
                            tableSelect.disabled = false;
                            tableSelect.value = '';
                            statusMessage.innerHTML = '';
                        }
                    }

                    async function processPayment(e) {
                        e.preventDefault();
                        const tableNumber = document.getElementById('tableNumber').value;
                        const isTakeAway = document.getElementById('takeAwayToggle').checked;
                        if (!tableNumber && !isTakeAway) {
                            Swal.fire('Error!', 'Harap pilih meja atau Take Away', 'error');
                            return;
                        }
                        const totalPrice = cart.reduce((acc, person) => acc + person.items.reduce((sum, item) => sum + (item.price *
                            item.quantity), 0), 0);
                        const orderData = {
                            table_id: isTakeAway ? 'takeaway' : tableNumber,
                            items: cart,
                            total_price: totalPrice
                        };
                        sessionStorage.setItem('pendingOrder', JSON.stringify(orderData));
                        const paymentOrderItems = document.getElementById('paymentOrderItems');
                        paymentOrderItems.innerHTML = cart.map(person => `
                <div class="border-b pb-2 mb-2">
                    <p class="font-bold">${person.person}</p>
                    ${person.items.map(item => `
                                                        <div class="flex justify-between">
                                                            <span>${item.name} (Qty: ${item.quantity})</span>
                                                            <span>Rp${(item.price * item.quantity).toLocaleString('id-ID')}</span>
                                                        </div>
                                                    `).join('')}
                    <p class="flex justify-between font-bold mt-2">
                        <span>Total ${person.person}:</span>
                        <span>Rp${person.items.reduce((sum, item) => sum + (item.price * item.quantity), 0).toLocaleString('id-ID')}</span>
                    </p>
                </div>
            `).join('');
                        document.getElementById('paymentTotal').textContent = `Rp${totalPrice.toLocaleString('id-ID')}`;
                        closeCheckoutModal();
                        document.getElementById('paymentConfirmationModal').classList.remove('hidden');
                    }

                    window.addEventListener('beforeunload', function(e) {
                        const modal = document.getElementById('orderConfirmationModal');
                        if (modal && !modal.classList.contains('hidden')) {
                            const transactionId = document.getElementById('printButton')?.dataset.transactionId;
                            const status = document.querySelector('.status-text')?.textContent;
                            if (transactionId && status) {
                                sessionStorage.setItem('pendingOrderStatus', JSON.stringify({
                                    showModal: true,
                                    transactionId: transactionId,
                                    status: status,
                                    timestamp: Date.now()
                                }));
                            }
                        }
                    });

                    async function submitPayment() {
                        const paymentProof = document.getElementById('paymentProof').files[0];
                        const providerId = document.getElementById('paymentMethod').value;
                        const orderData = JSON.parse(sessionStorage.getItem('pendingOrder'));

                        if (!providerId) {
                            Swal.fire('Error!', 'Harap pilih metode pembayaran', 'error');
                            return;
                        }
                        if (!paymentProof) {
                            Swal.fire('Error!', 'Harap upload bukti pembayaran', 'error');
                            return;
                        }
                        if (!paymentProof.type.startsWith('image/')) {
                            Swal.fire('Error!', 'File harus berupa gambar', 'error');
                            return;
                        }

                        const formData = new FormData();
                        formData.append('payment_proof', paymentProof);
                        formData.append('provider_id', providerId);
                        formData.append('order_data', JSON.stringify({
                            table_id: orderData.table_id,
                            total_price: orderData.total_price,
                            items: orderData.items
                        }));

                        try {
                            const response = await fetch('/confirm-payment', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                },
                                body: formData
                            });
                            const result = await response.json();
                            if (result.success) {
                                // Simpan data transaksi di session storage sebelum refresh
                                const transactionData = {
                                    showModal: true,
                                    transactionId: result.transactionId,
                                    status: result.status,
                                    timestamp: Date.now()
                                };
                                sessionStorage.setItem('pendingOrderStatus', JSON.stringify(transactionData));

                                // Hapus data pesanan lama
                                sessionStorage.removeItem('pendingOrder');

                                // Refresh halaman
                                location.reload();

                                // Catatan: Modal akan ditampilkan oleh fungsi checkPendingOrder setelah refresh
                            } else {
                                throw new Error(result.message);
                            }
                        } catch (error) {
                            console.error('Payment error:', error);
                            Swal.fire('Error!', error.message, 'error');
                        }
                    }

                    function checkTableStatus(tableId) {
                        const select = document.getElementById('tableNumber');
                        const selectedOption = select.querySelector(`option[value="${tableId}"]`);
                        const statusMessage = document.getElementById('tableStatusMessage');
                        if (tableId === 'takeaway') {
                            statusMessage.innerHTML = '<span class="text-blue-500">ℹ️ Tidak memerlukan meja untuk Take Away</span>';
                            return;
                        }
                        if (selectedOption) {
                            if (selectedOption.disabled) {
                                statusMessage.innerHTML = '<span class="text-red-500">⛔ Meja sedang digunakan!</span>';
                                select.value = '';
                            } else {
                                statusMessage.innerHTML = '<span class="text-green-500">✅ Meja tersedia</span>';
                            }
                        } else {
                            statusMessage.innerHTML = '';
                        }
                    }

                    let refreshInterval;

                    async function refreshTableStatus() {
                        try {
                            const response = await fetch('/tables');
                            const tables = await response.json();
                            const select = document.getElementById('tableNumber');
                            const currentValue = select.value;
                            select.innerHTML = `<option value="">Pilih Meja</option>`;
                            tables.forEach(table => {
                                const option = new Option(
                                    `Meja ${table.number} - ${table.status === 'occupied' ? '(Terisi)' : '(Tersedia)'}`,
                                    table.id);
                                option.dataset.status = table.status;
                                option.disabled = table.status.toLowerCase() === 'occupied';
                                select.appendChild(option);
                            });
                            select.value = currentValue;
                            if (currentValue) checkTableStatus(currentValue);
                        } catch (error) {
                            console.error('Gagal memperbarui status meja:', error);
                        }
                    }

                    function showOrderModal(status, transactionId) {
                        showModal('orderConfirmationModal');
                        try {
                            const modal = document.getElementById('orderConfirmationModal');
                            if (!modal) return;
                            modal.classList.remove('hidden');
                            modal.classList.add('show');
                            const statusElement = modal.querySelector('.status-text');
                            const printBtn = modal.querySelector('#printButton');
                            if (!statusElement || !printBtn) {
                                throw new Error('Modal elements not found');
                            }
                            statusElement.textContent = status;
                            statusElement.className = `font-bold ${status === 'proses' ? 'text-green-500' : 'text-yellow-500'}`;
                            printBtn.style.display = status === 'proses' ? 'block' : 'none';
                            printBtn.dataset.transactionId = transactionId;
                            modal.classList.remove('hidden');
                            modal.style.display = 'flex';
                            window.scrollTo({
                                top: 0,
                                behavior: 'smooth'
                            });
                        } catch (error) {
                            console.error('Error showing modal:', error);
                        }
                    }

                    function printOrder() {
                        try {
                            const transactionId = document.getElementById('printButton').dataset.transactionId;
                            if (!transactionId) throw new Error('ID Transaksi tidak valid');
                            fetch(`/transaksi/print/${transactionId}`)
                                .then(response => {
                                    if (!response.ok) throw new Error('Gagal memuat data cetakan');
                                    return response.text();
                                })
                                .then(html => {
                                    const printWindow = window.open('', '_blank');
                                    if (!printWindow || printWindow.closed) {
                                        throw new Error('Pop-up diblokir. Izinkan pop-up untuk mencetak');
                                    }
                                    printWindow.document.write(html);
                                    printWindow.document.close();
                                    setTimeout(() => {
                                        printWindow.print();
                                        printWindow.onafterprint = () => {
                                            sessionStorage.removeItem('pendingOrderStatus');
                                            printWindow.close();
                                        };
                                    }, 500);
                                })
                                .catch(error => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal Mencetak',
                                        html: `Silakan coba lagi atau <a href="/transaksi/print/${transactionId}" target="_blank" class="text-blue-500">buka halaman cetak</a>`,
                                    });
                                });
                        } catch (error) {
                            console.error('Print error:', error);
                            Swal.fire('Error!', error.message, 'error');
                        }
                    }

                    function showPersistedOrderModal(status, transactionId) {
                        const modal = document.getElementById('orderConfirmationModal');
                        if (!modal) return;
                        const statusElement = modal.querySelector('.status-text');
                        const printBtn = document.getElementById('printButton');
                        if (statusElement && printBtn) {
                            statusElement.textContent = status;
                            statusElement.className = `font-bold ${status === 'proses' ? 'text-green-500' : 'text-yellow-500'}`;
                            printBtn.style.display = status === 'proses' ? 'block' : 'none';
                            printBtn.dataset.transactionId = transactionId;
                            modal.classList.remove('hidden');
                        }
                    }

                    function showModal(modalId) {
                        document.getElementById(modalId).classList.remove('hidden');
                        document.getElementById('mobileCartDropdown').classList.add('hidden');
                    }

                    async function checkPendingOrder() {
                        try {
                            const pendingOrderStr = sessionStorage.getItem('pendingOrderStatus');
                            if (!pendingOrderStr) return;
                            const pendingOrder = JSON.parse(pendingOrderStr);
                            if (!pendingOrder || !pendingOrder.showModal) return;
                            const oneHourAgo = Date.now() - 3600000;
                            if (pendingOrder.timestamp > oneHourAgo) {
                                try {
                                    const response = await fetch(`/transactions/${pendingOrder.transactionId}/status`);
                                    if (!response.ok) throw new Error('Failed to fetch status');
                                    const data = await response.json();
                                    const currentStatus = data.status.toLowerCase();
                                    const updatedOrder = {
                                        ...pendingOrder,
                                        status: currentStatus,
                                        timestamp: Date.now()
                                    };
                                    sessionStorage.setItem('pendingOrderStatus', JSON.stringify(updatedOrder));
                                    showPersistedOrderModal(currentStatus, pendingOrder.transactionId);
                                    if (!['pending', 'proses'].includes(currentStatus)) {
                                        sessionStorage.removeItem('pendingOrderStatus');
                                    }
                                } catch (error) {
                                    console.error('Gagal memperbarui status:', error);
                                    showPersistedOrderModal(pendingOrder.status, pendingOrder.transactionId);
                                }
                            } else {
                                sessionStorage.removeItem('pendingOrderStatus');
                            }
                        } catch (error) {
                            console.error('Error checking pending order:', error);
                            sessionStorage.removeItem('pendingOrderStatus');
                        }
                    }

                    function previewImage(event) {
                        const input = event.target;
                        const container = document.getElementById('imagePreviewContainer');
                        const preview = document.getElementById('imagePreview');
                        const uploadLabel = document.getElementById('uploadLabel');
                        if (input.files && input.files[0]) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                preview.src = e.target.result;
                                container.classList.remove('hidden');
                                uploadLabel.style.display = 'none';
                            }
                            reader.readAsDataURL(input.files[0]);
                        }
                    }

                    function showImageModal(src) {
                        const modal = document.getElementById('imageModal');
                        const modalImage = document.getElementById('modalImage');
                        modalImage.src = src;
                        modal.classList.remove('hidden');
                    }

                    function closeImageModal() {
                        document.getElementById('imageModal').classList.add('hidden');
                    }

                    function removeImagePreview() {
                        document.getElementById('paymentProof').value = '';
                        document.getElementById('imagePreviewContainer').classList.add('hidden');
                        document.getElementById('imagePreview').src = '';
                        document.getElementById('uploadLabel').style.display = 'block';
                    }

                    function closeImageModalIfBackground(event) {
                        if (event.target.id === 'imageModal') {
                            closeImageModal();
                        }
                    }

                    function showTableInfoAlert() {
                        Swal.fire({
                            title: 'Informasi Meja',
                            text: 'Jika meja yang Anda tempati kosong tetapi tidak bisa di pilih dalam website, silakan hubungi kasir.',
                            icon: 'info',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#ef4444',
                            customClass: {
                                popup: 'rounded-xl',
                                title: 'text-lg font-bold text-gray-800',
                                content: 'text-sm text-gray-600',
                                confirmButton: 'px-4 py-2 rounded-md'
                            }
                        });
                    }
                </script>
                </body>
                </html>
                </x-app-layout>)
