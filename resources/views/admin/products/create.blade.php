<x-layouts.plain-app>
    <x-slot:title>Tambah Produk</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.products.index') }}" 
                       class="text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-extrabold text-gray-900">Tambah Produk Baru</h1>
                        <p class="mt-2 text-sm text-gray-600">
                            Tambahkan produk baru ke dalam katalog toko
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" 
                      x-data="productForm()" class="space-y-6">
                    @csrf
                    <div class="px-6 py-6 space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                            
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <x-shared.forms.input
                                    name="name"
                                    label="Nama Produk"
                                    placeholder="Masukkan nama produk"
                                    required
                                    container-class="sm:col-span-2"
                                    x-model="form.name"
                                    @input="generateSlug()"
                                />

                                <x-shared.forms.select
                                    name="category_id"
                                    label="Kategori"
                                    :options="$categories->pluck('name', 'id')->toArray()"
                                    placeholder="Pilih kategori"
                                    required
                                />

                                <x-shared.forms.input
                                    name="stock"
                                    type="number"
                                    label="Stok"
                                    placeholder="0"
                                    min="0"
                                    :value="0"
                                    required
                                />

                                <x-shared.forms.textarea
                                    name="description"
                                    label="Deskripsi"
                                    placeholder="Masukkan deskripsi produk"
                                    :rows="4"
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
                                    prefix="Rp"
                                    help-text="Kosongkan jika tidak ada diskon"
                                    optional
                                />
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                Gambar Produk <span class="text-red-500">*</span>
                            </h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <x-shared.forms.input
                                        name="images[]"
                                        type="file"
                                        label="Upload Gambar"
                                        accept="image/*"
                                        multiple
                                        help-text="Format: JPG, PNG, GIF, WebP. Maksimal 2MB per file. Minimal 1 gambar."
                                        @change="previewImages($event)"
                                    />
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

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Status</h3>
                            
                            <x-shared.forms.checkbox
                                name="is_active"
                                label="Produk aktif dan dapat dijual"
                                :checked="true"
                            />
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4">
                        <div class="flex justify-between items-center">
                            <x-shared.button
                                variant="light"
                                href="{{ route('admin.products.index') }}"
                                icon='<svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
                            >
                                Batal
                            </x-shared.button>

                            <x-shared.button
                                type="submit"
                                variant="primary"
                                icon='<svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
                            >
                                Simpan Produk
                            </x-shared.button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function productForm() {
            return {
                form: {
                    name: ''
                },
                imagePreview: [],
                
                generateSlug() {
                    this.form.slug = this.form.name
                        .toLowerCase()
                        .replace(/[^a-z0-9]+/g, '-')
                        .replace(/(^-|-$)/g, '');
                },
                
                previewImages(event) {
                    const files = Array.from(event.target.files);
                    this.imagePreview = [];
                    
                    files.forEach((file, index) => {
                        if (file.type.startsWith('image/')) {
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
                }
            }
        }
    </script>
</x-layouts.plain-app>