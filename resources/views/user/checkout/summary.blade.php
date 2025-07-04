<x-layouts.plain-app>
    <div class="max-w-4xl mx-auto p-6 bg-white" x-data="checkoutSummary()">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Ringkasan Pesanan</h1>
            <p class="text-gray-600">Order #{{ $order->order_number }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Order Items -->
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

                <!-- Order Summary -->
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

            <!-- Payment Options -->
            <div class="space-y-6">
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fa-solid fa-credit-card mr-2"></i>
                        Pilih Metode Pembayaran
                    </h2>
                    
                    <div class="space-y-4">
                        <!-- Pay Now -->
                        <div class="p-4 border-2 border-blue-200 rounded-lg bg-blue-50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold text-blue-900">Bayar Sekarang</h3>
                                    <p class="text-sm text-blue-700">Pembayaran langsung menggunakan berbagai metode</p>
                                </div>
                                <i class="fa-solid fa-bolt text-blue-600 text-2xl"></i>
                            </div>
                        </div>

                        <!-- Pay Later -->
                        <div class="p-4 border-2 border-gray-200 rounded-lg bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold text-gray-900">Bayar Nanti</h3>
                                    <p class="text-sm text-gray-600">Simpan pesanan dan bayar kemudian</p>
                                </div>
                                <i class="fa-solid fa-clock text-gray-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 space-y-3">
                        <button @click="processPayment('pay_now')" 
                                :disabled="loading"
                                class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200">
                            <i class="fa-solid fa-credit-card mr-2"></i>
                            <span x-show="!loading">Bayar Sekarang</span>
                            <span x-show="loading">Processing...</span>
                        </button>

                        <button @click="processPayment('pay_later')" 
                                :disabled="loading"
                                class="w-full bg-gray-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200">
                            <i class="fa-solid fa-clock mr-2"></i>
                            <span x-show="!loading">Bayar Nanti</span>
                            <span x-show="loading">Processing...</span>
                        </button>
                    </div>
                </div>

                <!-- Order Status -->
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
            </div>
        </div>
    </div>

    <script>
        function checkoutSummary() {
            return {
                loading: false,
                
                async processPayment(method) {
                    if (this.loading) return;
                    
                    this.loading = true;
                    
                    try {
                        const response = await fetch('{{ route("payment.process") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                order_id: {{ $order->id }},
                                payment_method: method
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            if (data.payment_type === 'midtrans') {
                                // Open Midtrans snap
                                window.snap.pay(data.snap_token, {
                                    onSuccess: function(result) {
                                        window.location.href = '{{ route("payment.finish") }}';
                                    },
                                    onPending: function(result) {
                                        window.location.href = '{{ route("payment.finish") }}';
                                    },
                                    onError: function(result) {
                                        alert('Pembayaran gagal: ' + result.status_message);
                                    }
                                });
                            } else if (data.payment_type === 'pay_later') {
                                window.location.href = data.redirect;
                            }
                        } else {
                            alert(data.message || 'Terjadi kesalahan');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat memproses pembayaran');
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>

    <!-- Midtrans Snap -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
</x-layouts.plain-app>