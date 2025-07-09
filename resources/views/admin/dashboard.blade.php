<x-layouts.plain-app>
    <div class="bg-gray-50 min-h-screen">
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Dashboard Admin</h1>
                        <p class="text-gray-600 mt-1">Selamat datang kembali! Berikut ringkasan aktivitas terkini.</p>
                    </div>
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        {{ now()->translatedFormat('l, d F Y') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Pengguna</p>
                            <p class="text-3xl font-bold text-gray-900">{{ number_format($userStats['total_users']) }}</p>
                            <p class="text-sm text-green-600 mt-1">
                                <i class="fas fa-arrow-up mr-1"></i>
                                +{{ $userStats['new_users_this_month'] }} bulan ini
                            </p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i class="fas fa-users text-2xl text-blue-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Produk</p>
                            <p class="text-3xl font-bold text-gray-900">{{ number_format($productStats['total_products']) }}</p>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ $productStats['active_products'] }} aktif, {{ $productStats['inactive_products'] }} nonaktif
                            </p>
                        </div>
                        <div class="bg-purple-100 p-3 rounded-full">
                            <i class="fas fa-box text-2xl text-purple-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Pesanan</p>
                            <p class="text-3xl font-bold text-gray-900">{{ number_format($orderStats['total_orders']) }}</p>
                            <p class="text-sm text-green-600 mt-1">
                                <i class="fas fa-arrow-up mr-1"></i>
                                +{{ $orderStats['total_orders'] }} hari ini
                            </p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-shopping-cart text-2xl text-green-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Pendapatan</p>
                            <p class="text-3xl font-bold text-gray-900">Rp {{ number_format($transactionStats['total_revenue'], 0, ',', '.') }}</p>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ $transactionStats['total_transactions'] }} transaksi
                            </p>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <i class="fas fa-money-bill-wave text-2xl text-yellow-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Status Pesanan</h3>
                        <i class="fas fa-chart-pie text-gray-400"></i>
                    </div>
                    <div class="space-y-4">
                        @foreach($orderStats['status_counts'] as $status => $count)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full mr-3 
                                    @if($status === 'pending') bg-yellow-400
                                    @elseif($status === 'processing') bg-blue-400
                                    @elseif($status === 'shipped') bg-purple-400
                                    @elseif($status === 'delivered') bg-green-400
                                    @elseif($status === 'cancelled') bg-red-400
                                    @else bg-gray-400
                                    @endif">
                                </div>
                                <span class="text-sm font-medium text-gray-700 capitalize">{{ ucfirst($status) }}</span>
                            </div>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($count) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Status Transaksi</h3>
                        <i class="fas fa-credit-card text-gray-400"></i>
                    </div>
                    <div class="space-y-4">
                        @foreach($transactionStats['status_counts'] as $status => $count)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full mr-3 
                                    @if($status === 'pending') bg-yellow-400
                                    @elseif($status === 'settlement') bg-green-400
                                    @elseif($status === 'failed') bg-red-400
                                    @elseif($status === 'expired') bg-gray-400
                                    @else bg-blue-400
                                    @endif">
                                </div>
                                <span class="text-sm font-medium text-gray-700 capitalize">{{ ucfirst($status) }}</span>
                            </div>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($count) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Pesanan Terbaru</h3>
                            <a href="{{ route('admin.orders.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pesanan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($recentOrders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                                        <div class="text-sm text-gray-500">{{ $order->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $order->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        Rp {{ number_format($order->total, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            @if($order->status->value === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($order->status->value === 'processing') bg-blue-100 text-blue-800
                                            @elseif($order->status->value === 'shipped') bg-purple-100 text-purple-800
                                            @elseif($order->status->value === 'delivered') bg-green-100 text-green-800
                                            @elseif($order->status->value === 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($order->status->value) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada pesanan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Pengguna Terbaru</h3>
                            <a href="{{ route('admin.dashboard') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bergabung</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($recentUsers as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <i class="fas fa-user text-gray-600"></i>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                @if($user->isAdmin())
                                                <div class="text-xs text-red-600 font-medium">Admin</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->created_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->email_verified_at)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Terverifikasi
                                        </span>
                                        @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Belum Terverifikasi
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada pengguna</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($productStats['low_stock_products'] > 0 || $productStats['out_of_stock_products'] > 0)
            <div class="mt-8">
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-6">
                    <div class="flex items-center">
                        <div class="bg-orange-100 p-2 rounded-full">
                            <i class="fas fa-exclamation-triangle text-orange-600"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-orange-900">Peringatan Stok</h3>
                            <p class="text-orange-700 mt-1">
                                @if($productStats['out_of_stock_products'] > 0)
                                    {{ $productStats['out_of_stock_products'] }} produk kehabisan stok.
                                @endif
                                @if($productStats['low_stock_products'] > 0)
                                    {{ $productStats['low_stock_products'] }} produk stok menipis (≤10).
                                @endif
                            </p>
                            <a href="#" class="text-orange-600 hover:text-orange-700 font-medium text-sm mt-2 inline-block">
                                Kelola Stok <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-layouts.plain-app>