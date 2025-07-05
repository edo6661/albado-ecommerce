<x-layouts.plain-app>
    <div class="max-w-4xl mx-auto p-6 bg-white" x-data="checkoutSummary()">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Ringkasan Pesanan</h1>
            <p class="text-gray-600">Order #{{ $order->order_number }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="space-y-6">
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fa-solid fa-box mr-2"></i>
                        Item Pesanan
                    </h2>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                        <div class="flex items-center space-x-4 p-4 bg-white rounded-lg border">
                            <div class="flex-shrink-0">
                                <img src="{{ $item->product->images->first()?->path ?? '/default-product.jpg' }}" 
                                     alt="{{ $item->product_name }}"
                                     class="w-16 h-16 object-cover rounded-lg">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-gray-900">{{ $item->product_name }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ number_format($item->product_price, 0, ',', '.') }} × {{ $item->quantity }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">
                                    Rp {{ number_format($item->total, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fa-solid fa-receipt mr-2"></i>
                        Ringkasan Biaya
                    </h2>
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
                        <div class="border-t pt-3">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total</span>
                                <span class="text-blue-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-6 rounded-lg border border-blue-200">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fa-solid fa-shopping-cart mr-2"></i>
                        Proses Pesanan Anda
                    </h2>
                    
                    <div class="bg-white p-4 rounded-lg mb-4 border border-blue-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-check text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Pesanan Siap Diproses</h3>
                                <p class="text-sm text-gray-600">Klik tombol di bawah untuk melanjutkan proses order</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button @click="processPayment()" 
                                :disabled="loading"
                                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-4 px-6 rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200 shadow-lg">
                            <template x-if="loading">
                                <span class="flex items-center justify-center">
                                    <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                                    Memproses Pesanan...
                                </span>
                            </template>
                            <template x-if="!loading">
                                <span class="flex items-center justify-center">
                                    <i class="fa-solid fa-credit-card mr-2"></i>
                                    Proses Order
                                </span>
                            </template>
                        </button>
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fa-solid fa-info-circle mr-2"></i>
                        Status Pesanan
                    </h2>
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $order->status->label() }}</p>
                            <p class="text-sm text-gray-600">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
                    <div class="flex items-start space-x-3">
                        <i class="fa-solid fa-exclamation-triangle text-amber-600 text-xl mt-1"></i>
                        <div>
                            <h3 class="font-semibold text-amber-800">Catatan Penting</h3>
                            <p class="text-sm text-amber-700 mt-1">
                                Pastikan informasi pesanan sudah benar sebelum melanjutkan proses. 
                                Setelah diproses, pesanan tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function checkoutSummary() {
            return {
                loading: false,
                
                async processPayment() {
                    if (this.loading) return;
                    
                    this.loading = true;
                    
                    try {
                        const snapToken = '{{ $order->transaction->snap_token ?? "" }}';
                        
                        if (snapToken) {
                            window.snap.pay(snapToken, {
                                onSuccess: function(result) {
                                    this.updateTransactionStatus(result, 'settlement');
                                }.bind(this),
                                onPending: function(result) {
                                    this.updateTransactionStatus(result, 'pending');
                                }.bind(this),
                                onClose: function() {
                                    this.updateTransactionStatus(null, 'pending');
                                }.bind(this),
                                onError: function(result) {
                                    this.updateTransactionStatus(result, 'pending');
                                }.bind(this)
                            });
                        } else {
                            alert('Session expired, silakan checkout ulang');
                            window.location.href = '/'; 
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
                                order_id: {{ $order->id }},
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

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
</x-layouts.plain-app>