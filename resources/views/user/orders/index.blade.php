<x-layouts.plain-app>
    <div class="max-w-6xl mx-auto p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Riwayat Pesanan</h1>
            <p class="text-gray-600">Kelola pesanan dan transaksi Anda</p>
        </div>

        @if($orders->isEmpty())
            <div class="text-center py-12">
                <i class="fa-solid fa-box-open text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum ada pesanan</h3>
                <p class="text-gray-600 mb-4">Mulai berbelanja sekarang</p>
                <a href="{{ route('home') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Mulai Belanja
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($orders as $order)
                    <div class="bg-white border rounded-lg p-6" x-data="orderItem({{ $order->id }})">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $order->order_number }}</h3>
                                <p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                        @if($order->status->value === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status->value === 'paid') bg-green-100 text-green-800
                                        @elseif($order->status->value === 'failed') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $order->status->label() }}
                                    </span>
                                </div>
                                <p class="font-bold text-blue-600">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <div class="space-y-2">
                                    @foreach($order->items->take(3) as $item)
                                        <div class="flex items-center space-x-3 my-2">
                                            <a href="{{ route('products.show', $item->product->slug) }}" class="flex items-center space-x-3">
                                                <img src="{{ $item->product->images->first()?->path_url ?? '/default-product.jpg' }}" 
                                                     class="w-10 h-10 object-cover rounded">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $item->product_name }}</p>
                                                    <p class="text-xs text-gray-500">{{ $item->quantity }}x</p>
                                                </div>
                                            </a>
                                            
                                            @if($order->status->value === 'delivered')
                                                @if(!$item->user_has_rated)
                                                    <a href="{{ route('ratings.create', [
                                                        'product' => $item->product_id
                                                    ]) }}" class="text-blue-600 hover:underline text-sm">
                                                        Rating
                                                    </a>
                                                @else
                                                    <div class="flex items-center space-x-1">
                                                        <div class="flex text-yellow-400">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                @if($i <= $item->user_rating->rating)
                                                                    <i class="fa-solid fa-star text-xs"></i>
                                                                @else
                                                                    <i class="fa-regular fa-star text-xs"></i>
                                                                @endif
                                                            @endfor
                                                        </div>
                                                        <span class="text-gray-600 text-xs">{{ $item->user_rating->rating }}/5</span>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    @endforeach
                                    @if($order->items->count() > 3)
                                        <p class="text-xs text-gray-500">+{{ $order->items->count() - 3 }} item lainnya</p>
                                    @endif
                                </div>
                            </div>

                            @if($order->transaction)
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-2">Status Pembayaran</h4>
                                    <div class="space-y-1">
                                        <p class="text-sm text-gray-600">
                                            Status: 
                                            <span class="font-medium 
                                                @if($order->transaction->isPending()) text-yellow-600
                                                @elseif($order->transaction->isSuccess()) text-green-600
                                                @else text-red-600
                                                @endif">
                                                {{ $order->transaction->status->label() }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t">
                            <div class="flex space-x-2">
                                <a href="{{ route('orders.show', $order) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Lihat Detail
                                </a>
                            </div>
                            
                            <div class="flex space-x-2">
                                @if($order->transaction && $order->transaction->isPending() && $order->transaction->snap_token)
                                    <button @click="resumePayment()" 
                                            :disabled="loading"
                                            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 disabled:opacity-50">
                                        <span x-show="!loading">Lanjutkan Pembayaran</span>
                                        <span x-show="loading">Processing...</span>
                                    </button>
                                @endif
                                
                                @if($order->status->value === 'shipped') 
                                    <a href="{{ route('orders.track', $order->id) }}" class="text-blue-600 hover:underline">
                                        Lacak Pesanan
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    
    <script>
        function orderItem(orderId) {
            
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