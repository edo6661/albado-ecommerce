@props([
    'totalOrders' => 0,
    'selectedCount' => 0
])

<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900">Data Pesanan</h1>
        <p class="mt-2 text-sm text-gray-600">
            Kelola semua pesanan pelanggan
        </p>
    </div>
    <div class="flex items-center space-x-3">
        <x-shared.button
            x-show="selectedOrders.length > 0" 
            @click="showBulkDeleteModal = true"
            variant="danger"
            icon='<i class="fas fa-trash mr-2"></i>'
        >
            Hapus Terpilih (<span x-text="selectedOrders.length"></span>)
        </x-shared.button>
        
    </div>
</div>