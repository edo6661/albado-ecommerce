<x-layouts.plain-app>
    <x-slot:title>Detail Transaksi #{{ $transaction->transaction_id }}</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8" x-data="transactionDetail()">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.transactions.index') }}"
                           class="text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="text-3xl font-extrabold text-gray-900">Detail Transaksi</h1>
                            <p class="mt-2 text-sm text-gray-600">
                                Transaksi #{{ $transaction->transaction_id }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        @if($transaction->isPending())
                            <x-shared.button
                                @click="showUpdateStatusModal = true"
                                variant="secondary"
                                icon='<i class="fas fa-edit mr-2"></i>'
                            >
                                Update Status
                            </x-shared.button>
                        @endif
                        
                        @if($transaction->order)
                            <x-shared.button
                                :href="route('admin.orders.show', $transaction->order->id)"
                                variant="primary"
                                icon='<i class="fas fa-eye mr-2"></i>'
                            >
                                Lihat Order
                            </x-shared.button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Transaction Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Transaction Details -->
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Detail Transaksi</h3>
                        </div>
                        <div class="px-6 py-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">ID Transaksi</p>
                                    <p class="text-sm text-gray-500">{{ $transaction->transaction_id }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Order ID Midtrans</p>
                                    <p class="text-sm text-gray-500">{{ $transaction->order_id_midtrans ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Tipe Pembayaran</p>
                                    <p class="text-sm text-gray-500">{{ $transaction->payment_type ? $transaction->payment_type->label() : '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Mata Uang</p>
                                    <p class="text-sm text-gray-500">{{ $transaction->currency ?? 'IDR' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Waktu Transaksi</p>
                                    <p class="text-sm text-gray-500">{{ $transaction->transaction_time ? $transaction->transaction_time->format('d M Y, H:i') : '-' }}</p>
                                </div>
                                @if($transaction->settlement_time)
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Waktu Settlement</p>
                                        <p class="text-sm text-gray-500">{{ $transaction->settlement_time->format('d M Y, H:i') }}</p>
                                    </div>
                                @endif
                                @if($transaction->fraud_status)
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Status Fraud</p>
                                        <p class="text-sm text-gray-500">{{ $transaction->fraud_status }}</p>
                                    </div>
                                @endif
                                @if($transaction->status_message)
                                    <div class="md:col-span-2">
                                        <p class="text-sm font-medium text-gray-900">Pesan Status</p>
                                        <p class="text-sm text-gray-500">{{ $transaction->status_message }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Order Items (if exists) -->
                    @if($transaction->order && $transaction->order->items)
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Item Order</h3>
                            </div>
                            <div class="px-6 py-4">
                                <div class="space-y-4">
                                    @foreach($transaction->order->items as $item)
                                        <div class="flex items-center justify-between py-4 border-b border-gray-100 last:border-b-0">
                                            <div class="flex items-center space-x-4">
                                                @if($item->product && $item->product->images->isNotEmpty())
                                                    <img src="{{ $item->product->images->first()->path_url }}" 
                                                         alt="{{ $item->product_name }}"
                                                         class="w-16 h-16 rounded-lg object-cover">
                                                @else
                                                    <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                                        <i class="fas fa-image text-gray-400"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h4 class="font-medium text-gray-900">{{ $item->product_name }}</h4>
                                                    <p class="text-sm text-gray-500">Rp {{ number_format($item->product_price, 0, ',', '.') }} x {{ $item->quantity }}</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-medium text-gray-900">Rp {{ number_format($item->total, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Midtrans Response -->
                    @if($transaction->midtrans_response)
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Response Midtrans</h3>
                            </div>
                            <div class="px-6 py-4">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <pre class="text-xs text-gray-700 whitespace-pre-wrap">{{ json_encode($transaction->midtrans_response, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Transaction Status -->
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Status Transaksi</h3>
                        </div>
                        <div class="px-6 py-4">
                            <div class="text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                      :class="getTransactionStatusClass('{{ $transaction->status->value }}')">
                                    {{ $transaction->status->label() }}
                                </span>
                                <p class="text-xs text-gray-500 mt-2">
                                    Dibuat: {{ $transaction->created_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Amount Info -->
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Informasi Amount</h3>
                        </div>
                        <div class="px-6 py-4">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-900">
                                    Rp {{ number_format($transaction->gross_amount, 0, ',', '.') }}
                                </p>
                                <p class="text-sm text-gray-500 mt-1">Total Transaksi</p>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    @if($transaction->order && $transaction->order->user)
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Informasi Pelanggan</h3>
                            </div>
                            <div class="px-6 py-4 space-y-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $transaction->order->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $transaction->order->user->email }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Order Info -->
                    @if($transaction->order)
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Informasi Order</h3>
                            </div>
                            <div class="px-6 py-4 space-y-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Nomor Order</p>
                                    <p class="text-sm text-gray-500">{{ $transaction->order->order_number }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Status Order</p>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                          :class="getOrderStatusClass('{{ $transaction->order->status->value }}')">
                                        {{ $transaction->order->status->label() }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Total Item</p>
                                    <p class="text-sm text-gray-500">{{ $transaction->order->items->count() }} item</p>
                                </div>
                                @if($transaction->order->notes)
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Catatan</p>
                                        <p class="text-sm text-gray-500">{{ $transaction->order->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Modals -->
        <x-shared.features.transaction.show-modals :transaction="$transaction" />
    </div>

    <script>
        function transactionDetail() {
            return {
                showUpdateStatusModal: false,
                isLoading: false,
                newStatus: '{{ $transaction->status->value }}',
                
                getTransactionStatusClass(status) {
                    const statusClasses = {
                        'pending': 'bg-yellow-100 text-yellow-800',
                        'success': 'bg-green-100 text-green-800',
                        'failed': 'bg-red-100 text-red-800',
                        'expired': 'bg-gray-100 text-gray-800',
                        'cancelled': 'bg-red-100 text-red-800'
                    };
                    return statusClasses[status] || 'bg-gray-100 text-gray-800';
                },
                
                getOrderStatusClass(status) {
                    const statusClasses = {
                        'pending': 'bg-yellow-100 text-yellow-800',
                        'confirmed': 'bg-blue-100 text-blue-800',
                        'processing': 'bg-purple-100 text-purple-800',
                        'shipped': 'bg-indigo-100 text-indigo-800',
                        'delivered': 'bg-green-100 text-green-800',
                        'cancelled': 'bg-red-100 text-red-800',
                        'refunded': 'bg-gray-100 text-gray-800'
                    };
                    return statusClasses[status] || 'bg-gray-100 text-gray-800';
                },
                
                async updateTransactionStatus() {
                    this.isLoading = true;
                    this.showUpdateStatusModal = false;
                    
                    try {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/transactions/{{ $transaction->id }}/status`;
                        form.style.display = 'none';
                        
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        if (csrfToken) {
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = csrfToken.getAttribute('content');
                            form.appendChild(csrfInput);
                        }
                        
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'PATCH';
                        form.appendChild(methodInput);
                        
                        const statusInput = document.createElement('input');
                        statusInput.type = 'hidden';
                        statusInput.name = 'status';
                        statusInput.value = this.newStatus;
                        form.appendChild(statusInput);
                        
                        document.body.appendChild(form);
                        form.submit();
                    } catch (error) {
                        console.error('Error updating transaction status:', error);
                        alert('Terjadi kesalahan saat mengubah status transaksi.');
                    } finally {
                        this.isLoading = false;
                    }
                }
            }
        }
    </script>
</x-layouts.plain-app>