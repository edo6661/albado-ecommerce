<x-layouts.plain-app>
    <x-slot:title>Kategori Produk - Toko Online</x-slot:title>
    
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl font-bold mb-4">Kategori Produk</h1>
                    <p class="text-xl text-blue-100">Jelajahi berbagai kategori produk yang tersedia</p>
                </div>
            </div>
        </div>

        <!-- Categories Grid -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            @if($categories->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($categories as $category)
                        <a href="{{ route('categories.show', $category->slug) }}" 
                           class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition duration-200 group">
                            <div class="aspect-square bg-gray-200 overflow-hidden relative">
                                @if($category->image)
                                    <img src="{{ $category->image_url }}" 
                                         alt="{{ $category->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition duration-200">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-50 to-purple-50">
                                        <i class="fa-solid fa-folder text-6xl text-blue-400"></i>
                                    </div>
                                @endif
                                
                                <!-- Product Count Badge -->
                                <div class="absolute top-3 right-3 bg-white bg-opacity-90 text-gray-800 px-2 py-1 rounded-full text-sm font-medium">
                                    {{ $category->products->count() }} produk
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 text-center group-hover:text-blue-600 transition duration-200">
                                    {{ $category->name }}
                                </h3>
                                
                                <div class="mt-4 flex items-center justify-center text-blue-600">
                                    <span class="text-sm font-medium">Lihat Produk</span>
                                    <i class="fa-solid fa-arrow-right ml-2 text-sm group-hover:translate-x-1 transition duration-200"></i>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fa-solid fa-folder-open text-6xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Kategori</h3>
                    <p class="text-gray-600">Kategori produk belum tersedia saat ini</p>
                </div>
            @endif
        </div>

        <!-- Statistics Section -->
        <div class="bg-white py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Statistik Kategori</h2>
                    <p class="text-lg text-gray-600">Ringkasan kategori dan produk yang tersedia</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="bg-blue-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-folder text-3xl text-blue-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $categories->count() }}</h3>
                        <p class="text-gray-600">Total Kategori</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="bg-green-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-box text-3xl text-green-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $categories->sum(function($category) { return $category->products->count(); }) }}</h3>
                        <p class="text-gray-600">Total Produk</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="bg-purple-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-star text-3xl text-purple-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $categories->where('products')->count() }}</h3>
                        <p class="text-gray-600">Kategori Aktif</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.plain-app>