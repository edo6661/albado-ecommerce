<x-layouts.plain-app>
    <x-slot:title>{{ $product->name }} - Detail Produk</x-slot:title>
    
    <div class="min-h-screen bg-gray-50">
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700">
                                <i class="fa-solid fa-home"></i>
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fa-solid fa-chevron-right text-gray-400 mx-2"></i>
                                <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-gray-700">Produk</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fa-solid fa-chevron-right text-gray-400 mx-2"></i>
                                <a href="{{ route('categories.show', $product->category->slug) }}" class="text-gray-500 hover:text-gray-700">{{ $product->category->name }}</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fa-solid fa-chevron-right text-gray-400 mx-2"></i>
                                <span class="text-gray-700 font-medium">{{ $product->name }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="lg:grid lg:grid-cols-2 lg:gap-x-8">
                    <div class="lg:max-w-lg lg:self-start">
                    @if($product->images->count() > 0)
                        <div class="relative aspect-square overflow-hidden rounded-lg" 
                            x-data="productImageGallery()"
                            x-cloak>
                        <template x-for="(image, index) in images" :key="index">
                            <div
                            x-show="currentImageIndex === index"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute inset-0 w-full h-full"
                            >
                            <img 
                                :src="image.url" 
                                :alt="image.alt"
                                class="w-full h-full object-cover"
                            >
                            </div>
                        </template>
                        
                        @if($product->images->count() > 1)
                            <button 
                            @click="previousImage()" 
                            class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-75 hover:bg-opacity-100 rounded-full p-3 shadow-md z-10 transition-all duration-200 hover:scale-110 focus:outline-none"
                            >
                            <i class="fa-solid fa-chevron-left text-gray-600 text-lg"></i>
                            </button>
                            <button 
                            @click="nextImage()" 
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-75 hover:bg-opacity-100 rounded-full p-3 shadow-md z-10 transition-all duration-200 hover:scale-110 focus:outline-none"
                            >
                            <i class="fa-solid fa-chevron-right text-gray-600 text-lg"></i>
                            </button>
                        @endif
                        </div>

                        @if($product->images->count() > 1)
                        <div class="mt-4 flex space-x-2 overflow-x-auto py-2">
                            @foreach($product->images as $index => $image)
                            <button 
                            @click="setCurrentImage({{ $index }})"
                            class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 transition-all duration-200 transform hover:scale-105"
                            :class="{
                                'border-blue-500 scale-105': currentImageIndex === {{ $index }},
                                'border-gray-200': currentImageIndex !== {{ $index }}
                            }"
                            >
                            <img 
                                src="{{ $image->path_url }}" 
                                alt="{{ $product->name }}"
                                class="w-full h-full object-cover"
                            >
                            </button>
                            @endforeach
                        </div>
                        @endif
                    @else
                        <div class="aspect-square w-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg">
                        <i class="fa-solid fa-image text-8xl text-gray-400"></i>
                        </div>
                    @endif
                    </div>

                    <div class="mt-8 lg:mt-0 lg:col-start-2">
                        <div class="px-6 py-8">
                            <div class="mb-4">
                                <a href="{{ route('categories.show', $product->category->slug) }}" 
                                   class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors">
                                    {{ $product->category->name }}
                                </a>
                            </div>

                            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>

                            <div class="mb-6">
                                @if($product->discount_price)
                                    <div class="flex items-center space-x-3">
                                        <span class="text-3xl font-bold text-red-600">
                                            Rp {{ number_format($product->discount_price, 0, ',', '.') }}
                                        </span>
                                        <span class="text-xl text-gray-500 line-through">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </span>
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded-lg text-sm font-semibold">
                                            -{{ number_format((($product->price - $product->discount_price) / $product->price) * 100, 0) }}%
                                        </span>
                                    </div>
                                @else
                                    <span class="text-3xl font-bold text-gray-900">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>

                            <div class="mb-6">
                                @if($product->stock > 0)
                                    <div class="flex items-center space-x-2">
                                        <i class="fa-solid fa-check-circle text-green-500"></i>
                                        <span class="text-green-600 font-medium">Tersedia ({{ $product->stock }} item)</span>
                                    </div>
                                @else
                                    <div class="flex items-center space-x-2">
                                        <i class="fa-solid fa-times-circle text-red-500"></i>
                                        <span class="text-red-600 font-medium">Stok Habis</span>
                                    </div>
                                @endif
                            </div>

                            <div class="mb-8">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Deskripsi Produk</h3>
                                <p class="text-gray-600 leading-relaxed">{{ $product->description }}</p>
                            </div>

                            <div class="space-y-4" x-data="productDetail()">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center border rounded-lg">
                                        <button type="button" 
                                                @click="decreaseQuantity()"
                                                class="px-4 py-3 text-gray-500 hover:text-gray-700">
                                            <i class="fa-solid fa-minus"></i>
                                        </button>
                                        <input type="number" 
                                               x-model="quantity"
                                               min="1" 
                                               :max="{{ $product->stock }}"
                                               class="w-20 text-center border-0 focus:ring-0 focus:outline-none py-3">
                                        <button type="button" 
                                                @click="increaseQuantity()"
                                                class="px-4 py-3 text-gray-500 hover:text-gray-700">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </div>
                                    <button type="button" 
                                            @click="addToCart()"
                                            :disabled="loading || {{ $product->stock <= 0 ? 'true' : 'false' }}"
                                            class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200">
                                        <i class="fa-solid fa-cart-plus mr-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($relatedProducts->count() > 0)
                <div class="mt-16">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8">Produk Terkait</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($relatedProducts as $relatedProduct)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-200 group">
                                <div class="aspect-square bg-gray-200 overflow-hidden relative">
                                    <a href="{{ route('products.show', $relatedProduct->slug) }}">
                                        @if($relatedProduct->images->first())
                                            <img src="{{ $relatedProduct->images->first()->path_url }}" 
                                                 alt="{{ $relatedProduct->name }}"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-200">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                                <i class="fa-solid fa-image text-4xl text-gray-400"></i>
                                            </div>
                                        @endif
                                    </a>
                                    
                                    @if($relatedProduct->discount_price)
                                        <div class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded-md text-sm font-semibold">
                                            -{{ number_format((($relatedProduct->price - $relatedProduct->discount_price) / $relatedProduct->price) * 100, 0) }}%
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="p-4">
                                    <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                                        <a href="{{ route('products.show', $relatedProduct->slug) }}" class="hover:text-blue-600">
                                            {{ $relatedProduct->name }}
                                        </a>
                                    </h3>
                                    
                                    <div class="flex items-center justify-between">
                                        <div class="flex flex-col">
                                            @if($relatedProduct->discount_price)
                                                <span class="text-lg font-bold text-gray-900">Rp {{ number_format($relatedProduct->discount_price, 0, ',', '.') }}</span>
                                                <span class="text-sm text-gray-500 line-through">Rp {{ number_format($relatedProduct->price, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-lg font-bold text-gray-900">Rp {{ number_format($relatedProduct->price, 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            <div class="mt-16">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="px-6 py-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Rating & Ulasan</h2>
                        
                        <div class="mb-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="text-center">
                                    <div class="text-6xl font-bold text-gray-900 mb-2">
                                        {{ number_format($ratingStats['average_rating'], 1) }}
                                    </div>
                                    <div class="flex justify-center mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fa-solid fa-star text-xl {{ $i <= $ratingStats['average_rating'] ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                    <p class="text-gray-600">{{ $ratingStats['total_ratings'] }} ulasan</p>
                                </div>
                                
                                <div class="space-y-2">
                                    @for($i = 5; $i >= 1; $i--)
                                        <div class="flex items-center">
                                            <span class="text-sm text-gray-600 w-8">{{ $i }}</span>
                                            <i class="fa-solid fa-star text-yellow-400 text-sm mx-2"></i>
                                            <div class="flex-1 bg-gray-200 rounded-full h-2 mx-2">
                                                <div class="bg-yellow-400 h-2 rounded-full" 
                                                    style="width: {{ $ratingStats['rating_distribution'][$i]['percentage'] }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-600 w-12">{{ $ratingStats['rating_distribution'][$i]['count'] }}</span>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        @auth
                            <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                                @if($userRating)
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">Rating Anda:</p>
                                            <div class="flex items-center mt-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fa-solid fa-star text-lg {{ $i <= $userRating->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                @endfor
                                                <span class="ml-2 text-gray-600">{{ $userRating->rating }}/5</span>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('ratings.edit', $userRating->id) }}" 
                                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('ratings.destroy', $userRating->id) }}" 
                                                class="inline" onsubmit="return confirm('Yakin ingin menghapus rating?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @elseif($canUserRate)
                                    <div class="text-center">
                                        <p class="text-gray-600 mb-3">Anda sudah membeli produk ini. Berikan rating dan ulasan!</p>
                                        <a href="{{ route('ratings.create', $product->id) }}" 
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                            <i class="fa-solid fa-star mr-2"></i>
                                            Beri Rating
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endauth

                        @if($ratings->count() > 0)
                            <div class="space-y-6">
                                <h3 class="text-lg font-semibold text-gray-900">Ulasan Terbaru</h3>
                                
                                @foreach($ratings as $rating)
                                    <div class="border-b border-gray-200 pb-6 last:border-b-0">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                                    {{ strtoupper(substr($rating->user->name, 0, 1)) }}
                                                </div>
                                                <div class="ml-3">
                                                    <p class="font-medium text-gray-900">{{ $rating->user->name }}</p>
                                                    <div class="flex items-center">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fa-solid fa-star text-sm {{ $i <= $rating->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                        @endfor
                                                        <span class="ml-2 text-sm text-gray-600">{{ $rating->rating }}/5</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="text-sm text-gray-500">{{ $rating->created_at->diffForHumans() }}</p>
                                        </div>
                                        
                                        @if($rating->review)
                                            <p class="text-gray-700 mb-3">{{ $rating->review }}</p>
                                        @endif
                                        
                                        @if($rating->images->count() > 0)
                                            <div class="flex space-x-2 overflow-x-auto">
                                                @foreach($rating->images as $image)
                                                    <img src="{{ $image->path_url }}" 
                                                        alt="Rating image" 
                                                        class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                                
                                @if($ratingStats['total_ratings'] > 5)
                                    <div class="text-center">
                                        <a href="{{ route('products.ratings', $product->id) }}" 
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                            Lihat Semua Ulasan ({{ $ratingStats['total_ratings'] }})
                                            <i class="fa-solid fa-arrow-right ml-2"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fa-solid fa-star text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-600">Belum ada ulasan untuk produk ini</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function productImageGallery() {
            return {
                currentImageIndex: 0,
                images: [
                    @foreach($product->images as $image)
                        {
                            url: '{{ $image->path_url }}',
                            alt: '{{ $product->name }}'
                        },
                    @endforeach
                ],
                
                get currentImage() {
                    return this.images[this.currentImageIndex] || { url: '', alt: '' };
                },
                
                setCurrentImage(index) {
                    this.currentImageIndex = index;
                },
                
                nextImage() {
                    this.currentImageIndex = (this.currentImageIndex + 1) % this.images.length;
                },
                
                previousImage() {
                    this.currentImageIndex = this.currentImageIndex === 0 ? this.images.length - 1 : this.currentImageIndex - 1;
                }
            }
        }

        function productDetail() {
            return {
                quantity: 1,
                loading: false,
                
                increaseQuantity() {
                    if (this.quantity < {{ $product->stock }}) {
                        this.quantity++;
                    }
                },
                
                decreaseQuantity() {
                    if (this.quantity > 1) {
                        this.quantity--;
                    }
                },
                
                async addToCart() {
                    this.loading = true;
                    
                    try {
                        const response = await fetch('{{ route("cart.add") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                product_id: {{ $product->id }},
                                quantity: this.quantity
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.showNotification('success', data.message);
                            this.updateCartBadge(data.cart_summary.total_quantity);
                            this.quantity = 1;
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