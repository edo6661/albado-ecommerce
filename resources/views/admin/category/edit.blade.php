<x-layouts.plain-app>
    <x-slot:title>Edit Kategori</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8" 
         x-data="categoryForm({
            categoryName: '{{ old('name', $category->name) }}',
            categorySlug: '{{ old('slug', $category->slug) }}'
         })">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.categories.index') }}"
                       class="text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-extrabold text-gray-900">Edit Kategori</h1>
                        <p class="mt-2 text-sm text-gray-600">
                            Perbarui informasi kategori: {{ $category->name }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <form method="POST" action="{{ route('admin.categories.update', $category->id) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="px-6 py-6 space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Kategori</h3>
                            
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <x-shared.forms.input
                                    name="name"
                                    label="Nama Kategori"
                                    placeholder="Masukkan nama kategori"
                                    :value="$category->name"
                                    required
                                    container-class="sm:col-span-2"
                                    x-model="form.name"
                                    @input="generateSlug()"
                                />

                                <x-shared.forms.input
                                    name="slug"
                                    label="Slug"
                                    placeholder="slug-kategori"
                                    :value="$category->slug"
                                    required
                                    container-class="sm:col-span-2"
                                    x-model="form.slug"
                                    help-text="URL slug untuk kategori"
                                />
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            @if($category->image)
                                <div class="mb-6">
                                    <x-shared.forms.label>Gambar Saat Ini</x-shared.forms.label>
                                    
                                    <div class="mt-2 relative inline-block" id="current-image">
                                        <img src="{{ $category->image_url }}" alt="Gambar kategori saat ini" 
                                             class="h-32 w-32 object-cover rounded-lg border-2 border-gray-200">
                                        
                                        <div class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity rounded-lg bg-black bg-opacity-50">
                                            <x-shared.button
                                                type="button"
                                                variant="danger"
                                                size="sm"
                                                onclick="deleteCurrentImage({{ $category->id }})"
                                                icon='<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>'
                                            >
                                                Hapus
                                            </x-shared.button>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">Arahkan kursor ke gambar untuk menghapusnya.</p>
                                </div>
                            @endif

                            <div>
                                <x-shared.forms.input
                                    name="image"
                                    type="file"
                                    label="{{ $category->image ? 'Ganti Gambar' : 'Upload Gambar' }}"
                                    accept="image/*"
                                    help-text="Format: JPG, PNG, GIF, WebP. Maksimal 2MB per file."
                                    @change="previewImage($event)"
                                />

                                <div x-show="imagePreview" class="mt-4">
                                    <div class="relative inline-block">
                                        <img :src="imagePreview" alt="Preview gambar kategori" 
                                             class="h-32 w-32 object-cover rounded-lg border-2 border-gray-200">
                                        <button type="button" 
                                                @click="removeImage()"
                                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            @if($category->image)
                                <div class="mt-4">
                                    <x-shared.forms.checkbox
                                        name="delete_image"
                                        label="Hapus gambar saat ini"
                                        help-text="Centang untuk menghapus gambar yang ada saat ini"
                                    />
                                </div>
                            @endif
                        </div>

                        @if($category->products->count() > 0)
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Informasi Produk</h3>
                                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-blue-700">
                                                Kategori ini memiliki <strong>{{ $category->products->count() }} produk</strong> yang terkait.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
                        <x-shared.button
                            variant="light"
                            href="{{ route('admin.categories.index') }}"
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
        function categoryForm(data = {}) {
            return {
                form: {
                    name: data.categoryName || '',
                    slug: data.categorySlug || ''
                },
                imagePreview: null,
                
                generateSlug() {
                    this.form.slug = this.form.name
                        .toLowerCase()
                        .replace(/[^a-z0-9\s]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/(^-|-$)/g, '');
                },
                
                previewImage(event) {
                    const file = event.target.files[0];
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.imagePreview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                },
                
                removeImage() {
                    this.imagePreview = null;
                    const fileInput = document.querySelector('input[name="image"]');
                    fileInput.value = '';
                }
            }
        }

        function deleteCurrentImage(categoryId) {
            if (!confirm('Apakah Anda yakin ingin menghapus gambar ini?')) {
                return;
            }
            
            const currentImageElement = document.getElementById('current-image');
            
            fetch(`/admin/categories/${categoryId}/image`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentImageElement.remove();
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