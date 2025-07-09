<x-layouts.plain-app>
    <x-slot:title>Detail Rating - {{ $rating->product->name }}</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('ratings.index') }}" 
                           class="text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="text-3xl font-extrabold text-gray-900">Detail Rating</h1>
                            <p class="mt-2 text-sm text-gray-600">
                                Rating dan ulasan yang Anda berikan
                            </p>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('ratings.edit', $rating->id) }}"
                           class="bg-blue-600 text-white px-4 py-2 rounded-md font-medium hover:bg-blue-700 transition duration-150 ease-in-out">
                            <i class="fa-solid fa-edit mr-2"></i>Edit
                        </a>
                        <button onclick="deleteRating({{ $rating->id }})"
                                class="bg-red-600 text-white px-4 py-2 rounded-md font-medium hover:bg-red-700 transition duration-150 ease-in-out">
                            <i class="fa-solid fa-trash mr-2"></i>Hapus
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6">
                <div class="p-6">
                    <div class="flex items-center space-x-4">
                        <img src="{{ $rating->product->images->first()?->path_url ?? '/images/placeholder.jpg' }}" 
                             alt="{{ $rating->product->name }}" 
                             class="h-20 w-20 rounded-lg object-cover">
                        <div class="flex-1">
                            <h2 class="text-xl font-semibold text-gray-900">{{ $rating->product->name }}</h2>
                            <p class="text-sm text-gray-500">{{ $rating->product->category->name }}</p>
                            <p class="text-lg font-semibold text-blue-600 mt-1">
                                Rp {{ number_format($rating->product->price, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rating Details -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-6">
                    <div class="space-y-6">
                        <!-- Rating Stars -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                            <div class="flex items-center space-x-2">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="h-6 w-6 {{ $i <= $rating->rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-lg font-semibold text-gray-900">{{ $rating->rating }}/5</span>
                                <span class="text-sm text-gray-500">
                                    @switch($rating->rating)
                                        @case(1) (Sangat Buruk) @break
                                        @case(2) (Buruk) @break
                                        @case(3) (Biasa) @break
                                        @case(4) (Baik) @break
                                        @case(5) (Sangat Baik) @break
                                    @endswitch
                                </span>
                            </div>
                        </div>

                        <!-- Review -->
                        @if($rating->review)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ulasan</label>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-gray-800 leading-relaxed">{{ $rating->review }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Images -->
                        @if($rating->images->count() > 0)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Foto Produk</label>
                                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                                    @foreach($rating->images as $image)
                                        <div class="relative group">
                                            <img src="{{ $image->path_url }}" 
                                                 alt="Rating Image {{ $loop->iteration }}"
                                                 class="h-32 w-full object-cover rounded-lg border-2 border-gray-200 cursor-pointer hover:border-blue-500 transition duration-150"
                                                 onclick="openImageModal('{{ $image->path_url }}')">
                                            
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Rating Info -->
                        <div class="border-t pt-6">
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span>Dibuat: {{ $rating->created_at->format('d M Y, H:i') }}</span>
                                @if($rating->updated_at != $rating->created_at)
                                    <span>Terakhir diubah: {{ $rating->updated_at->format('d M Y, H:i') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50" onclick="closeImageModal()">
        <div class="max-w-4xl max-h-full p-4">
            <img id="modalImage" src="" alt="Rating Image" class="max-w-full max-h-full object-contain">
        </div>
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300">
            <i class="fa-solid fa-times"></i>
        </button>
    </div>

    <script>
        function openImageModal(imageUrl) {
            document.getElementById('modalImage').src = imageUrl;
            document.getElementById('imageModal').classList.remove('hidden');
            document.getElementById('imageModal').classList.add('flex');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.getElementById('imageModal').classList.remove('flex');
        }

        function deleteRating(ratingId) {
            if (confirm('Apakah Anda yakin ingin menghapus rating ini?')) {
                fetch(`/ratings/${ratingId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Rating berhasil dihapus!');
                        window.location.href = '{{ route("ratings.index") }}';
                    } else {
                        alert(data.message || 'Terjadi kesalahan saat menghapus rating.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus rating.');
                });
            }
        }
    </script>
</x-layouts.plain-app>