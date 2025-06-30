@props([
    'statusOptions' => [],
    'userOptions' => [],
    'filters' => []
])

<div class="bg-white shadow-sm rounded-lg p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div>
            <x-shared.forms.input
                name="search"
                id="search"
                label="Cari Pesanan"
                placeholder="Nomor pesanan atau nama pelanggan..."
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
            <label for="user" class="block text-sm font-medium text-gray-700 mb-2">Filter Pelanggan</label>
            <x-shared.forms.select
                name="user_id"
                id="user"
                :options="$userOptions"
                placeholder="Semua Pelanggan"
                x-model="selectedUser"
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
    <div class="flex items-center justify-between mt-4 flex-wrap space-y-4">
        <div class="mt-4 flex justify-start space-x-2 items-center">
            <x-shared.button
                @click="searchQuery = ''; selectedStatus = ''; selectedUser = ''; dateFrom = ''; dateTo = ''; applyFilters()"
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
        <x-shared.button
            @click="exportToPdf()" 
            variant="secondary"
        >
            Export To PDF
        </x-shared.button>
    </div>
    
</div>