<!-- resources/views/components/shared/header.blade.php -->
<header class="bg-white shadow-md sticky top-0 z-40" x-data="headerData()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-900">
                    <i class="fa-solid fa-store mr-2 text-blue-600"></i>
                    Toko Online
                </a>
            </div>

            <!-- Navigation -->
            <nav class="hidden md:flex space-x-8">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600 transition duration-200">
                    <i class="fa-solid fa-home mr-1"></i>
                    Beranda
                </a>
                <a href="#categories" class="text-gray-700 hover:text-blue-600 transition duration-200">
                    <i class="fa-solid fa-th-large mr-1"></i>
                    Kategori
                </a>
                <a href="#products" class="text-gray-700 hover:text-blue-600 transition duration-200">
                    <i class="fa-solid fa-box mr-1"></i>
                    Produk
                </a>
            </nav>

            <!-- Right side buttons -->
            <div class="flex items-center space-x-4">
                <!-- Cart Button -->
                @auth
                    <button type="button" 
                            @click="toggleCart()"
                            class="relative p-2 text-gray-700 hover:text-blue-600 transition duration-200">
                        <i class="fa-solid fa-shopping-cart text-xl"></i>
                        <span x-show="cartCount > 0" 
                              x-text="cartCount"
                              class="cart-badge absolute -top-2 -right-2 bg-red-500 text-white rounded-full text-xs px-2 py-1 min-w-[20px] text-center">
                        </span>
                    </button>
                @else
                    <a href="{{ route('login') }}" 
                       class="p-2 text-gray-700 hover:text-blue-600 transition duration-200">
                        <i class="fa-solid fa-shopping-cart text-xl"></i>
                    </a>
                @endauth

                <!-- User Menu -->
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="flex items-center space-x-2 text-gray-700 hover:text-blue-600 transition duration-200">
                            <i class="fa-solid fa-user"></i>
                            <span class="hidden sm:inline">{{ Auth::user()->name }}</span>
                            <i class="fa-solid fa-chevron-down text-sm"></i>
                        </button>
                        
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            <a href="{{ route('profile.edit') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fa-solid fa-user-edit mr-2"></i>
                                Profil
                            </a>
                            @if(Auth::user()->is_admin)
                                <a href="{{ route('admin.dashboard.index') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fa-solid fa-tachometer-alt mr-2"></i>
                                    Dashboard Admin
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fa-solid fa-sign-out-alt mr-2"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('login') }}" 
                           class="text-gray-700 hover:text-blue-600 transition duration-200">
                            <i class="fa-solid fa-sign-in-alt mr-1"></i>
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                            <i class="fa-solid fa-user-plus mr-1"></i>
                            Daftar
                        </a>
                    </div>
                @endauth

                <!-- Mobile menu button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" 
                        class="md:hidden p-2 text-gray-700 hover:text-blue-600">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- Mobile menu -->
        <div x-show="mobileMenuOpen" 
             @click.away="mobileMenuOpen = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="md:hidden border-t border-gray-200 bg-white">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('home') }}" 
                   class="block px-3 py-2 text-gray-700 hover:text-blue-600 transition duration-200">
                    <i class="fa-solid fa-home mr-2"></i>
                    Beranda
                </a>
                <a href="#categories" 
                   class="block px-3 py-2 text-gray-700 hover:text-blue-600 transition duration-200">
                    <i class="fa-solid fa-th-large mr-2"></i>
                    Kategori
                </a>
                <a href="#products" 
                   class="block px-3 py-2 text-gray-700 hover:text-blue-600 transition duration-200">
                    <i class="fa-solid fa-box mr-2"></i>
                    Produk
                </a>
            </div>
        </div>
    </div>

    <!-- Cart Sidebar -->
    @auth
        <div x-show="cartOpen" 
             @click.away="cartOpen = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform translate-x-full"
             x-transition:enter-end="transform translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="transform translate-x-0"
             x-transition:leave-end="transform translate-x-full"
             class="fixed inset-y-0 right-0 w-80 bg-white shadow-xl z-50 overflow-y-auto">
            
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fa-solid fa-shopping-cart mr-2"></i>
                        Keranjang Belanja
                    </h2>
                    <button @click="cartOpen = false" 
                            class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="p-4">
                <div x-show="cartItems.length === 0" class="text-center py-8">
                    <i class="fa-solid fa-shopping-cart text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Keranjang belanja kosong</p>
                </div>

                <div x-show="cartItems.length > 0">
                    <div class="space-y-4 mb-4">
                        <template x-for="item in cartItems" :key="item.id">
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <img :src="item.product.images[0]?.path_url || '/default-product.jpg'" 
                                         :alt="item.product.name"
                                         class="w-12 h-12 object-cover rounded">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900" x-text="item.product.name"></p>
                                    <p class="text-sm text-gray-500" x-text="formatPrice(item.price)"></p>
                                    <p class="text-xs text-gray-400" x-text="'Qty: ' + item.quantity"></p>
                                </div>
                                <div class="flex-shrink-0">
                                    <p class="text-sm font-semibold text-gray-900" x-text="formatPrice(item.subtotal)"></p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-lg font-semibold text-gray-900">Total:</span>
                            <span class="text-lg font-bold text-blue-600" x-text="formatPrice(cartTotal)"></span>
                        </div>
                        
                        <button class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-200">
                            <i class="fa-solid fa-credit-card mr-2"></i>
                            Checkout
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cart overlay -->
        <div x-show="cartOpen" 
             @click="cartOpen = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-red-500 bg-opacity-50 z-40"></div>
    @endauth
</header>

<script>
    function headerData() {
        return {
            mobileMenuOpen: false,
            cartOpen: false,
            cartItems: [],
            cartCount: 0,
            cartTotal: 0,
            
            init() {
                @auth
                    this.loadCartSummary();
                @endauth
            },
            
            toggleCart() {
                this.cartOpen = !this.cartOpen;
                if (this.cartOpen) {
                    this.loadCartSummary();
                }
            },
            
            async loadCartSummary() {
                try {
                    const response = await fetch('{{ route("cart.summary") }}');
                    const data = await response.json();
                    
                    if (data.success) {
                        this.cartItems = data.cart_summary.items || [];
                        this.cartCount = data.cart_summary.total_quantity || 0;
                        this.cartTotal = data.cart_summary.total_price || 0;
                    }
                } catch (error) {
                    console.error('Error loading cart summary:', error);
                }
            },
            
            formatPrice(price) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(price);
            }
        }
    }
</script>