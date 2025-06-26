@props([
    'totalProducts' => 0,
    'selectedCount' => 0,
    'createRoute' => ''
])

<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900">Data Produk</h1>
        <p class="mt-2 text-sm text-gray-600">
            Kelola semua produk di toko Anda
        </p>
    </div>
    <div class="flex items-center space-x-3">
        <x-shared.button
            x-show="selectedProducts.length > 0" 
            @click="showBulkDeleteModal = true"
            variant="danger"
            icon='<i class="fas fa-trash mr-2"></i>'
        >
            Hapus Terpilih (<span x-text="selectedProducts.length"></span>)
        </x-shared.button>
        
        <x-shared.button
            :href="$createRoute"
            variant="primary"
            icon='<i class="fas fa-plus mr-2"></i>'
        >
            Tambah Produk
        </x-shared.button>
    </div>
</div>