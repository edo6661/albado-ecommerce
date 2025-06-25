{{-- resources/views/admin/products/show.blade.php --}}
<x-layouts.plain-app>
    <x-slot:title>Detail Produk - {{ $product->name }}</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            {{-- Header --}}
            <div class="flex justify-between items-center mb-8">
                <div>
                    <div class="flex items-center space-x-3 mb-2">
                        <a href="{{ route('admin.products.index') }}" 
                           class="text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <h1 class="text-3xl font-extrabold text-gray-900">Detail Produk</h1>
                    </div>
                    <p class="text-sm text-gray-600">
                        Informasi lengkap produk {{ $product->name }}
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.products.edit', $product->id) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Produk
                    </a>
                    <form action="{{ route('admin.products.destroy', $product->id) }}" 
                          method="POST" 
                          class="inline"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus Produk
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Main Content --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Product Images --}}
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Gambar Produk</h3>
                        </div>
                        <div class="p-6">
                            @if($product->images->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($product->images as $image)
                                        <div class="relative group">
                                            <img class="w-full h-48 object-cover rounded-lg shadow-sm" 
                                                 src="{{ $image->path_url }}" 
                                                 alt="{{ $product->name }} - Gambar {{ $loop->iteration }}">
                                            <div class="absolute inset-0 group-hover:bg-opacity-30 transition-all duration-200 rounded-lg flex items-center justify-center">
                                                <button onclick="viewImage('{{ $image->path_url }}')" 
                                                        class="opacity-0 group-hover:opacity-100 bg-white rounded-full p-2 text-gray-700 hover:text-gray-900 transition-all duration-200">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">Belum ada gambar untuk produk ini</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Product Description --}}
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Deskripsi Produk</h3>
                        </div>
                        <div class="p-6">
                            @if($product->description)
                                <div class="prose max-w-none text-gray-700">
                                    {!! nl2br(e($product->description)) !!}
                                </div>
                            @else
                                <p class="text-gray-500 italic">Belum ada deskripsi untuk produk ini.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    {{-- Product Info --}}
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Informasi Produk</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            {{-- Product Name --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $product->name }}</p>
                            </div>

                            {{-- Category --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $product->category->name }}
                                </span>
                            </div>

                            {{-- Status --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                @if($product->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="mr-1.5 h-3 w-3 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3"></circle>
                                        </svg>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        <svg class="mr-1.5 h-3 w-3 text-gray-400" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3"></circle>
                                        </svg>
                                        Nonaktif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Pricing & Stock --}}
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Harga & Stok</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            {{-- Price --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                                <div class="space-y-1">
                                    @if($product->discount_price)
                                        <div class="flex items-center space-x-2">
                                            <span class="text-2xl font-bold text-red-600">
                                                Rp {{ number_format($product->discount_price, 0, ',', '.') }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                                                Diskon
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500 line-through">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </p>
                                        @php
                                            $discountPercent = round((($product->price - $product->discount_price) / $product->price) * 100);
                                        @endphp
                                        <p class="text-sm text-green-600 font-medium">
                                            Hemat {{ $discountPercent }}%
                                        </p>
                                    @else
                                        <span class="text-2xl font-bold text-gray-900">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Stock --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                                <div class="flex items-center space-x-2">
                                    <span class="text-2xl font-bold {{ $product->stock < 10 ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ $product->stock }}
                                    </span>
                                    <span class="text-sm text-gray-500">unit</span>
                                </div>
                                @if($product->stock < 10)
                                    <div class="mt-2 flex items-center">
                                        <svg class="h-4 w-4 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm text-red-600 font-medium">Stok rendah!</span>
                                    </div>
                                @endif
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
                                <p class="text-sm text-gray-900">{{ $product->created_at->format('d F Y, H:i') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Terakhir Diperbarui</label>
                                <p class="text-sm text-gray-900">{{ $product->updated_at->format('d F Y, H:i') }}</p>
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