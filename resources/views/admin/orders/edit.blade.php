<x-layouts.plain-app>
    <x-slot:title>Edit Order #{{ $order->order_number }}</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8" x-data="orderEditForm()">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.orders.show', $order->id) }}"
                       class="text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-extrabold text-gray-900">Edit Order</h1>
                        <p class="mt-2 text-sm text-gray-600">
                            Perbarui informasi order: #{{ $order->order_number }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <form method="POST" action="{{ route('admin.orders.update', $order->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="px-6 py-6 space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Order</h3>
                            
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <x-shared.forms.input
                                        name="order_number"
                                        label="Nomor Order"
                                        :value="$order->order_number"
                                        readonly
                                        disabled
                                        help-text="Nomor order tidak dapat diubah"
                                    />
                                </div>

                                <div>
                                    <x-shared.forms.input
                                        name="customer_name"
                                        label="Nama Pelanggan"
                                        :value="$order->user->name"
                                        readonly
                                        disabled
                                    />
                                </div>

                                <div>
                                    <x-shared.forms.input
                                        name="customer_email"
                                        label="Email Pelanggan"
                                        :value="$order->user->email"
                                        readonly
                                        disabled
                                    />
                                </div>

                                <div>
                                    <x-shared.forms.select
                                        name="status"
                                        label="Status Order"
                                        :options="$statusOptions"
                                        :value="$order->status->value"
                                        placeholder="Pilih status order"
                                        disabled
                                    />
                                </div>

                                <div>
                                    <x-shared.forms.input
                                        name="created_at"
                                        label="Tanggal Order"
                                        :value="$order->created_at->format('d M Y, H:i')"
                                        readonly
                                        disabled
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Item Order</h3>
                            
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="space-y-3">
                                    @foreach($order->items as $item)
                                        <div class="flex items-center justify-between py-3 px-4 bg-white rounded-md shadow-sm">
                                            <div class="flex items-center space-x-4">
                                                @if($item->product && $item->product->images->isNotEmpty())
                                                    <img src="{{ $item->product->images->first()->path_url }}" 
                                                         alt="{{ $item->product_name }}"
                                                         class="w-12 h-12 rounded-lg object-cover">
                                                @else
                                                    <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                        <i class="fas fa-image text-gray-400 text-sm"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h4 class="font-medium text-gray-900 text-sm">{{ $item->product_name }}</h4>
                                                    <p class="text-xs text-gray-500">
                                                        Rp {{ number_format($item->product_price, 0, ',', '.') }} × {{ $item->quantity }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-medium text-gray-900 text-sm">
                                                    Rp {{ number_format($item->total, 0, ',', '.') }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="border-t border-gray-200 mt-4 pt-4">
                                    <div class="flex justify-end space-y-1">
                                        <div class="text-right space-y-1">
                                            <div class="flex justify-between min-w-48">
                                                <span class="text-sm text-gray-600">Subtotal:</span>
                                                <span class="text-sm font-medium">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                                            </div>
                                            @if($order->tax > 0)
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-600">Pajak:</span>
                                                    <span class="text-sm font-medium">Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                                                </div>
                                            @endif
                                            <div class="flex justify-between border-t border-gray-200 pt-1">
                                                <span class="text-base font-medium text-gray-900">Total:</span>
                                                <span class="text-base font-bold text-gray-900">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Item order tidak dapat diubah. Untuk mengubah item, batalkan order ini dan buat order baru.
                            </p>
                        </div>

                        @if($order->transaction)
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pembayaran</h3>
                                
                                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-600">Status Pembayaran:</span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                              :class="getPaymentStatusClass('{{ $order->transaction->status->value }}')">
                                            {{ $order->transaction->status->label() }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-600">Metode Pembayaran:</span>
                                        <span class="text-sm text-gray-900">{{ $order->transaction->payment_method ?? 'Belum dipilih' }}</span>
                                    </div>
                                    @if($order->transaction->paid_at)
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-gray-600">Dibayar pada:</span>
                                            <span class="text-sm text-gray-900">{{ $order->transaction->paid_at->format('d M Y, H:i') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Catatan Order</h3>
                            
                            <x-shared.forms.textarea
                                name="notes"
                                label="Catatan Internal"
                                placeholder="Tambahkan catatan untuk order ini..."
                                :rows="4"
                                :value="$order->notes"
                                help-text="Catatan ini hanya untuk keperluan internal admin dan tidak akan terlihat oleh pelanggan."
                            />
                        </div>

                        <div x-show="showStatusWarning" 
                             class="border-t border-gray-200 pt-6">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">Perhatian Perubahan Status</h3>
                                        <div class="mt-2 text-sm text-yellow-700">
                                            <p x-text="statusWarningMessage"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
                        <x-shared.button
                            variant="light"
                            href="{{ route('admin.orders.show', $order->id) }}"
                        >
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali ke Detail
                        </x-shared.button>

                        <div class="flex space-x-3">
                            <x-shared.button
                                type="submit"
                                variant="primary"
                            >
                                <i class="fas fa-save mr-2"></i>
                                Simpan Perubahan
                            </x-shared.button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function orderEditForm() {
            return {
                orderStatus: '{{ $order->status->value }}',
                originalStatus: '{{ $order->status->value }}',
                showStatusWarning: false,
                statusWarningMessage: '',
                
                handleStatusChange() {
                    if (this.orderStatus !== this.originalStatus) {
                        this.showStatusWarning = true;
                        this.generateStatusWarning();
                    } else {
                        this.showStatusWarning = false;
                    }
                },
                
                generateStatusWarning() {
                    const statusMessages = {
                        'pending': 'Order akan kembali ke status menunggu konfirmasi.',
                        'confirmed': 'Order akan dikonfirmasi dan siap diproses.',
                        'processing': 'Order akan masuk ke tahap pemrosesan.',
                        'shipped': 'Order akan ditandai sebagai sudah dikirim. Pastikan produk sudah benar-benar dikirim.',
                        'delivered': 'Order akan ditandai sebagai sudah diterima pelanggan.',
                        'cancelled': 'Order akan dibatalkan. Tindakan ini mungkin memerlukan proses refund jika sudah dibayar.',
                        'refunded': 'Order akan ditandai sebagai sudah di-refund.'
                    };
                    
                    this.statusWarningMessage = statusMessages[this.orderStatus] || 'Status order akan diubah.';
                },
                
                getPaymentStatusClass(status) {
                    const statusClasses = {
                        'pending': 'bg-yellow-100 text-yellow-800',
                        'success': 'bg-green-100 text-green-800',
                        'failed': 'bg-red-100 text-red-800',
                        'expired': 'bg-gray-100 text-gray-800'
                    };
                    return statusClasses[status] || 'bg-gray-100 text-gray-800';
                }
            }
        }
    </script>
</x-layouts.plain-app>