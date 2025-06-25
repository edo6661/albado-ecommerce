<x-layouts.plain-app>
    <x-slot:title>Data Produk</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8" x-data="productManager()">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900">Data Produk</h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Kelola semua produk di toko Anda
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <button x-show="selectedProducts.length > 0" 
                            @click="showBulkDeleteModal = true"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus Terpilih (<span x-text="selectedProducts.length"></span>)
                    </button>
                    
                    <a href="{{ route('admin.products.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Produk
                    </a>
                </div>
            </div>
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari Produk</label>
                        <input x-model="searchQuery" 
                               @input.debounce.300ms="filterProducts()"
                               type="text" 
                               id="search"
                               placeholder="Nama produk..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Filter Kategori</label>
                        <select x-model="selectedCategory" 
                                @change="filterProducts()"
                                id="category"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua Kategori</option>
                            @foreach($products->unique('category.name') as $product)
                                <option value="{{ $product->category->name }}">{{ $product->category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Filter Status</label>
                        <select x-model="selectedStatus" 
                                @change="filterProducts()"
                                id="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua Status</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">
                            <span x-show="filteredProducts.length === 0 && searchQuery === '' && selectedCategory === '' && selectedStatus === ''">
                                Daftar Produk ({{ $products->total() }} produk)
                            </span>
                            <span x-show="filteredProducts.length > 0 || searchQuery !== '' || selectedCategory !== '' || selectedStatus !== ''" 
                                  x-text="`Daftar Produk (${filteredProducts.length} produk)`">
                            </span>
                        </h3>
                        
                        <div class="flex items-center space-x-2">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       x-model="selectAll" 
                                       @change="toggleSelectAll()"
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">Pilih Semua</span>
                            </label>
                        </div>
                    </div>
                </div>

                @if($products->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <input type="checkbox" 
                                               x-model="selectAll" 
                                               @change="toggleSelectAll()"
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Produk
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kategori
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Harga
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stok
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($products as $product)
                                    <tr class="hover:bg-gray-50" 
                                        x-show="shouldShowProduct({{ json_encode([
                                            'id' => $product->id,
                                            'name' => $product->name,
                                            'category' => $product->category->name,
                                            'is_active' => $product->is_active
                                        ]) }})"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 transform scale-95"
                                        x-transition:enter-end="opacity-100 transform scale-100">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" 
                                                   :value="{{ $product->id }}"
                                                   x-model="selectedProducts"
                                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-12 w-12">
                                                    @if($product->images->first())
                                                        <img class="h-12 w-12 rounded-lg object-cover" 
                                                             src="{{ $product->images->first()->path_url }}" 
                                                             alt="{{ $product->name }}"
                                                             @click="onClickShowImageModal('{{ $product->images->first()->path_url }}', '{{ $product->name }}')"
                                                             class="cursor-pointer hover:opacity-75 transition duration-150">
                                                    @else
                                                        <div class="h-12 w-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $product->name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ Str::limit($product->description, 40) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $product->category->name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div>
                                                @if($product->discount_price)
                                                    <span class="text-red-600 font-medium">
                                                        Rp {{ number_format($product->discount_price, 0, ',', '.') }}
                                                    </span>
                                                    <span class="text-gray-500 line-through ml-2">
                                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                                    </span>
                                                @else
                                                    <span class="font-medium">
                                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="font-medium {{ $product->stock < 10 ? 'text-red-600' : 'text-gray-900' }}">
                                                {{ $product->stock }}
                                            </span>
                                            @if($product->stock < 10)
                                                <span class="text-xs text-red-500 block">Stok rendah</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($product->is_active)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3"></circle>
                                                    </svg>
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <svg class="mr-1.5 h-2 w-2 text-gray-400" fill="currentColor" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3"></circle>
                                                    </svg>
                                                    Nonaktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('admin.products.show', $product->id) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900 transition duration-150 ease-in-out"
                                                   title="Lihat Detail">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                                <a href="{{ route('admin.products.edit', $product->id) }}" 
                                                   class="text-yellow-600 hover:text-yellow-900 transition duration-150 ease-in-out"
                                                   title="Edit Produk">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                                <form
                                                    action="{{ route('admin.products.destroy' , $product->id) }}"
                                                    method="POST"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                    type="submit"
                                                        class="text-red-600 hover:text-red-900 transition duration-150 ease-in-out"
                                                        title="Hapus Produk">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada produk</h3>
                        <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan produk pertama Anda.</p>
                        <div class="mt-6">
                            <a href="{{ route('admin.products.create') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Produk
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div x-show="showBulkDeleteModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mt-2">Konfirmasi Hapus</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            Apakah Anda yakin ingin menghapus <span class="font-medium" x-text="selectedProducts.length"></span> produk yang dipilih?
                            <br>
                            Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                    <div class="flex items-center px-4 py-3 space-x-3">
                        <button @click="showBulkDeleteModal = false"
                                class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Batal
                        </button>
                        <button @click="bulkDeleteProducts()"
                                class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Hapus Semua
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="showImageModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click.self="showImageModal = false"
             class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
            <div class="relative max-w-4xl max-h-full p-4">
                <button @click="showImageModal = false" 
                        class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <img :src="imagePreview.src" 
                     :alt="imagePreview.alt"
                     class="max-w-full max-h-full rounded-lg shadow-lg">
                <div class="absolute bottom-4 left-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded">
                    <span x-text="imagePreview.alt"></span>
                </div>
            </div>
        </div>

        <div x-show="isLoading" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 shadow-lg">
                <div class="flex items-center space-x-3">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600"></div>
                    <span class="text-gray-700">Memproses...</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        function productManager() {
            return {
                
                selectedProducts: [],
                selectAll: false,
                searchQuery: '',
                selectedCategory: '',
                selectedStatus: '',
                filteredProducts: [],
                
                
                showDeleteModal: false,
                showBulkDeleteModal: false,
                showImageModal: false,
                isLoading: false,
                
                
                productToDelete: { id: null, name: '' },
                imagePreview: { src: '', alt: '' },

                
                init() {
                    this.initializeProductData();
                    this.filterProducts();
                },

                
                initializeProductData() {
                    const products = @json($products->items());
                    this.allProducts = products.map(product => ({
                        id: product.id,
                        name: product.name,
                        category: product.category.name,
                        is_active: product.is_active
                    }));
                },

                
                filterProducts() {
                    this.filteredProducts = this.allProducts.filter(product => {
                        const matchesSearch = this.searchQuery === '' || 
                            product.name.toLowerCase().includes(this.searchQuery.toLowerCase());
                        
                        const matchesCategory = this.selectedCategory === '' || 
                            product.category === this.selectedCategory;
                        
                        const matchesStatus = this.selectedStatus === '' || 
                            (this.selectedStatus === 'active' && product.is_active) ||
                            (this.selectedStatus === 'inactive' && !product.is_active);
                        
                        return matchesSearch && matchesCategory && matchesStatus;
                    });
                },

                
                shouldShowProduct(product) {
                    if (this.searchQuery === '' && this.selectedCategory === '' && this.selectedStatus === '') {
                        return true;
                    }
                    
                    const matchesSearch = this.searchQuery === '' || 
                        product.name.toLowerCase().includes(this.searchQuery.toLowerCase());
                    
                    const matchesCategory = this.selectedCategory === '' || 
                        product.category === this.selectedCategory;
                    
                    const matchesStatus = this.selectedStatus === '' || 
                        (this.selectedStatus === 'active' && product.is_active) ||
                        (this.selectedStatus === 'inactive' && !product.is_active);
                    
                    return matchesSearch && matchesCategory && matchesStatus;
                },

                
                toggleSelectAll() {
                    if (this.selectAll) {
                        this.selectedProducts = this.filteredProducts.map(p => p.id);
                    } else {
                        this.selectedProducts = [];
                    }
                },

                
                $watch: {
                    selectedProducts(newVal) {
                        this.selectAll = newVal.length > 0 && newVal.length === this.filteredProducts.length;
                    }
                },

                
                confirmDelete(productId, productName) {
                    this.productToDelete = { id: productId, name: productName };
                    this.showDeleteModal = true;
                },

                
                async deleteProduct() {
                    this.isLoading = true;
                    this.showDeleteModal = false;
                    
                    try {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/products/${+this.productToDelete.id}`;
                        form.style.display = 'none';
                        
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        if (csrfToken) {
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = csrfToken.getAttribute('content');
                            form.appendChild(csrfInput);
                        }
                        
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        form.appendChild(methodInput);
                        
                        document.body.appendChild(form);
                        form.submit();
                    } catch (error) {
                        console.error('Error deleting product:', error);
                        alert('Terjadi kesalahan saat menghapus produk.');
                    } finally {
                        this.isLoading = false;
                    }
                },

                
                async bulkDeleteProducts() {
                    if (this.selectedProducts.length === 0) return;
                    
                    this.isLoading = true;
                    this.showBulkDeleteModal = false;
                    
                    try {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '/admin/products/bulk-destroy';
                        form.style.display = 'none';
                        
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        if (csrfToken) {
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = csrfToken.getAttribute('content');
                            form.appendChild(csrfInput);
                        }
                        
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        form.appendChild(methodInput);
                        
                        this.selectedProducts.forEach(productId => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'ids[]';
                            input.value = productId;
                            form.appendChild(input);
                        });
                        
                        document.body.appendChild(form);
                        form.submit();
                    } catch (error) {
                        console.error('Error bulk deleting products:', error);
                        alert('Terjadi kesalahan saat menghapus produk.');
                    } finally {
                        this.isLoading = false;
                    }
                },

                
                onClickShowImageModal(src, alt) {
                    this.imagePreview = { src, alt };
                    this.showImageModal = true;
                },

                
                formatCurrency(amount) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(amount);
                }
            }
        }
    </script>
</x-layouts.plain-app>
                           