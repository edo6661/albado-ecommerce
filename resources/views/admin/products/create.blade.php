{{-- resources/views/admin/products/create.blade.php --}}
<x-layouts.plain-app>
    <x-slot:title>Tambah Produk</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.products.index') }}" 
                       class="text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-extrabold text-gray-900">Tambah Produk Baru</h1>
                        <p class="mt-2 text-sm text-gray-600">
                            Tambahkan produk baru ke dalam katalog toko
                        </p>
                    </div>
                </div>
            </div>

            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="rounded-md bg-red-50 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                Terdapat beberapa kesalahan pada form
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" 
                      x-data="productForm()" class="space-y-6">
                    @csrf

                    <div class="px-6 py-6 space-y-6">
                        {{-- Basic Information --}}
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                            
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label for="name" class="block text-sm font-medium text-gray-700">
                                        Nama Produk <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1">
                                        <input id="name" 
                                               name="name" 
                                               type="text" 
                                               class="appearance-none block w-full px-3 py-2 border @error('name') border-red-300 @else border-gray-300 @enderror rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                               placeholder="Masukkan nama produk"
                                               value="{{ old('name') }}"
                                               x-model="form.name"
                                               @input="generateSlug()">
                                    </div>
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-gray-700">
                                        Kategori <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1">
                                        <select id="category_id" 
                                                name="category_id" 
                                                class="block w-full px-3 py-2 border @error('category_id') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="">Pilih kategori</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('category_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="stock" class="block text-sm font-medium text-gray-700">
                                        Stok <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1">
                                        <input id="stock" 
                                               name="stock" 
                                               type="number" 
                                               min="0"
                                               class="appearance-none block w-full px-3 py-2 border @error('stock') border-red-300 @else border-gray-300 @enderror rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                               placeholder="0"
                                               value="{{ old('stock', 0) }}">
                                    </div>
                                    @error('stock')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="description" class="block text-sm font-medium text-gray-700">
                                        Deskripsi
                                    </label>
                                    <div class="mt-1">
                                        <textarea id="description" 
                                                  name="description" 
                                                  rows="4" 
                                                  class="block w-full px-3 py-2 border @error('description') border-red-300 @else border-gray-300 @enderror rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                                  placeholder="Masukkan deskripsi produk">{{ old('description') }}</textarea>
                                    </div>
                                    @error('description')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Pricing --}}
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Harga</h3>
                            
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <label for="price" class="block text-sm font-medium text-gray-700">
                                        Harga Normal <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1 relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input id="price" 
                                               name="price" 
                                               type="number" 
                                               min="0"
                                               step="0.01"
                                               class="appearance-none block w-full pl-10 pr-3 py-2 border @error('price') border-red-300 @else border-gray-300 @enderror rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                               placeholder="0.00"
                                               value="{{ old('price') }}">
                                    </div>
                                    @error('price')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="discount_price" class="block text-sm font-medium text-gray-700">
                                        Harga Diskon (Opsional)
                                    </label>
                                    <div class="mt-1 relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input id="discount_price" 
                                               name="discount_price" 
                                               type="number" 
                                               min="0"
                                               step="0.01"
                                               class="appearance-none block w-full pl-10 pr-3 py-2 border @error('discount_price') border-red-300 @else border-gray-300 @enderror rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                               placeholder="0.00"
                                               value="{{ old('discount_price') }}">
                                    </div>
                                    @error('discount_price')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ada diskon</p>
                                </div>
                            </div>
                        </div>

                        {{-- Images --}}
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Gambar Produk <span class="text-red-500">*</span></h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="images" class="block text-sm font-medium text-gray-700">
                                        Upload Gambar
                                    </label>
                                    <div class="mt-1">
                                        <input type="file" 
                                               name="images[]" 
                                               id="images" 
                                               multiple 
                                               accept="image/*"
                                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                               @change="previewImages($event)">
                                    </div>
                                    @error('images')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    @error('images.*')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">
                                        Format: JPG, PNG, GIF, WebP. Maksimal 2MB per file. Minimal 1 gambar.
                                    </p>
                                </div>

                                {{-- Image Preview --}}
                                <div x-show="imagePreview.length > 0" class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                                    <template x-for="(image, index) in imagePreview" :key="index">
                                        <div class="relative">
                                            <img :src="image.url" :alt="'Preview ' + (index + 1)" 
                                                 class="object-contain rounded-lg border-2 border-gray-200">
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

                        {{-- Status --}}
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Status</h3>
                            
                            <div class="flex items-center">
                                <input id="is_active" 
                                       name="is_active" 
                                       type="checkbox" 
                                       value="1"
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Produk aktif dan dapat dijual
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="bg-gray-50 px-6 py-4">
                        <div class="flex justify-between items-center">
                            <a href="{{ route('admin.products.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Batal
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simpan Produk
                            </button>
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
                    // Simple slug generation
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