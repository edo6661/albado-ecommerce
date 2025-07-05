<x-layouts.plain-app>
    <div class="max-w-4xl mx-auto p-6">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Dynamic Header Based on Payment Status -->
            @if($latestOrder && $latestOrder->latestTransaction)
                @if($latestOrder->latestTransaction->status->isSuccess())
                    <!-- Success Header -->
                    <div class="bg-gradient-to-r from-green-500 to-green-600 p-8 text-center text-white">
                        <div class="mb-4">
                            <i class="fa-solid fa-check-circle text-6xl mb-4"></i>
                            <h1 class="text-3xl font-bold mb-2">Pembayaran Selesai!</h1>
                            <p class="text-green-100 text-lg">Terima kasih atas pembelian Anda</p>
                        </div>
                    </div>
                @elseif($latestOrder->latestTransaction->status->isPending())
                    <!-- Pending Header -->
                    <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-8 text-center text-white">
                        <div class="mb-4">
                            <i class="fa-solid fa-clock text-6xl mb-4"></i>
                            <h1 class="text-3xl font-bold mb-2">Menunggu Pembayaran</h1>
                            <p class="text-yellow-100 text-lg">Silakan selesaikan pembayaran Anda</p>
                        </div>
                    </div>
                @elseif($latestOrder->latestTransaction->status->isFailed())
                    <!-- Failed Header -->
                    <div class="bg-gradient-to-r from-red-500 to-red-600 p-8 text-center text-white">
                        <div class="mb-4">
                            <i class="fa-solid fa-times-circle text-6xl mb-4"></i>
                            <h1 class="text-3xl font-bold mb-2">Pembayaran Gagal</h1>
                            <p class="text-red-100 text-lg">Terjadi kesalahan dalam proses pembayaran</p>
                        </div>
                    </div>
                @elseif($latestOrder->latestTransaction->status->isExpired())
                    <!-- Expired Header -->
                    <div class="bg-gradient-to-r from-gray-500 to-gray-600 p-8 text-center text-white">
                        <div class="mb-4">
                            <i class="fa-solid fa-clock-exclamation text-6xl mb-4"></i>
                            <h1 class="text-3xl font-bold mb-2">Pembayaran Kadaluarsa</h1>
                            <p class="text-gray-100 text-lg">Waktu pembayaran telah habis</p>
                        </div>
                    </div>
                @else
                    <!-- Default/Unknown Status Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-8 text-center text-white">
                        <div class="mb-4">
                            <i class="fa-solid fa-info-circle text-6xl mb-4"></i>
                            <h1 class="text-3xl font-bold mb-2">Status Pembayaran</h1>
                            <p class="text-blue-100 text-lg">{{ $latestOrder->latestTransaction->status->label() }}</p>
                        </div>
                    </div>
                @endif
            @else
                <!-- No Transaction/Order Header -->
                <div class="bg-gradient-to-r from-gray-500 to-gray-600 p-8 text-center text-white">
                    <div class="mb-4">
                        <i class="fa-solid fa-receipt text-6xl mb-4"></i>
                        <h1 class="text-3xl font-bold mb-2">Informasi Pesanan</h1>
                        <p class="text-gray-100 text-lg">Detail pesanan Anda</p>
                    </div>
                </div>
            @endif

            @if($latestOrder)
            <!-- Order Details -->
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column - Order Info -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fa-solid fa-receipt mr-2"></i>
                                Detail Pesanan
                            </h2>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Nomor Order:</span>
                                    <span class="font-medium text-blue-600">{{ $latestOrder->order_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Tanggal:</span>
                                    <span class="font-medium">{{ $latestOrder->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status Order:</span>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-{{ $latestOrder->status->color() }}-100 text-{{ $latestOrder->status->color() }}-800">
                                        {{ $latestOrder->status->label() }}
                                    </span>
                                </div>
                                @if($latestOrder->latestTransaction)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status Pembayaran:</span>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-{{ $latestOrder->latestTransaction->status->getColor() }}-100 text-{{ $latestOrder->latestTransaction->status->getColor() }}-800">
                                        {{ $latestOrder->latestTransaction->status->label() }}
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Items Purchased -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fa-solid fa-box mr-2"></i>
                                Item yang Dibeli
                            </h2>
                            <div class="space-y-3">
                                @foreach($latestOrder->items as $item)
                                <div class="flex items-center space-x-4 p-3 bg-white rounded-lg border">
                                    <div class="flex-shrink-0">
                                        @if($item->product && $item->product->images->first())
                                        <img src="{{ $item->product->images->first()->path_url }}" 
                                             alt="{{ $item->product_name }}"
                                             class="w-12 h-12 object-cover rounded-lg">
                                        @else
                                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-image text-gray-400"></i>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-medium text-gray-900 text-sm">{{ $item->product_name }}</h3>
                                        <p class="text-xs text-gray-500">
                                            {{ $item->quantity }} × Rp {{ number_format($item->product_price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-900 text-sm">
                                            Rp {{ number_format($item->total, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Payment Summary -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fa-solid fa-calculator mr-2"></i>
                                Ringkasan Pembayaran
                            </h2>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal:</span>
                                    <span class="font-medium">Rp {{ number_format($latestOrder->subtotal, 0, ',', '.') }}</span>
                                </div>
                                @if($latestOrder->tax > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Pajak:</span>
                                    <span class="font-medium">Rp {{ number_format($latestOrder->tax, 0, ',', '.') }}</span>
                                </div>
                                @endif
                                <div class="border-t pt-3">
                                    <div class="flex justify-between text-lg font-bold">
                                        <span class="text-gray-900">Total:</span>
                                        <span class="text-green-600">Rp {{ number_format($latestOrder->total, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($latestOrder->latestTransaction)
                        <!-- Transaction Details -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fa-solid fa-credit-card mr-2"></i>
                                Detail Transaksi
                            </h2>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">ID Transaksi:</span>
                                    <span class="font-medium text-sm">{{ $latestOrder->latestTransaction->transaction_id }}</span>
                                </div>
                                @if($latestOrder->latestTransaction->payment_type)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Metode Pembayaran:</span>
                                    <span class="font-medium">{{ $latestOrder->latestTransaction->payment_type->value }}</span>
                                </div>
                                @endif
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Waktu Transaksi:</span>
                                    <span class="font-medium">{{ $latestOrder->latestTransaction->transaction_time?->format('d M Y, H:i') }}</span>
                                </div>
                                @if($latestOrder->latestTransaction->settlement_time)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Waktu Settlement:</span>
                                    <span class="font-medium">{{ $latestOrder->latestTransaction->settlement_time->format('d M Y, H:i') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Next Steps -->
                        <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                            <h2 class="text-lg font-semibold text-blue-900 mb-4">
                                <i class="fa-solid fa-info-circle mr-2"></i>
                                Langkah Selanjutnya
                            </h2>
                            <div class="space-y-3 text-sm text-blue-800">
                                @if($latestOrder->latestTransaction && $latestOrder->latestTransaction->status->isSuccess())
                                <div class="flex items-start space-x-2">
                                    <i class="fa-solid fa-check text-green-500 mt-0.5"></i>
                                    <p>Pembayaran Anda telah berhasil diverifikasi</p>
                                </div>
                                <div class="flex items-start space-x-2">
                                    <i class="fa-solid fa-truck text-blue-500 mt-0.5"></i>
                                    <p>Pesanan Anda akan segera diproses dan dikirim</p>
                                </div>
                                <div class="flex items-start space-x-2">
                                    <i class="fa-solid fa-envelope text-blue-500 mt-0.5"></i>
                                    <p>Anda akan menerima email konfirmasi dan nomor tracking</p>
                                </div>
                                @elseif($latestOrder->latestTransaction && $latestOrder->latestTransaction->status->isPending())
                                <div class="flex items-start space-x-2">
                                    <i class="fa-solid fa-credit-card text-yellow-500 mt-0.5"></i>
                                    <p>Pembayaran belum diselesaikan</p>
                                </div>
                                <div class="flex items-start space-x-2">
                                    <i class="fa-solid fa-clock text-blue-500 mt-0.5"></i>
                                    <p>Silakan lanjutkan proses pembayaran</p>
                                </div>
                                @elseif($latestOrder->latestTransaction && $latestOrder->latestTransaction->status->isFailed())
                                <div class="flex items-start space-x-2">
                                    <i class="fa-solid fa-exclamation-triangle text-red-500 mt-0.5"></i>
                                    <p>Pembayaran gagal, silakan coba lagi</p>
                                </div>
                                <div class="flex items-start space-x-2">
                                    <i class="fa-solid fa-redo text-blue-500 mt-0.5"></i>
                                    <p>Anda dapat melakukan pembayaran ulang</p>
                                </div>
                                @elseif($latestOrder->latestTransaction && $latestOrder->latestTransaction->status->isExpired())
                                <div class="flex items-start space-x-2">
                                    <i class="fa-solid fa-clock-exclamation text-gray-500 mt-0.5"></i>
                                    <p>Waktu pembayaran telah habis</p>
                                </div>
                                <div class="flex items-start space-x-2">
                                    <i class="fa-solid fa-shopping-cart text-blue-500 mt-0.5"></i>
                                    <p>Silakan buat pesanan baru untuk melanjutkan</p>
                                </div>
                                @else
                                <div class="flex items-start space-x-2">
                                    <i class="fa-solid fa-credit-card text-blue-500 mt-0.5"></i>
                                    <p>Silakan selesaikan pembayaran untuk memproses pesanan</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <!-- No Order Found -->
            <div class="p-8 text-center">
                <i class="fa-solid fa-exclamation-triangle text-4xl text-yellow-500 mb-4"></i>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Tidak Ada Data Pesanan</h2>
                <p class="text-gray-600 mb-6">Kami tidak dapat menemukan data pesanan terakhir Anda</p>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="bg-gray-50 p-6 border-t">
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('home') }}" 
                       class="inline-flex items-center justify-center bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-200">
                        <i class="fa-solid fa-home mr-2"></i>
                        Kembali ke Beranda
                    </a>
{{--                     
                    @if($latestOrder)
                    <a href="{{ route('orders.show', $latestOrder->id) }}" 
                       class="inline-flex items-center justify-center bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-700 transition duration-200">
                        <i class="fa-solid fa-eye mr-2"></i>
                        Lihat Detail Order
                    </a>
                    @endif
                    
                    <a href="{{ route('shop.index') }}" 
                       class="inline-flex items-center justify-center bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition duration-200">
                        <i class="fa-solid fa-shopping-bag mr-2"></i>
                        Lanjut Belanja
                    </a> --}}
                </div>
            </div>
        </div>
    </div>
</x-layouts.plain-app>