<x-layouts.plain-app>
    <x-slot:title>Tambah Kategori</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.categories.index') }}" 
                       class="text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-extrabold text-gray-900">Tambah Kategori Baru</h1>
                        <p class="mt-2 text-sm text-gray-600">
                            Tambahkan kategori baru ke dalam sistem
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" 
                      x-data="categoryForm()" class="space-y-6">
                    @csrf
                    <div class="px-6 py-6 space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Kategori</h3>
                            
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <x-shared.forms.input
                                    name="name"
                                    label="Nama Kategori"
                                    placeholder="Masukkan nama kategori"
                                    required
                                    container-class="sm:col-span-2"
                                    x-model="form.name"
                                    @input="generateSlug()"
                                />

                                <x-shared.forms.input
                                    name="slug"
                                    label="Slug"
                                    placeholder="slug-kategori"
                                    required
                                    container-class="sm:col-span-2"
                                    x-model="form.slug"
                                    help-text="URL slug untuk kategori, akan dibuat otomatis dari nama"
                                />
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                Gambar Kategori
                            </h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <x-shared.forms.input
                                        name="image"
                                        type="file"
                                        label="Upload Gambar"
                                        accept="image/*"
                                        help-text="Format: JPG, PNG, GIF, WebP. Maksimal 2MB per file."
                                        @change="previewImage($event)"
                                    />
                                </div>

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
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4">
                        <div class="flex justify-between items-center">
                            <x-shared.button
                                variant="light"
                                href="{{ route('admin.categories.index') }}"
                                icon='<svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
                            >
                                Batal
                            </x-shared.button>

                            <x-shared.button
                                type="submit"
                                variant="primary"
                                icon='<svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
                            >
                                Simpan Kategori
                            </x-shared.button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function categoryForm() {
            return {
                form: {
                    name: '',
                    slug: ''
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
    </script>
</x-layouts.plain-app>