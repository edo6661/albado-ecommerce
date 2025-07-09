<x-layouts.plain-app>
    <div class="max-w-4xl mx-auto p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Pesanan</h1>
                <p class="text-gray-600">{{ $order->order_number }}</p>
            </div>
        </div>

        <div class="bg-white border rounded-lg p-6 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">Status Pesanan</h2>
                    <div class="flex items-center space-x-3">
                        <span class="px-3 py-1 rounded-full text-sm font-medium 
                            @if($order->status->value === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->status->value === 'paid') bg-green-100 text-green-800
                            @elseif($order->status->value === 'failed') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $order->status->label() }}
                        </span>
                        <span class="text-sm text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Total Pesanan</p>
                    <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        @if($order->transaction)
        <div class="bg-white border rounded-lg p-6 mb-6" x-data="paymentHandler({{ $order->id }})">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Status Pembayaran</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">ID Transaksi:</span>
                        <span class="font-medium">{{ $order->transaction->transaction_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="font-medium 
                            @if($order->transaction->isPending()) text-yellow-600
                            @elseif($order->transaction->isSuccess()) text-green-600
                            @else text-red-600
                            @endif">
                            {{ $order->transaction->status->label() }}
                        </span>
                    </div>
                    @if($order->transaction->payment_type)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Metode Pembayaran:</span>
                        <span class="font-medium">{{ $order->transaction->payment_type->label() }}</span>
                    </div>
                    @endif
                </div>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Waktu Transaksi:</span>
                        <span class="font-medium">{{ $order->transaction->transaction_time?->format('d M Y, H:i') ?? '-' }}</span>
                    </div>
                    @if($order->transaction->settlement_time)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Waktu Settlement:</span>
                        <span class="font-medium">{{ $order->transaction->settlement_time->format('d M Y, H:i') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-600">Jumlah:</span>
                        <span class="font-medium">Rp {{ number_format($order->transaction->gross_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            @if($order->transaction->isPending() && $order->transaction->snap_token)
            <div class="mt-6 pt-6 border-t">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600">Pembayaran belum diselesaikan</p>
                        <p class="text-xs text-gray-500">Selesaikan pembayaran untuk melanjutkan pesanan</p>
                    </div>
                    <button @click="resumePayment()" 
                            :disabled="loading"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50">
                        <span x-show="!loading">Bayar Sekarang</span>
                        <span x-show="loading">Processing...</span>
                    </button>
                </div>
            </div>
            @endif
        </div>
        @endif

        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Item Pesanan</h2>
            
            <div class="space-y-4">
                @foreach($order->items as $item)
                <div class="flex items-center space-x-4 p-4 border rounded-lg">
                    <img src="{{ $item->product->images->first()?->path_url ?? '/default-product.jpg' }}" 
                         alt="{{ $item->product_name }}"
                         class="w-16 h-16 object-cover rounded-lg">
                    
                    <div class="flex-1">
                        <h3 class="font-medium text-gray-900">{{ $item->product_name }}</h3>
                        <p class="text-sm text-gray-500">Harga: Rp {{ number_format($item->product_price, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-500">Jumlah: {{ $item->quantity }}</p>
                    </div>
                    
                    <div class="text-right">
                        <p class="font-medium text-gray-900">Rp {{ number_format($item->total, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Pesanan</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal ({{ $order->items->sum('quantity') }} item)</span>
                    <span class="font-medium">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                
                @if($order->tax > 0)
                <div class="flex justify-between">
                    <span class="text-gray-600">Pajak</span>
                    <span class="font-medium">Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                </div>
                @endif
                
                <div class="border-t pt-3">
                    <div class="flex justify-between">
                        <span class="text-lg font-semibold text-gray-900">Total</span>
                        <span class="text-lg font-bold text-blue-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($order->notes)
        <div class="bg-white border rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Catatan Pesanan</h2>
            <p class="text-gray-700">{{ $order->notes }}</p>
        </div>
        @endif

        <div class="flex justify-between items-center">
            
            <div class="flex space-x-3">
                <button onclick="window.print()" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    <i class="fa-solid fa-print mr-2"></i>
                    Cetak
                </button>
            </div>
        </div>
    </div>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    
    <script>
        function paymentHandler(orderId) {
            return {
                loading: false,
                
                async resumePayment() {
                    this.loading = true;
                    
                    try {
                        const response = await fetch(`/orders/${orderId}/resume-payment`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            const self = this;
                            
                            window.snap.pay(data.snap_token, {
                                onSuccess: function(result) {
                                    self.updateTransactionStatus(result, 'settlement');
                                },
                                onPending: function(result) {
                                    self.updateTransactionStatus(result, 'pending');
                                },
                                onClose: function() {
                                    window.location.href = '{{ route("payment.finish") }}';
                                },
                                onError: function(result) {
                                    console.error('Payment error:', result);
                                    alert('Terjadi kesalahan saat memproses pembayaran');
                                }
                            });
                        } else {
                            alert(data.message || 'Terjadi kesalahan');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat memproses pembayaran');
                    } finally {
                        this.loading = false;
                    }
                },
                
                async updateTransactionStatus(result, status) {
                    try {
                        const response = await fetch('{{ route("payment.callback") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                order_id: orderId,
                                transaction_status: status,
                                midtrans_result: result
                            })
                        });

                        const data = await response.json();
                        
                        if (data.success) {
                            window.location.href = '{{ route("payment.finish") }}';
                        } else {
                            console.error('Failed to update transaction status:', data.message);
                            window.location.href = '{{ route("payment.finish") }}';
                        }
                    } catch (error) {
                        console.error('Error updating transaction status:', error);
                        window.location.href = '{{ route("payment.finish") }}';
                    }
                }
            }
        }
    </script>
</x-layouts.plain-app>