<x-layouts.plain-app>
    <x-slot:title>Beri Rating - {{ $product->name }}</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <div class="mb-8">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('ratings.index') }}" 
                       class="text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-extrabold text-gray-900">Beri Rating & Ulasan</h1>
                        <p class="mt-2 text-sm text-gray-600">
                            Bagikan pengalaman Anda dengan produk ini
                        </p>
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6">
                <div class="p-6">
                    <div class="flex items-center space-x-4">
                        <img src="{{ $product->images->first()?->path_url ?? '/images/placeholder.jpg' }}" 
                             alt="{{ $product->name }}" 
                             class="h-16 w-16 rounded-lg object-cover">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900">{{ $product->name }}</h2>
                            <p class="text-sm text-gray-500">{{ $product->category->name }}</p>
                            <p class="text-lg font-semibold text-blue-600 mt-1">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rating Form -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <form @submit.prevent="submitForm()" class="space-y-6" x-data="ratingForm()">
                    <div class="px-6 py-6 space-y-6">
                        <!-- Rating Stars -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Rating <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center space-x-1">
                                <template x-for="star in 5">
                                    <button type="button" 
                                            @click="setRating(star)"
                                            @mouseover="hoverRating = star"
                                            @mouseleave="hoverRating = 0"
                                            class="focus:outline-none">
                                        <svg class="h-8 w-8 transition-colors duration-200" 
                                             :class="star <= (hoverRating || rating) ? 'text-yellow-400' : 'text-gray-300'" 
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                            <p class="mt-1 text-sm text-gray-500" x-show="rating > 0">
                                <span x-text="getRatingText()"></span>
                            </p>
                        </div>

                        <!-- Review -->
                        <div>
                            <label for="review" class="block text-sm font-medium text-gray-700 mb-2">
                                Ulasan (Opsional)
                            </label>
                            <textarea name="review" 
                                      id="review"
                                      rows="4"
                                      x-model="review"
                                      placeholder="Ceritakan pengalaman Anda dengan produk ini..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                      maxlength="1000"></textarea>
                            <p class="mt-1 text-sm text-gray-500">
                                <span x-text="review.length"></span>/1000 karakter
                            </p>
                        </div>

                        <!-- Images -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Foto Produk (Opsional)
                            </label>
                            <div class="space-y-4">
                                <div>
                                    <input type="file" 
                                           name="images[]" 
                                           id="images"
                                           multiple 
                                           accept="image/*"
                                           @change="previewImages($event)"
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <p class="mt-1 text-sm text-gray-500">
                                        Format: JPG, PNG, WebP. Maksimal 5 foto, masing-masing 2MB.
                                    </p>
                                </div>

                                <div x-show="imagePreview.length > 0" class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                                    <template x-for="(image, index) in imagePreview" :key="index">
                                        <div class="relative">
                                            <img :src="image.url" :alt="'Preview ' + (index + 1)" 
                                                 class="h-24 w-full object-cover rounded-lg border-2 border-gray-200">
                                            <button type="button" 
                                                    @click="removeImage(index)"
                                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4">
                        <div class="flex justify-between items-center">
                            <x-shared.button
                                variant="light"
                                href="{{ route('ratings.index') }}"
                                icon='<svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
                            >
                                Batal
                            </x-shared.button>

                            <button type="submit" 
                                    :disabled="rating === 0 || isSubmitting"
                                    :class="rating === 0 || isSubmitting ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-700'"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-md font-medium transition duration-150 ease-in-out">
                                <span x-show="!isSubmitting">Kirim Rating</span>
                                <span x-show="isSubmitting">Mengirim...</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function ratingForm() {
            return {
                rating: 0,
                hoverRating: 0,
                review: '',
                imagePreview: [],
                isSubmitting: false,
                
                setRating(star) {
                    this.rating = star;
                },
                
                getRatingText() {
                    const texts = {
                        1: 'Sangat Buruk',
                        2: 'Buruk',
                        3: 'Biasa',
                        4: 'Baik',
                        5: 'Sangat Baik'
                    };
                    return texts[this.rating] || '';
                },
                
                previewImages(event) {
                    const files = Array.from(event.target.files);
                    this.imagePreview = [];
                    
                    if (files.length > 5) {
                        alert('Maksimal 5 foto yang dapat diunggah');
                        return;
                    }
                    
                    files.forEach((file, index) => {
                        if (file.type.startsWith('image/')) {
                            if (file.size > 2048 * 1024) {
                                alert(`File ${file.name} terlalu besar. Maksimal 2MB.`);
                                return;
                            }
                            
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.imagePreview.push({
                                    url: e.target.result,
                                    file: file,
                                    index: index
                                });
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                },
                
                removeImage(index) {
                    this.imagePreview.splice(index, 1);
                    
                    // Update file input
                    const fileInput = document.getElementById('images');
                    const dt = new DataTransfer();
                    
                    this.imagePreview.forEach(preview => {
                        dt.items.add(preview.file);
                    });
                    
                    fileInput.files = dt.files;
                },
                submitForm() {
                    // Langsung akses data dengan 'this'
                    if (this.rating === 0) {
                        alert('Rating wajib diisi.');
                        return;
                    }

                    this.isSubmitting = true;

                    const formData = new FormData();
                    formData.append('product_id', {{ $product->id }});
                    formData.append('rating', this.rating);
                    formData.append('review', this.review);

                    const imageFiles = document.getElementById('images').files;
                    for (let i = 0; i < imageFiles.length; i++) {
                        formData.append('images[]', imageFiles[i]);
                    }

                    fetch('{{ route("ratings.store") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json' // Tambahkan ini untuk konsistensi
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Rating berhasil ditambahkan!');
                            window.location.href = '{{ route("ratings.index") }}';
                        } else {
                            // Menampilkan pesan error dari server jika ada
                            alert(data.message || 'Terjadi kesalahan saat validasi data.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat mengirim rating.');
                    })
                    .finally(() => {
                        this.isSubmitting = false;
                    });
                },
            }
        }

    </script>
</x-layouts.plain-app>