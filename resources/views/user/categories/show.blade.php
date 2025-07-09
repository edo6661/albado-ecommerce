<x-layouts.plain-app>
    <x-slot:title>{{ $category->name }} - Kategori Produk</x-slot:title>

    <div class="min-h-screen bg-gray-50">
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <nav class="flex justify-center mb-4" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="{{ route('home') }}" class="text-blue-100 hover:text-white">
                                    <i class="fa-solid fa-home mr-2"></i>
                                    Beranda
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <i class="fa-solid fa-chevron-right mx-3 text-blue-200"></i>
                                    <a href="{{ route('categories.index') }}" class="text-blue-100 hover:text-white">Kategori</a>
                                </div>
                            </li>
                            <li aria-current="page">
                                <div class="flex items-center">
                                    <i class="fa-solid fa-chevron-right mx-3 text-blue-200"></i>
                                    <span class="text-white font-medium">{{ $category->name }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    <h1 class="text-4xl font-bold mb-4">{{ $category->name }}</h1>
                    <p class="text-xl text-blue-100">{{ $products->total() }} produk ditemukan dalam kategori ini</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        @if($category->image)
                            <img src="{{ $category->image_url }}"
                                 alt="{{ $category->name }}"
                                 class="w-16 h-16 object-cover rounded-lg">
                        @else
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-folder text-2xl text-gray-400"></i>
                            </div>
                        @endif

                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h2>
                            <p class="text-gray-600">{{ $products->total() }} produk tersedia</p>
                        </div>
                    </div>

                    <a href="{{ route('categories.index') }}"
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition duration-200">
                        <i class="fa-solid fa-arrow-left mr-2"></i>
                        Kembali ke Kategori
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white border-t">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <form method="GET" class="flex flex-wrap items-center gap-4" x-data="filterForm()">
                    <div class="flex-1 min-w-64">
                        <div class="relative">
                            <input type="text"
                                   name="search"
                                   value="{{ $search }}"
                                   placeholder="Cari produk..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <div class="min-w-48">
                        <select name="sort_by"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="latest" {{ $sortBy === 'latest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="oldest" {{ $sortBy === 'oldest' ? 'selected' : '' }}>Terlama</option>
                            <option value="price_low" {{ $sortBy === 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                            <option value="price_high" {{ $sortBy === 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                            <option value="name_asc" {{ $sortBy === 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                            <option value="name_desc" {{ $sortBy === 'name_desc' ? 'selected' : '' }}>Nama Z-A</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="number"
                               name="min_price"
                               value="{{ $minPrice }}"
                               placeholder="Min"
                               class="w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <span class="text-gray-500">-</span>
                        <input type="number"
                               name="max_price"
                               value="{{ $maxPrice }}"
                               placeholder="Max"
                               class="w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                            <i class="fa-solid fa-filter mr-2"></i>
                            Filter
                        </button>
                        <a href="{{ route('categories.show', $category->slug) }}"
                           class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition duration-200">
                            <i class="fa-solid fa-times mr-2"></i>
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
                     x-data="productGrid()">
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
                                <div class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded-md text-sm font-semibold">
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
                            <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                                <a href="{{ route('products.show', $product->slug) }}"
                                   class="hover:text-blue-600 transition duration-200">
                                    {{ $product->name }}
                                </a>
                            </h3>

                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $product->description }}</p>

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

                            <div class="flex items-center space-x-2 flex-1">
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
                <div class="text-center py-16">
                    <i class="fa-solid fa-box-open text-6xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak Ada Produk</h3>
                    <p class="text-gray-600 mb-4">Belum ada produk dalam kategori ini atau tidak sesuai dengan filter yang dipilih</p>
                    <a href="{{ route('categories.show', $category->slug) }}"
                       class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                        Reset Filter
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
        function filterForm() {
            return {
                // Filter form functionality can be added here
            }
        }

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