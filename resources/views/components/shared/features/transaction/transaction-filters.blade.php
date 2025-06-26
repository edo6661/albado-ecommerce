{{-- resources/views/components/shared/features/transaction/transaction-filters.blade.php --}}
@props([
    'statusOptions' => [],
    'paymentOptions' => [],
    'orderOptions' => [],
    'filters' => []
])

<div class="bg-white shadow-sm rounded-lg p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
        <div>
            <x-shared.forms.input
                name="search"
                id="search"
                label="Cari Transaksi"
                placeholder="ID Transaksi, nomor pesanan, atau nama..."
                x-model="searchQuery"
            />
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Filter Status</label>
            <x-shared.forms.select
                name="status"
                id="status"
                :options="$statusOptions"
                placeholder="Semua Status"
                x-model="selectedStatus"
            />
        </div>
        <div>
            <label for="payment_type" class="block text-sm font-medium text-gray-700 mb-2">Jenis Pembayaran</label>
            <x-shared.forms.select
                name="payment_type"
                id="payment_type"
                :options="$paymentOptions"
                placeholder="Semua Jenis"
                x-model="selectedPaymentType"
            />
        </div>
        <div>
            <label for="order" class="block text-sm font-medium text-gray-700 mb-2">Filter Pesanan</label>
            <x-shared.forms.select
                name="order_id"
                id="order"
                :options="$orderOptions"
                placeholder="Semua Pesanan"
                x-model="selectedOrder"
            />
        </div>
        <div>
            <x-shared.forms.input
                name="date_from"
                id="date_from"
                type="date"
                label="Tanggal Mulai"
                x-model="dateFrom"
            />
        </div>
        <div>
            <x-shared.forms.input
                name="date_to"
                id="date_to"
                type="date"
                label="Tanggal Akhir"
                x-model="dateTo"
            />
        </div>
    </div>
    <div class="mt-4 flex justify-end space-x-2">
        <x-shared.button
            @click="searchQuery = ''; selectedStatus = ''; selectedPaymentType = ''; selectedOrder = ''; dateFrom = ''; dateTo = ''; applyFilters()"
            variant="light"
        >
            Reset Filter
        </x-shared.button>
        <x-shared.button
            @click="applyFilters()"
            variant="primary"
            icon='<i class="fas fa-filter mr-2"></i>'
        >
            Terapkan Filter
        </x-shared.button>
    </div>
</div>