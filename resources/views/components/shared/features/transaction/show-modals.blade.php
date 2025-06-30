@props(['transaction'])

<!-- Update Status Modal -->
<x-shared.modal 
    show="showUpdateStatusModal"
    type="warning" 
    title="Update Status Transaksi"
    :closable="true"
    :backdrop="true"
    on-close="showUpdateStatusModal = false">
    
    <div>
        <p class="text-sm text-gray-500 mb-4">
            Ubah status untuk transaksi "<strong>#{{ $transaction->transaction_id }}</strong>"
        </p>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status Baru</label>
            <select x-model="newStatus" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @foreach(\App\Enums\TransactionStatus::cases() as $status)
                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="mt-4 p-3 bg-yellow-50 rounded-md">
            <p class="text-xs text-yellow-700">
                <strong>Catatan:</strong> Perubahan status transaksi akan mempengaruhi alur pembayaran dan status order terkait.
            </p>
        </div>
    </div>
    
    <x-slot:footer>
        <x-shared.button
            @click="showUpdateStatusModal = false"
            variant="light"
        >
            Batal
        </x-shared.button>
        <x-shared.button
            @click="updateTransactionStatus()"
            variant="warning"
        >
            <i class="fas fa-save mr-2"></i>
            Update Status
        </x-shared.button>
    </x-slot:footer>
</x-shared.modal>

<!-- Loading Modal -->
<x-shared.modal 
    show="isLoading"
    size="sm"
    :closable="false"
    :backdrop="false"
    :animation="true">
    
    <div class="flex items-center justify-center space-x-3 py-4">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
        <span class="text-gray-700 font-medium">Memproses...</span>
    </div>
</x-shared.modal>