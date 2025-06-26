<x-layouts.plain-app>
    <x-slot:title>Edit Produk</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8" 
         x-data="productForm({
            productName: '{{ old('name', $product->name) }}',
            productSlug: '{{ old('slug', $product->slug) }}'
         })">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.products.index') }}"
                       class="text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-extrabold text-gray-900">Edit Produk</h1>
                        <p class="mt-2 text-sm text-gray-600">
                            Perbarui informasi produk: {{ $product->name }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="px-6 py-6 space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                            
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <x-shared.forms.input
                                    name="name"
                                    label="Nama Produk"
                                    placeholder="Masukkan nama produk"
                                    :value="$product->name"
                                    required
                                    container-class="sm:col-span-2"
                                />

                                <x-shared.forms.select
                                    name="category_id"
                                    label="Kategori"
                                    :options="$categories->pluck('name', 'id')->toArray()"
                                    :value="$product->category_id"
                                    placeholder="Pilih kategori"
                                    required
                                />

                                <x-shared.forms.input
                                    name="stock"
                                    type="number"
                                    label="Stok"
                                    placeholder="0"
                                    min="0"
                                    :value="$product->stock"
                                    required
                                />

                                <x-shared.forms.textarea
                                    name="description"
                                    label="Deskripsi"
                                    placeholder="Masukkan deskripsi produk"
                                    :rows="4"
                                    :value="$product->description"
                                    container-class="sm:col-span-2"
                                />
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Harga</h3>
                            
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <x-shared.forms.input
                                    name="price"
                                    type="number"
                                    label="Harga Normal"
                                    placeholder="0.00"
                                    min="0"
                                    step="0.01"
                                    :value="$product->price"
                                    prefix="Rp"
                                    required
                                />

                                <x-shared.forms.input
                                    name="discount_price"
                                    type="number"
                                    label="Harga Diskon"
                                    placeholder="0.00"
                                    min="0"
                                    step="0.01"
                                    :value="$product->discount_price"
                                    prefix="Rp"
                                    help-text="Kosongkan jika tidak ada diskon"
                                    optional
                                />
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <x-shared.forms.label>Gambar Saat Ini</x-shared.forms.label>
                            
                            @if($product->images->isNotEmpty())
                                <div id="existing-images" class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 mt-2">
                                    @foreach($product->images as $image)
                                        <div class="relative group" id="image-{{ $image->id }}">
                                            <img src="{{ $image->path_url }}" alt="Gambar produk" 
                                                 class="h-24 w-full object-cover rounded-lg border-2 border-gray-200">
                                            
                                            <div class="absolute inset-0  flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">
                                                <x-shared.button
                                                    type="button"
                                                    variant="danger"
                                                    size="sm"
                                                    onclick="deleteExistingImage({{ $product->id }}, {{ $image->id }})"
                                                    icon='<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>'
                                                >
                                                    Hapus
                                                </x-shared.button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Arahkan kursor ke gambar untuk menghapusnya.</p>
                            @else
                                <p class="text-sm text-gray-500 mt-2">Tidak ada gambar untuk produk ini.</p>
                            @endif
                        </div>

                        <div>
                            <x-shared.forms.input
                                name="images[]"
                                type="file"
                                label="Tambah Gambar Baru"
                                accept="image/*"
                                multiple
                                help-text="Anda dapat memilih beberapa file sekaligus untuk menambah gambar baru."
                                @change="previewImages($event)"
                            />

                            <div x-show="imagePreview.length > 0" class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                                <template x-for="(image, index) in imagePreview" :key="index">
                                    <div class="relative">
                                        <img :src="image.url" class="h-24 w-full object-cover rounded-lg border-2 border-gray-200">
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

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Status</h3>
                            
                            <x-shared.forms.checkbox
                                name="is_active"
                                label="Produk aktif dan dapat dijual"
                                :checked="$product->is_active"
                            />
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
                        <x-shared.button
                            variant="light"
                            href="{{ route('admin.products.index') }}"
                        >
                            Batal
                        </x-shared.button>

                        <x-shared.button
                            type="submit"
                            variant="primary"
                        >
                            Simpan Perubahan
                        </x-shared.button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function productForm() {
            return {
                imagePreview: [],
                
                previewImages(event) {
                    const files = Array.from(event.target.files);
                    this.imagePreview = [];
                    
                    files.forEach(file => {
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.imagePreview.push({ url: e.target.result, file: file });
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                },
                
                removeImage(index) {
                    this.imagePreview.splice(index, 1);
                    const fileInput = document.getElementById('images');
                    const dt = new DataTransfer();
                    this.imagePreview.forEach(preview => dt.items.add(preview.file));
                    fileInput.files = dt.files;
                }
            }
        }

        function deleteExistingImage(productId, imageId) {
            if (!confirm('Apakah Anda yakin ingin menghapus gambar ini?')) {
                return;
            }
            
            const imageElement = document.getElementById(`image-${imageId}`);
            
            fetch(`/admin/products/${productId}/images/${imageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    imageElement.remove();
                    
                    const remainingImages = document.querySelectorAll('#existing-images > div');
                    if (remainingImages.length === 0) {
                        document.getElementById('existing-images').innerHTML = '<p class="text-sm text-gray-500 col-span-full">Tidak ada gambar untuk produk ini.</p>';
                    }
                    
                    showNotification('Gambar berhasil dihapus', 'success');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus gambar');
            });
        }
        
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-md z-50 ${type === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200'}`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
</x-layouts.plain-app>