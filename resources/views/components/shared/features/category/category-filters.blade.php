<div class="bg-white shadow-sm rounded-lg p-6 mb-6 flex flex-col space-y-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-shared.forms.input
                name="search"
                id="search"
                label="Cari Kategori"
                placeholder="Nama atau slug kategori..."
                x-model="searchQuery"
                @input.debounce.300ms="filterCategories()"
            />
        </div>
    </div>
</div>