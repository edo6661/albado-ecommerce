<x-shared.modal 
    show="showBulkDeleteModal"
    type="danger" 
    title="Konfirmasi Hapus"
    :closable="true"
    :backdrop="true"
    on-close="showBulkDeleteModal = false">
    
    <div class="text-center">
        <p class="text-sm text-gray-500">
            Apakah Anda yakin ingin menghapus <span class="font-medium" x-text="selectedProducts.length"></span> produk yang dipilih?
            <br>
            Tindakan ini tidak dapat dibatalkan.
        </p>
    </div>
    
    <x-slot:footer>
        <x-shared.button
            @click="showBulkDeleteModal = false"
            variant="light"
        >
            Batal
        </x-shared.button>
        <x-shared.button
            @click="bulkDeleteProducts()"
            variant="danger"
        >
            Hapus Semua
        </x-shared.button>
    </x-slot:footer>
</x-shared.modal>

<x-shared.modal 
    show="showDeleteModal"
    type="danger" 
    title="Konfirmasi Hapus"
    :closable="true"
    :backdrop="true"
    on-close="showDeleteModal = false">
    
    <div class="text-center">
        <p class="text-sm text-gray-500">
            Apakah Anda yakin ingin menghapus produk "<strong x-text="productToDelete.name"></strong>"?
            Tindakan ini tidak dapat dibatalkan.
        </p>
    </div>
    
    <x-slot:footer>
        <x-shared.button
            @click="showDeleteModal = false"
            variant="light"
        >
            Batal
        </x-shared.button>
        <x-shared.button
            @click="deleteProduct()"
            variant="danger"
        >
            Ya, Hapus
        </x-shared.button>
    </x-slot:footer>
</x-shared.modal>

<x-shared.modal 
    show="showImageModal"
    size="4xl"
    :closable="true"
    :backdrop="true"
    overlay-class="bg-black bg-opacity-75"
    modal-class="bg-transparent shadow-none"
    :animation="true"
    on-close="showImageModal = false">
    
    <div class="relative">
        <img :src="imagePreview.src" 
            :alt="imagePreview.alt"
            class="max-w-full max-h-[80vh] rounded-lg shadow-lg mx-auto">
        <div class="absolute bottom-4 left-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded">
            <span x-text="imagePreview.alt"></span>
        </div>
    </div>
</x-shared.modal>

<x-shared.modal 
    show="isLoading"
    size="sm"
    :closable="false"
    :backdrop="false"
    :animation="true">
    
    <div class="flex items-center justify-center space-x-3">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600"></div>
        <span class="text-gray-700">Memproses...</span>
    </div>
</x-shared.modal>