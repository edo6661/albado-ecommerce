<x-layouts.plain-app>
    <x-slot:title>Beranda - Toko Online</x-slot:title>
    
    <div class="min-h-screen bg-gray-50">
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl md:text-6xl font-bold mb-4">
                        Selamat Datang di Toko Online
                    </h1>
                    <p class="text-xl md:text-2xl mb-8 text-blue-100">
                        Temukan produk terbaik dengan kualitas premium
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="#categories" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-200">
                            Jelajahi Kategori
                        </a>
                        <a href="#products" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition duration-200">
                            Lihat Produk
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div id="categories" class="py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Kategori Produk</h2>
                    <p class="text-lg text-gray-600">Pilih kategori sesuai kebutuhan Anda</p>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($categories as $category)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-200 group">
                        <div class="aspect-square bg-gray-200 overflow-hidden">
                            @if($category->image)
                                <img src="{{ $category->image_url }}" 
                                     alt="{{ $category->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-200">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                    <i class="fa-solid fa-folder text-4xl text-gray-400"></i>
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 text-center">{{ $category->name }}</h3>
                            <p class="text-sm text-gray-500 text-center mt-1">{{ $category->products->count() }} produk</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div id="products" class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Produk Unggulan</h2>
                    <p class="text-lg text-gray-600">Koleksi terbaru dan terpopuler</p>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" 
                     x-data="productGrid()">
                    @foreach($featuredProducts as $product)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-200 group">
                        <div class="aspect-square bg-gray-200 overflow-hidden relative">
                            @if($product->images->first())
                                <img src="{{ $product->images->first()->path_url }}" 
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-200">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                    <i class="fa-solid fa-image text-4xl text-gray-400"></i>
                                </div>
                            @endif
                            
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
                            <div class="mb-2">
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                    {{ $product->category->name }}
                                </span>
                            </div>
                            
                            <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ $product->name }}</h3>
                            
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
                                        class="flex-1 bg-red-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200"
                                        {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                    <i class="fa-solid fa-cart-plus mr-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Semua Produk</h2>
                    <p class="text-lg text-gray-600">Jelajahi koleksi lengkap produk kami</p>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" 
                     x-data="productGrid()">
                    @foreach($activeProducts as $product)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-200 group">
                        <div class="aspect-square bg-gray-200 overflow-hidden relative">
                            @if($product->images->first())
                                <img src="{{ $product->images->first()->path_url }}" 
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-200">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                    <i class="fa-solid fa-image text-4xl text-gray-400"></i>
                                </div>
                            @endif
                            
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
                            <div class="mb-2">
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                    {{ $product->category->name }}
                                </span>
                            </div>
                            
                            <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ $product->name }}</h3>
                            
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
                    {{ $activeProducts->links() }}
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
                    @foreach(array_merge($featuredProducts->toArray(), $activeProducts->take(12)->toArray()) as $product)
                        this.quantities[{{ $product['id'] }}] = 1;
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
                    @foreach($featuredProducts as $product)
                        if (productId === {{ $product->id }}) product = { stock: {{ $product->stock }} };
                    @endforeach
                    @foreach($activeProducts->take(12) as $product)
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
                        this.showNotification('error', 'Terjadi kesalahan. Silakan coba lagi', error);
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