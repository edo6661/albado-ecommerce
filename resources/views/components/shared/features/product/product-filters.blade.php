@props([
    'categoryOptions' => [],
])

<div class="bg-white shadow-sm rounded-lg p-6 mb-6 flex flex-col space-y-8 ">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <x-shared.forms.input
                name="search"
                id="search"
                label="Cari Produk"
                placeholder="Nama produk..."
                x-model="searchQuery"
                @input.debounce.300ms="filterProducts()"
            />
        </div>
        <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Filter Kategori</label>
            <x-shared.forms.select
                name="category"     
                id="category"
                :options="$categoryOptions"
                placeholder="Semua Kategori"
                x-model="selectedCategory"
                @change="filterProducts()"
            />
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Filter Status</label>
            <x-shared.forms.select
                name="status"
                id="status"
                :options="['active' => 'Aktif', 'inactive' => 'Nonaktif']"
                placeholder="Semua Status"
                x-model="selectedStatus"
                @change="filterProducts()"
            />
        </div>
        <x-shared.button
            @click="exportToPdf()" 
            variant="secondary"
        >
            Export PDF
        </x-shared.button>
    </div>
</div>