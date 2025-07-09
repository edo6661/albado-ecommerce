<x-layouts.plain-app>
    <x-slot:title>Daftar Rating Saya</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-extrabold text-gray-900">Rating & Ulasan Saya</h1>
                        <p class="mt-2 text-sm text-gray-600">
                            Kelola rating dan ulasan yang pernah Anda berikan
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                @if($ratings->count() > 0)
                    <div class="divide-y divide-gray-200">
                        @foreach($ratings as $rating)
                            <div class="p-6 hover:bg-gray-50 transition duration-150 ease-in-out">
                                <div class="flex items-start space-x-4">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0">
                                        <img src="{{ $rating->product->images->first()?->path_url ?? '/images/placeholder.jpg' }}" 
                                             alt="{{ $rating->product->name }}" 
                                             class="h-16 w-16 rounded-lg object-cover">
                                    </div>

                                    <!-- Rating Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h3 class="text-lg font-medium text-gray-900">
                                                    {{ $rating->product->name }}
                                                </h3>
                                                <div class="flex items-center mt-1">
                                                    <div class="flex items-center">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= $rating->rating)
                                                                <svg class="h-5 w-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                </svg>
                                                            @else
                                                                <svg class="h-5 w-5 text-gray-300 fill-current" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                </svg>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                    <span class="ml-2 text-sm text-gray-500">
                                                        {{ $rating->created_at->format('d M Y') }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('ratings.show', $rating->id) }}" 
                                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                    Lihat
                                                </a>
                                                <a href="{{ route('ratings.edit', $rating->id) }}" 
                                                   class="text-green-600 hover:text-green-800 text-sm font-medium">
                                                    Edit
                                                </a>
                                                <button onclick="deleteRating({{ $rating->id }})" 
                                                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>

                                        @if($rating->review)
                                            <div class="mt-3">
                                                <p class="text-gray-700 text-sm line-clamp-3">
                                                    {{ $rating->review }}
                                                </p>
                                            </div>
                                        @endif

                                        <!-- Rating Images -->
                                        @if($rating->images->count() > 0)
                                            <div class="mt-3 flex space-x-2">
                                                @foreach($rating->images->take(3) as $image)
                                                    <img src="{{ $image->path_url }}" 
                                                         alt="Rating Image" 
                                                         class="h-12 w-12 rounded-lg object-cover">
                                                @endforeach
                                                @if($rating->images->count() > 3)
                                                    <div class="h-12 w-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                                        <span class="text-xs text-gray-500">+{{ $rating->images->count() - 3 }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        {{ $ratings->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada rating</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Anda belum memberikan rating untuk produk apapun.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function deleteRating(id) {
            if (confirm('Apakah Anda yakin ingin menghapus rating ini?')) {
                fetch(`/user/ratings/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Terjadi kesalahan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus rating');
                });
            }
        }
    </script>
</x-layouts.plain-app>