<x-shared.modal 
    show="showStatusModal"
    type="warning" 
    title="Update Status Pesanan"
    :closable="true"
    :backdrop="true"
    on-close="showStatusModal = false">
    
    <div>
        <p class="text-sm text-gray-500 mb-4">
            Ubah status untuk pesanan "<strong x-text="orderToUpdateStatus.number"></strong>"
        </p>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status Baru</label>
            <select x-model="newStatus" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
    
    <x-slot:footer>
        <x-shared.button
            @click="showStatusModal = false"
            variant="light"
        >
            Batal
        </x-shared.button>
        <x-shared.button
            @click="updateStatus()"
            variant="warning"
        >
            Update Status
        </x-shared.button>
    </x-slot:footer>
</x-shared.modal>

<!-- Modal Konfirmasi Hapus -->
<x-shared.modal 
    show="showDeleteModal"
    type="danger" 
    title="Konfirmasi Hapus"
    :closable="true"
    :backdrop="true"
    on-close="showDeleteModal = false">
    
    <div class="text-center">
        <p class="text-sm text-gray-500">
            Apakah Anda yakin ingin menghapus pesanan "<strong x-text="orderToDelete.number"></strong>"?
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
            @click="deleteOrder()"
            variant="danger"
        >
            Ya, Hapus
        </x-shared.button>
    </x-slot:footer>
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