@props(['order'])

<x-shared.modal 
    show="showStatusModal"
    type="warning" 
    title="Update Status Order"
    :closable="true"
    :backdrop="true"
    on-close="showStatusModal = false">
    
    <div>
        <p class="text-sm text-gray-500 mb-4">
            Ubah status untuk order "<strong>#{{ $order->order_number }}</strong>"
        </p>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status Baru</label>
            <select x-model="newStatus" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @foreach(\App\Enums\OrderStatus::cases() as $status)
                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="mt-4 p-3 bg-yellow-50 rounded-md">
            <p class="text-xs text-yellow-700">
                <strong>Catatan:</strong> Perubahan status akan mempengaruhi alur order dan notifikasi ke pelanggan.
            </p>
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
            @click="updateOrderStatus()"
            variant="warning"
        >
            <i class="fas fa-save mr-2"></i>
            Update Status
        </x-shared.button>
    </x-slot:footer>
</x-shared.modal>

<x-shared.modal 
    show="showCancelModal"
    type="danger" 
    title="Konfirmasi Batalkan Order"
    :closable="true"
    :backdrop="true"
    on-close="showCancelModal = false">
    
    <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
            <i class="fas fa-exclamation-triangle text-red-600"></i>
        </div>
        <p class="text-sm text-gray-700 mb-2">
            Apakah Anda yakin ingin membatalkan order ini?
        </p>
        <p class="text-xs text-gray-500">
            Order #{{ $order->order_number }} akan dibatalkan dan tidak dapat dikembalikan.
        </p>
        
        @if($order->transaction && $order->transaction->status->isSuccess())
            <div class="mt-4 p-3 bg-red-50 rounded-md">
                <p class="text-xs text-red-700">
                    <strong>Perhatian:</strong> Order ini sudah dibayar. Pembatalan akan memerlukan proses refund.
                </p>
            </div>
        @endif
    </div>
    
    <x-slot:footer>
        <x-shared.button
            @click="showCancelModal = false"
            variant="light"
        >
            Tidak, Kembali
        </x-shared.button>
        <x-shared.button
            @click="cancelOrder()"
            variant="danger"
        >
            <i class="fas fa-times mr-2"></i>
            Ya, Batalkan Order
        </x-shared.button>
    </x-slot:footer>
</x-shared.modal>

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