{{-- resources/views/components/shared/features/transaction/transaction-modals.blade.php --}}
@props([
    'statusOptions' => []
])

<!-- Modal Update Status -->
<x-shared.modal 
    show="showStatusModal"
    type="warning" 
    title="Update Status Transaksi"
    :closable="true"
    :backdrop="true"
    on-close="showStatusModal = false">
    
    <div>
        <p class="text-sm text-gray-500 mb-4">
            Ubah status untuk transaksi "<strong x-text="transactionToUpdateStatus.transactionId"></strong>"
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

