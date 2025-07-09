    <x-layouts.plain-app>
        <x-slot:title>Semua Produk - Toko Online</x-slot:title>
        
        <div class="min-h-screen bg-gray-50">
            <div class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Semua Produk</h1>
                            <p class="text-gray-600 mt-1">Temukan produk terbaik untuk kebutuhan Anda</p>
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $products->total() }} produk ditemukan
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex flex-col lg:flex-row gap-8">
                    <div class="w-full lg:w-1/4">
                        <div class="bg-white rounded-lg shadow-sm p-6 sticky top-20">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Produk</h3>
                            
                            <form method="GET" action="{{ route('products.index') }}" class="space-y-6">
                                <div>
                                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                                        Cari Produk
                                    </label>
                                    <input type="text" 
                                        id="search" 
                                        name="search" 
                                        value="{{ $search }}"
                                        placeholder="Nama produk..."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                        Kategori
                                    </label>
                                    <select id="category" 
                                            name="category" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Semua Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }} ({{ $category->products->count() }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Rentang Harga
                                    </label>
                                    <div class="flex items-center space-x-2">
                                        <input type="number" 
                                            name="min_price" 
                                            value="{{ $minPrice }}"
                                            placeholder="Min"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <span class="text-gray-500">-</span>
                                        <input type="number" 
                                            name="max_price" 
                                            value="{{ $maxPrice }}"
                                            placeholder="Max"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>

                                <div>
                                    <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-2">
                                        Urutkan
                                    </label>
                                    <select id="sort_by" 
                                            name="sort_by" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="latest" {{ $sortBy == 'latest' ? 'selected' : '' }}>Terbaru</option>
                                        <option value="price_low" {{ $sortBy == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                                        <option value="price_high" {{ $sortBy == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                                        <option value="rating_high" {{ $sortBy == 'rating_high' ? 'selected' : '' }}>Rating Tertinggi</option>
                                        <option value="rating_low" {{ $sortBy == 'rating_low' ? 'selected' : '' }}>Rating Terendah</option>
                                        <option value="name" {{ $sortBy == 'name' ? 'selected' : '' }}>Nama A-Z</option>
                                    </select>
                                </div>

                                <div class="flex space-x-3">
                                    <button type="submit" 
                                            class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200">
                                        <i class="fa-solid fa-filter mr-2"></i>
                                        Filter
                                    </button>
                                    <a href="{{ route('products.index') }}" 
                                    class="flex-1 bg-gray-100 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-200 transition duration-200 text-center">
                                        <i class="fa-solid fa-refresh mr-2"></i>
                                        Reset
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="flex-1">
                        @if($products->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" x-data="productGrid()">
                                @foreach($products as $product)
                                    <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition duration-200 group">
                                        <div class="aspect-square bg-gray-200 overflow-hidden relative">
                                            <a href="{{ route('products.show', $product->slug) }}">
                                                @if($product->images->first())
                                                    <img src="{{ $product->images->first()->path_url }}" 
                                                        alt="{{ $product->name }}"
                                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-200">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                                        <i class="fa-solid fa-image text-4xl text-gray-400"></i>
                                                    </div>
                                                @endif
                                            </a>
                                            
                                            @if($product->discount_price)
                                                <div class="absolute top-3 left-3 bg-red-500 text-white px-2 py-1 rounded-md text-sm font-semibold">
                                                    -{{ number_format((($product->price - $product->discount_price) / $product->price) * 100, 0) }}%
                                                </div>
                                            @endif
                                            
                                            @if($product->stock <= 0)
                                                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                                    <span class="text-white font-semibold">Stok Habis</span>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="p-4">
                                            <div class="mb-2">
                                                <a href="{{ route('categories.show', $product->category->slug) }}" 
                                                class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded hover:bg-blue-100 transition duration-200">
                                                    {{ $product->category->name }}
                                                </a>
                                            </div>
                                            
                                            <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                                                <a href="{{ route('products.show', $product->slug) }}" 
                                                class="hover:text-blue-600 transition duration-200">
                                                    {{ $product->name }}
                                                </a>
                                            </h3>
                                            
                                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $product->description }}</p>
                                            <div class="flex items-center mb-3">
                                                <div class="flex items-center">
                                                    @if($product->rating_count > 0)
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fa-solid fa-star text-sm {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                        @endfor
                                                        <span class="text-sm text-gray-600 ml-2">
                                                            {{ number_format($product->average_rating, 1) }}/5 
                                                            ({{ $product->rating_count }} ulasan)
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="flex flex-col">
                                                    @if($product->discount_price)
                                                        <span class="text-lg font-bold text-gray-900">Rp {{ number_format($product->discount_price, 0, ',', '.') }}</span>
                                                        <span class="text-sm text-gray-500 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                                    @else
                                                        <span class="text-lg font-bold text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    Stok: {{ $product->stock }}
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-center space-x-2">
                                                <div class="flex items-center border rounded-lg">
                                                    <button type="button" 
                                                            @click="decreaseQuantity({{ $product->id }})"
                                                            class="px-3 py-2 text-gray-500 hover:text-gray-700">
                                                        <i class="fa-solid fa-minus"></i>
                                                    </button>
                                                    <input type="number" 
                                                        x-model="quantities[{{ $product->id }}]"
                                                        min="1" 
                                                        :max="{{ $product->stock }}"
                                                        class="w-16 text-center border-0 focus:ring-0 focus:outline-none">
                                                    <button type="button" 
                                                            @click="increaseQuantity({{ $product->id }})"
                                                            class="px-3 py-2 text-gray-500 hover:text-gray-700">
                                                        <i class="fa-solid fa-plus"></i>
                                                    </button>
                                                </div>
                                                <button type="button" 
                                                        @click="addToCart({{ $product->id }})"
                                                        :disabled="loading"
                                                        class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200"
                                                        {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                                    <i class="fa-solid fa-cart-plus mr-2"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-8">
                                {{ $products->appends(request()->query())->links() }}
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="fa-solid fa-search text-6xl text-gray-400 mb-4"></i>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Produk Tidak Ditemukan</h3>
                                <p class="text-gray-600 mb-4">Coba ubah filter atau kata kunci pencarian Anda</p>
                                <a href="{{ route('products.index') }}" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
                                    <i class="fa-solid fa-refresh mr-2"></i>
                                    Reset Filter
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <script>
            function productGrid() {
                return {
                    loading: false,
                    quantities: {},
                    init() {
                        @foreach($products as $product)
                            this.quantities[{{ $product->id }}] = 1;
                        @endforeach
                    },
                    increaseQuantity(productId) {
                        if (this.quantities[productId] < this.getMaxStock(productId)) {
                            this.quantities[productId]++;
                        }
                    },
                    
                    decreaseQuantity(productId) {
                        if (this.quantities[productId] > 1) {
                            this.quantities[productId]--;
                        }
                    },
                    
                    getMaxStock(productId) {
                        let product = null;
                        @foreach($products as $product)
                            if (productId === {{ $product->id }}) product = { stock: {{ $product->stock }} };
                        @endforeach
                        return product ? product.stock : 1;
                    },
                    async addToCart(productId) {
                        this.loading = true;
                        
                        try {
                            const response = await fetch('{{ route("cart.add") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    product_id: productId,
                                    quantity: this.quantities[productId] || 1
                                })
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.showNotification('success', data.message);
                                this.updateCartBadge(data.cart_summary.total_quantity);
                                this.quantities[productId] = 1;
                            } else {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                    return;
                                }
                                this.showNotification('error', data.message);
                            }
                        } catch (error) {
                            this.showNotification('error', 'Terjadi kesalahan. Silakan coba lagi');
                        } finally {
                            this.loading = false;
                        }
                    },

                    showNotification(type, message) {
                        const toast = document.createElement('div');
                        toast.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full ${
                            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
                        }`;
                        toast.innerHTML = `
                            <div class="flex items-center">
                                <i class="fa-solid ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                                <span>${message}</span>
                            </div>
                        `;
                        
                        document.body.appendChild(toast);
                        
                        setTimeout(() => {
                            toast.classList.remove('translate-x-full');
                        }, 100);
                        
                        setTimeout(() => {
                            toast.classList.add('translate-x-full');
                            setTimeout(() => {
                                document.body.removeChild(toast);
                            }, 300);
                        }, 3000);
                    },

                    updateCartBadge(quantity) {
                        const cartBadge = document.querySelector('.cart-badge');
                        if (cartBadge) {
                            cartBadge.textContent = quantity;
                            cartBadge.style.display = quantity > 0 ? 'inline' : 'none';
                        }
                    }
                }
            }
        </script>
    </x-layouts.plain-app>