{{-- resources/views/components/shared/features/transaction/transaction-empty-state.blade.php --}}

<div class="text-center py-12">
    <div class="mx-auto h-24 w-24 text-gray-400">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
    </div>
    <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak ada transaksi</h3>
    <p class="mt-2 text-sm text-gray-500">
        Belum ada transaksi yang sesuai dengan filter yang dipilih.
    </p>
    <div class="mt-6">
        <x-shared.button
            @click="searchQuery = ''; selectedStatus = ''; selectedPaymentType = ''; selectedOrder = ''; dateFrom = ''; dateTo = ''; applyFilters()"
            variant="primary"
        >
            Reset Filter
        </x-shared.button>
    </div>
</div>