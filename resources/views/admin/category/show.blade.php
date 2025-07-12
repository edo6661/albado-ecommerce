{{-- resources/views/admin/categories/show.blade.php --}}
<x-layouts.plain-app>
    <x-slot:title>Detail Kategori - {{ $category->name }}</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            {{-- Header --}}
            <div class="flex justify-between items-center mb-8">
                <div>
                    <div class="flex items-center space-x-3 mb-2">
                        <a href="{{ route('admin.categories.index') }}" 
                           class="text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <h1 class="text-3xl font-extrabold text-gray-900">Detail Kategori</h1>
                    </div>
                    <p class="text-sm text-gray-600">
                        Informasi lengkap kategori {{ $category->name }}
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.categories.edit', $category->id) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Kategori
                    </a>
                    <form action="{{ route('admin.categories.destroy', $category->id) }}" 
                          method="POST" 
                          class="inline"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus Kategori
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Main Content --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Category Image --}}
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Gambar Kategori</h3>
                        </div>
                        <div class="p-6">
                            @if($category->image)
                                <div class="relative group max-w-3xl mx-auto">
                                    <img class="w-full h-64 object-cover rounded-lg shadow-sm" 
                                         src="{{ $category->image_url }}" 
                                         alt="{{ $category->name }}">
                                    <div class="absolute inset-0 group-hover:bg-black group-hover:bg-opacity-30 transition-all duration-200 rounded-lg flex items-center justify-center">
                                        <button onclick="viewImage('{{ $category->image_url }}')" 
                                                class="opacity-0 group-hover:opacity-100 bg-white rounded-full p-2 text-gray-700 hover:text-gray-900 transition-all duration-200">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">Belum ada gambar untuk kategori ini</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Products in Category --}}
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900">Produk dalam Kategori</h3>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $category->products->count() }} produk
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            @if($category->products->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($category->products as $product)
                                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                                            <div class="flex items-start space-x-4">
                                                @if($product->images->first())
                                                    <img class="w-16 h-16 object-cover rounded-md flex-shrink-0" 
                                                         src="{{ $product->images->first()->path_url }}" 
                                                         alt="{{ $product->name }}">
                                                @else
                                                    <div class="w-16 h-16 bg-gray-200 rounded-md flex items-center justify-center flex-shrink-0">
                                                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between">
                                                        <h4 class="text-sm font-medium text-gray-900 truncate">
                                                            {{ $product->name }}
                                                        </h4>
                                                        @if($product->is_active)
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                Aktif
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                                Nonaktif
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="mt-1 flex items-center justify-between">
                                                        <div class="flex items-center space-x-2">
                                                            @if($product->discount_price)
                                                                <span class="text-sm font-medium text-red-600">
                                                                    Rp {{ number_format($product->discount_price, 0, ',', '.') }}
                                                                </span>
                                                                <span class="text-xs text-gray-500 line-through">
                                                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                                                </span>
                                                            @else
                                                                <span class="text-sm font-medium text-gray-900">
                                                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <span class="text-xs text-gray-500">
                                                            Stok: {{ $product->stock }}
                                                        </span>
                                                    </div>
                                                    <div class="mt-2">
                                                        <a href="{{ route('admin.products.show', $product->id) }}" 
                                                           class="text-xs text-indigo-600 hover:text-indigo-500">
                                                            Lihat Detail →
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">Belum ada produk dalam kategori ini</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    {{-- Category Info --}}
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Informasi Kategori</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            {{-- Category Name --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $category->name }}</p>
                            </div>

                            {{-- Category Slug --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                                <p class="text-sm text-gray-600 bg-gray-50 px-3 py-2 rounded-md font-mono">
                                    {{ $category->slug }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Category Statistics --}}
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Statistik</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            {{-- Total Products --}}
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Total Produk</span>
                                <span class="text-2xl font-bold text-gray-900">{{ $category->products->count() }}</span>
                            </div>

                            {{-- Active Products --}}
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Produk Aktif</span>
                                <span class="text-2xl font-bold text-green-600">
                                    {{ $category->products->where('is_active', true)->count() }}
                                </span>
                            </div>

                            {{-- Inactive Products --}}
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Produk Nonaktif</span>
                                <span class="text-2xl font-bold text-gray-500">
                                    {{ $category->products->where('is_active', false)->count() }}
                                </span>
                            </div>

                            {{-- Total Stock --}}
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Total Stok</span>
                                <span class="text-2xl font-bold text-blue-600">
                                    {{ $category->products->sum('stock') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Timestamps --}}
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Informasi Waktu</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Dibuat</label>
                                <p class="text-sm text-gray-900">{{ $category->created_at->format('d F Y, H:i') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Terakhir Diperbarui</label>
                                <p class="text-sm text-gray-900">{{ $category->updated_at->format('d F Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Image Modal --}}
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
        <div class="relative max-w-4xl max-h-full">
            <button onclick="closeImageModal()" 
                    class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <img id="modalImage" class="max-w-full max-h-full object-contain rounded-lg" src="" alt="">
        </div>
    </div>

    <script>
        function viewImage(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        // Close modal when clicking outside the image
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
</x-layouts.plain-app>