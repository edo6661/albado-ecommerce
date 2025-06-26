@props([
    'totalCount' => 0
])

<div class="px-6 py-4 border-b border-gray-200">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-medium text-gray-900">
            <span x-show="filteredProducts.length === 0 && searchQuery === '' && selectedCategory === '' && selectedStatus === ''">
                Daftar Produk ({{ $totalCount }} produk)
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