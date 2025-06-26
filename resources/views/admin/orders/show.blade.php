<x-layouts.plain-app>
    <x-slot:title>Detail Order #{{ $order->order_number }}</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8" x-data="orderDetail()">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.orders.index') }}"
                           class="text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="text-3xl font-extrabold text-gray-900">Detail Order</h1>
                            <p class="mt-2 text-sm text-gray-600">
                                Order #{{ $order->order_number }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        @if($order->canBeCancelled())
                            <x-shared.button
                                @click="showCancelModal = true"
                                variant="danger"
                                icon='<i class="fas fa-times mr-2"></i>'
                            >
                                Batalkan Order
                            </x-shared.button>
                        @endif
                        
                        <x-shared.button
                            @click="showStatusModal = true"
                            variant="secondary"
                            icon='<i class="fas fa-edit mr-2"></i>'
                        >
                            Update Status
                        </x-shared.button>
                        
                        <x-shared.button
                            :href="route('admin.orders.edit', $order->id)"
                            variant="primary"
                            icon='<i class="fas fa-edit mr-2"></i>'
                        >
                            Edit Order
                        </x-shared.button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Order Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Order Items -->
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Item Order</h3>
                        </div>
                        <div class="px-6 py-4">
                            <div class="space-y-4">
                                @foreach($order->items as $item)
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
                                                <p class="text-sm text-gray-500">{{ number_format($item->product_price, 0, ',', '.') }} x {{ $item->quantity }}</p>
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

                    <!-- Order Summary -->
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Ringkasan Order</h3>
                        </div>
                        <div class="px-6 py-4">
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="font-medium">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                                </div>
                                @if($order->tax > 0)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Pajak</span>
                                        <span class="font-medium">Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="border-t border-gray-200 pt-3">
                                    <div class="flex justify-between">
                                        <span class="text-lg font-medium text-gray-900">Total</span>
                                        <span class="text-lg font-bold text-gray-900">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($order->notes)
                        <!-- Notes -->
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Catatan</h3>
                            </div>
                            <div class="px-6 py-4">
                                <p class="text-gray-700">{{ $order->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Order Status -->
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Status Order</h3>
                        </div>
                        <div class="px-6 py-4">
                            <div class="text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                      :class="getStatusBadgeClass('{{ $order->status->value }}')">
                                    {{ $order->status->label() }}
                                </span>
                                <p class="text-xs text-gray-500 mt-2">
                                    Dibuat: {{ $order->created_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Informasi Pelanggan</h3>
                        </div>
                        <div class="px-6 py-4 space-y-3">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $order->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $order->user->email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    @if($order->transaction)
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Informasi Pembayaran</h3>
                            </div>
                            <div class="px-6 py-4 space-y-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Status Pembayaran</p>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                          :class="getPaymentStatusClass('{{ $order->transaction->status->value }}')">
                                        {{ $order->transaction->status->label() }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Metode Pembayaran</p>
                                    <p class="text-sm text-gray-500">{{ $order->transaction->payment_method ?? 'Belum dipilih' }}</p>
                                </div>
                                @if($order->transaction->paid_at)
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Dibayar pada</p>
                                        <p class="text-sm text-gray-500">{{ $order->transaction->paid_at->format('d M Y, H:i') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <x-shared.features.order.show-modals :order="$order" />
    </div>

    <script>
        function orderDetail() {
            return {
                showStatusModal: false,
                showCancelModal: false,
                isLoading: false,
                newStatus: '{{ $order->status->value }}',
                
                getStatusBadgeClass(status) {
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
                
                getPaymentStatusClass(status) {
                    const statusClasses = {
                        'pending': 'bg-yellow-100 text-yellow-800',
                        'success': 'bg-green-100 text-green-800',
                        'failed': 'bg-red-100 text-red-800',
                        'expired': 'bg-gray-100 text-gray-800'
                    };
                    return statusClasses[status] || 'bg-gray-100 text-gray-800';
                },
                
                async updateOrderStatus() {
                    this.isLoading = true;
                    this.showStatusModal = false;
                    
                    try {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/orders/{{ $order->id }}/status`;
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
                        console.error('Error updating order status:', error);
                        alert('Terjadi kesalahan saat mengubah status order.');
                    } finally {
                        this.isLoading = false;
                    }
                },
                
                async cancelOrder() {
                    this.isLoading = true;
                    this.showCancelModal = false;
                    
                    try {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/orders/{{ $order->id }}/cancel`;
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
                        
                        document.body.appendChild(form);
                        form.submit();
                    } catch (error) {
                        console.error('Error cancelling order:', error);
                        alert('Terjadi kesalahan saat membatalkan order.');
                    } finally {
                        this.isLoading = false;
                    }
                }
            }
        }
    </script>
</x-layouts.plain-app>