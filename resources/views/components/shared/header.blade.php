<header class="bg-white shadow-md sticky top-0 z-40" x-data="headerData()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-900">
                    <i class="fa-solid fa-store mr-2 text-blue-600"></i>
                    Toko Online
                </a>
            </div>
            <nav class="hidden md:flex space-x-8">
                <a href="{{ route('categories.index') }}" class="text-gray-700 hover:text-blue-600 transition duration-200">
                    <i class="fa-solid fa-th-large mr-1"></i>
                    Kategori
                </a>
                <a href="{{ route('products.index') }} " class="text-gray-700 hover:text-blue-600 transition duration-200">
                    <i class="fa-solid fa-box mr-1"></i>
                    Produk
                </a>
            </nav>

            <div class="flex items-center space-x-4">
                @auth
                    @if(Auth::user()->isUser())
                        <button type="button" 
                                @click="toggleCart()"
                                class="relative p-2 text-gray-700 hover:text-blue-600 transition duration-200">
                            <i class="fa-solid fa-shopping-cart text-xl"></i>
                            <span x-show="cartItems.length > 0" 
                                x-text="cartItems.length"
                                class="cart-badge absolute -top-2 -right-2 bg-red-500 text-white rounded-full text-xs px-2 py-1 min-w-[20px] text-center">
                            </span>
                        </button>
                    @endif
                    
                @endauth

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
                            
                            @if(Auth::user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fa-solid fa-tachometer-alt mr-2"></i>
                                    Dashboard
                                </a>
                                <a href="{{ route('admin.products.index') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fa-solid fa-box mr-2"></i>
                                    Products
                                </a>
                                <a href="{{ route('admin.orders.index') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fa-solid fa-shopping-bag mr-2"></i>
                                    Orders
                                </a>
                                <a href="{{ route('admin.transactions.index') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fa-solid fa-credit-card mr-2"></i>
                                    Transactions
                                </a>
                                <a href="{{ route('admin.categories.index') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fa-solid fa-th-large mr-2"></i>
                                    Categories
                                </a>
                            @else
                                <a href="{{ route('profile.show') }}" 
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fa-solid fa-user-edit mr-2"></i>
                                    Profil
                                </a>
                                <a href="{{ route('profile.addresses.index') }}" 
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fa-solid fa-map-marker-alt mr-2"></i>
                                    Kelola Alamat
                                </a>
                                <a href="{{ route('orders.index') }}" 
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fa-solid fa-shopping-bag mr-2"></i>
                                    Orders
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
                    </div>
                @endauth
                @auth
                    @if(Auth::user()->isUser())
                        <button @click="mobileMenuOpen = !mobileMenuOpen" 
                                class="md:hidden p-2 text-gray-700 hover:text-blue-600">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                    @endif
                @endauth
            </div>
        </div>
        
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
                <a href="{{ route('categories.index') }}" 
                    class="block px-3 py-2 text-gray-700 hover:text-blue-600 transition duration-200">
                    <i class="fa-solid fa-th-large mr-2"></i>
                    Kategori
                </a>
                <a href="{{ route('products.index') }}" 
                    class="block px-3 py-2 text-gray-700 hover:text-blue-600 transition duration-200">
                    <i class="fa-solid fa-box mr-2"></i>
                    Produk
                </a>
            </div>
        </div>
    </div>

    @auth
        <div x-show="cartOpen" 
             @click.away="cartOpen = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform translate-x-full"
             x-transition:enter-end="transform translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="transform translate-x-0"
             x-transition:leave-end="transform translate-x-full"
             class="fixed inset-y-0 right-0 sm:max-w-96 w-full bg-white shadow-xl z-50 overflow-y-auto">
            
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
                <div class="flex items-center justify-between mb-4 p-3 bg-gray-100 rounded-lg">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                            x-model="selectAll"
                            @change="toggleSelectAll()"
                            class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm font-medium text-gray-900">Pilih Semua</span>
                    </label>
                    <span class="text-xs text-gray-500" x-text="selectedItems.length + ' dari ' + cartItems.length + ' item'"></span>
                </div>

                <div class="space-y-4 mb-4">
                    <template x-for="item in cartItems" :key="item.id">
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <input type="checkbox" 
                                x-model="selectedItems"
                                :value="item.id"
                                class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                            
                            <div class="flex-shrink-0">
                                <img :src="item.product.images[0]?.path || '/default-product.jpg'" 
                                    :alt="item.product.name"
                                    class="w-12 h-12 object-cover rounded">
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900" x-text="item.product.name"></p>
                                <p class="text-sm text-gray-500" x-text="formatPrice(item.price)"></p>
                                
                                <div class="flex items-center space-x-2 mt-2">
                                    <button type="button" 
                                            @click="updateQuantity(item.id, item.quantity - 1)"
                                            :disabled="item.quantity <= 1"
                                            class="w-6 h-6 flex items-center justify-center text-gray-500 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed border border-gray-300 rounded">
                                        <i class="fa-solid fa-minus text-xs"></i>
                                    </button>
                                    
                                    <div class="relative min-w-[30px] text-center">
                                        <span class="text-sm font-medium text-gray-900" 
                                              x-text="item.quantity"
                                              :class="{ 'opacity-50': loadingItems[item.id] }"></span>
                                    </div>
                                    
                                    <button type="button" 
                                            @click="updateQuantity(item.id, item.quantity + 1)"
                                            :disabled="item.quantity >= item.product.stock"
                                            class="w-6 h-6 flex items-center justify-center text-gray-500 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed border border-gray-300 rounded">
                                        <i class="fa-solid fa-plus text-xs"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="flex-shrink-0 text-right">
                                <p class="text-sm font-semibold text-gray-900" x-text="formatPrice(item.price * item.quantity)"></p>
                                <button type="button" 
                                        @click="removeFromCart(item.id)"
                                        class="text-red-500 hover:text-red-700 text-xs mt-1">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-lg font-semibold text-gray-900">Total Terpilih:</span>
                        <span class="text-lg font-bold text-blue-600" x-text="formatPrice(selectedTotal)"></span>
                    </div>
                
                <div class="mt-4 mb-4">
                    <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-2">Pilih Alamat Pengiriman:</label>
                    
                    <div x-show="addresses.length > 0">
                        <select name="shipping_address" id="shipping_address" x-model="selectedAddressId" @change="calculateShippingCost()"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">-- Pilih Alamat --</option>
                            <template x-for="address in addresses" :key="address.id">
                                <option :value="address.id" x-text="address.label ?? '' + ' - ' + address.city"></option>
                            </template>
                        </select>
                    </div>
                    
                    <div x-show="addresses.length === 0" class="text-center py-4">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <i class="fa-solid fa-exclamation-triangle text-yellow-600 text-xl mb-2"></i>
                            <p class="text-sm text-yellow-800 mb-3">Anda belum memiliki alamat pengiriman</p>
                            <a href="{{ route('profile.addresses.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition duration-200">
                                <i class="fa-solid fa-plus mr-2"></i>
                                Tambah Alamat
                            </a>
                        </div>
                    </div>
                    
                    <div x-show="shippingCostLoading" class="mt-2">
                        <i class="fa-solid fa-spinner fa-spin"></i> Menghitung ongkir...
                    </div>
                    <div x-show="shippingCost !== null && !shippingCostLoading" class="mt-2 text-sm text-gray-600">
                        Ongkos Kirim: <span class="font-semibold" x-text="formatPrice(shippingCost)"></span>
                    </div>
                    <div x-show="shippingError" class="mt-2 text-sm text-red-500" x-text="shippingError"></div>
                </div>
                
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-base text-gray-900">Subtotal:</span>
                        <span class="text-base font-semibold text-gray-900" x-text="formatPrice(selectedTotal)"></span>
                    </div>
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-base text-gray-900">Ongkir:</span>
                        <span class="text-base font-semibold text-gray-900" x-text="shippingCost !== null ? formatPrice(shippingCost) : 'Rp 0'"></span>
                    </div>
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-lg font-semibold text-gray-900">Total:</span>
                        <span class="text-lg font-bold text-blue-600" x-text="formatPrice(grandTotal)"></span>
                    </div>
                    <button :disabled="selectedItems.length === 0 || checkoutLoading || selectedAddressId === '' || shippingCost === null || addresses.length === 0" 
                            @click="checkout()"
                            class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200">
                            <template x-if="checkoutLoading">
                                <span>
                                    <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                                    Processing...
                                </span>
                            </template>
                            <template x-if="!checkoutLoading && addresses.length > 0">
                                <span>
                                    <i class="fa-solid fa-credit-card mr-2"></i>
                                    Checkout (<span x-text="selectedItems.length"></span> item)
                                </span>
                            </template>
                            <template x-if="!checkoutLoading && addresses.length === 0">
                                <span>
                                    <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                                    Tambah Alamat Terlebih Dahulu
                                </span>
                            </template>
                    </button>
                </div>
            </div>
            </div>
            </div>
        </div>

        <div x-show="cartOpen" 
             @click="cartOpen = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/50 bg-opacity-50 z-40"></div>
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
        selectedItems: [], 
        selectAll: false,
        checkoutLoading: false,
        updateTimers: {},
        loadingItems: {},
        addresses: [], 
        selectedAddressId: '', 
        shippingCost: null, 
        shippingCostLoading: false, 
        shippingError: '', 

        
        init() {
            @auth
                this.loadCartSummary();
                this.loadAddresses();
            @endauth
        },
        get grandTotal() {
            const sub = this.selectedTotal || 0;
            const ship = this.shippingCost || 0;
            return sub + ship;
        },
        async loadAddresses() {
            try {
                const response = await fetch('{{ route("profile.addresses.json") }}'); 
                const data = await response.json();
                this.addresses = data.addresses || [];
                
                if (this.addresses.length > 0) {
                    const defaultAddress = this.addresses.find(addr => addr.is_default);
                    if (defaultAddress) {
                        this.selectedAddressId = defaultAddress.id;
                        this.calculateShippingCost();
                    }
                }
            } catch (error) {
                console.error('Gagal memuat alamat:', error);
                this.addresses = [];
            }
        },
        async calculateShippingCost() {
            if (!this.selectedAddressId || this.addresses.length === 0) {
                this.shippingCost = null;
                this.shippingError = '';
                return;
            }

            this.shippingCostLoading = true;
            this.shippingError = '';
            this.shippingCost = null;

            try {
                const response = await fetch('{{ route("shipping.calculate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ address_id: this.selectedAddressId })
                });

                const data = await response.json();

                if (data.success) {
                    this.shippingCost = data.cost;
                } else {
                    this.shippingError = data.message || 'Gagal menghitung ongkir.';
                }

            } catch (error) {
                console.error('Error calculating shipping:', error);
                this.shippingError = 'Tidak dapat terhubung ke server.';
            } finally {
                this.shippingCostLoading = false;
            }
        },
        get selectedTotal() {
            
            return this.cartItems
                .filter(item => this.selectedItems.includes(item.id.toString()))
                .reduce((total, item) => {
                    const itemTotal = parseFloat(item.price) * parseInt(item.quantity);
                    return total + itemTotal;
                }, 0);
        },
        
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedItems = this.cartItems.map(item => item.id.toString());
            } else {
                this.selectedItems = [];
            }
        },
        
        toggleCart() {
            this.cartOpen = !this.cartOpen;
            if (this.cartOpen) {
                this.loadCartSummary();
            }
        },
        
        async updateQuantity(itemId, newQuantity) {
            if (newQuantity <= 0) {
                await this.removeFromCart(itemId);
                return;
            }
            
            const item = this.cartItems.find(i => i.id === itemId);
            if (!item) return;
            
            item.quantity = newQuantity;
            
            this.loadingItems[itemId] = true;
            
            if (this.updateTimers[itemId]) {
                clearTimeout(this.updateTimers[itemId]);
            }
            
            this.updateTimers[itemId] = setTimeout(async () => {
                try {
                    const response = await fetch('{{ route("cart.update") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            product_id: item.product_id,
                            quantity: newQuantity
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        await this.loadCartSummary();
                    } else {
                        console.error('Update failed:', data.message);
                        await this.loadCartSummary();
                    }
                } catch (error) {
                    console.error('Error updating quantity:', error);
                    await this.loadCartSummary();
                } finally {
                    delete this.loadingItems[itemId];
                    delete this.updateTimers[itemId];
                }
            }, 800); 
        },
        
        async removeFromCart(itemId) {
            const item = this.cartItems.find(i => i.id === itemId);
            if (!item) return;
            
            try {
                const response = await fetch('{{ route("cart.remove") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        product_id: item.product_id
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.selectedItems = this.selectedItems.filter(id => id !== itemId);
                    await this.loadCartSummary();
                }
            } catch (error) {
                console.error('Error removing item:', error);
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
                    
                    this.selectedItems = this.selectedItems.filter(id => 
                        this.cartItems.some(item => item.id.toString() === id)
                    );
                    
                    this.selectAll = this.cartItems.length > 0 && this.selectedItems.length === this.cartItems.length;
                    
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
        },
        
        async checkout() {
            if (this.selectedItems.length === 0) {
                alert('Pilih minimal 1 item untuk checkout');
                return;
            }
            
            if (this.addresses.length === 0) {
                alert('Anda belum memiliki alamat pengiriman. Silakan tambah alamat terlebih dahulu.');
                return;
            }
            
            if (!this.selectedAddressId) {
                alert('Silakan pilih alamat pengiriman terlebih dahulu.');
                return;
            }
            
            if (this.checkoutLoading) return; 
            
            this.checkoutLoading = true;
            
            try {
                const response = await fetch('{{ route("checkout") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        selected_items: this.selectedItems.map(id => Number(id)),
                        address_id: this.selectedAddressId 
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    alert(data.message || 'Terjadi kesalahan saat checkout');
                    this.checkoutLoading = false; 
                }
            } catch (error) {
                console.error('Error during checkout:', error);
                alert('Terjadi kesalahan saat checkout');
                this.checkoutLoading = false; 
            }
        }

    }
}
</script>