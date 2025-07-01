@props([
    'createRoute' => ''
])

<div class="text-center py-12">
    <i class="fas fa-folder-open text-6xl text-gray-400 mb-4"></i>
    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada kategori</h3>
    <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan kategori pertama untuk produk Anda.</p>
    <div class="mt-6">
        <x-shared.button
            :href="$createRoute"
            variant="primary"
            icon='<i class="fas fa-plus mr-2"></i>'
        >
            Tambah Kategori
        </x-shared.button>
    </div>
</div>