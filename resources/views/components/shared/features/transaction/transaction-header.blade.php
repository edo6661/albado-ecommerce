{{-- resources/views/components/shared/features/transaction/transaction-header.blade.php --}}
@props([
    'totalTransactions' => 0,
    'selectedCount' => 0
])

<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900">Data Transaksi</h1>
        <p class="mt-2 text-sm text-gray-600">
            Kelola semua transaksi pembayaran pelanggan
        </p>
    </div>
    <div class="flex items-center space-x-3">
        <x-shared.button
            x-show="selectedTransactions.length > 0" 
            @click="showBulkDeleteModal = true"
            variant="danger"
            icon='<i class="fas fa-trash mr-2"></i>'
        >
            Hapus Terpilih (<span x-text="selectedTransactions.length"></span>)
        </x-shared.button>
    </div>
</div>