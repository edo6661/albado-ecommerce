<x-layouts.plain-app>
    <x-slot:title>Edit Transaksi</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.transactions.index') }}" 
                       class="text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-extrabold text-gray-900">Edit Transaksi</h1>
                        <p class="mt-2 text-sm text-gray-600">
                            Edit transaksi {{ $transaction->transaction_id }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <form method="POST" action="{{ route('admin.transactions.update', $transaction->id) }}" 
                      x-data="transactionForm()" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div class="px-6 py-6 space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                            
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <x-shared.forms.input
                                    name="transaction_id"
                                    label="ID Transaksi"
                                    placeholder="Masukkan ID transaksi"
                                    :value="$transaction->transaction_id"
                                    required
                                    container-class="sm:col-span-2"
                                    help-text="ID unik untuk transaksi ini"
                                    readonly
                                />

                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Pesanan
                                    </label>
                                    <div class="bg-gray-50 border border-gray-200 rounded-md px-3 py-2">
                                        <span class="text-sm text-gray-900">
                                            {{ $transaction->order->order_number }} - {{ $transaction->order->user->name }}
                                        </span>
                                    </div>
                                </div>

                                <x-shared.forms.select
                                    name="status"
                                    label="Status"
                                    :options="$statusOptions"
                                    :value="$transaction->status"
                                    required
                                />

                                <x-shared.forms.select
                                    name="payment_type"
                                    label="Tipe Pembayaran"
                                    :options="$paymentOptions"
                                    :value="$transaction->payment_type"
                                    required
                                />

                                <x-shared.forms.input
                                    name="order_id_midtrans"
                                    label="Order ID Midtrans"
                                    placeholder="Masukkan order ID Midtrans"
                                    :value="$transaction->order_id_midtrans"
                                    optional
                                />
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pembayaran</h3>
                            
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <x-shared.forms.input
                                    name="gross_amount"
                                    type="number"
                                    label="Jumlah Total"
                                    placeholder="0.00"
                                    min="0"
                                    step="0.01"
                                    prefix="Rp"
                                    :value="$transaction->gross_amount"
                                    required
                                />

                                <x-shared.forms.input
                                    name="currency"
                                    label="Mata Uang"
                                    placeholder="IDR"
                                    :value="$transaction->currency ?? 'IDR'"
                                    required
                                />

                                <x-shared.forms.input
                                    name="bank"
                                    label="Bank"
                                    placeholder="Masukkan nama bank"
                                    :value="$transaction->bank"
                                    optional
                                />

                                <x-shared.forms.input
                                    name="va_number"
                                    label="Nomor Virtual Account"
                                    placeholder="Masukkan nomor VA"
                                    :value="$transaction->va_number"
                                    optional
                                />

                                <x-shared.forms.input
                                    name="fraud_status"
                                    label="Status Fraud"
                                    placeholder="Masukkan status fraud"
                                    :value="$transaction->fraud_status"
                                    optional
                                />

                                <x-shared.forms.input
                                    name="status_code"
                                    label="Kode Status"
                                    placeholder="Masukkan kode status"
                                    :value="$transaction->status_code"
                                    optional
                                />
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Tambahan</h3>
                            
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <x-shared.forms.input
                                    name="transaction_time"
                                    type="datetime-local"
                                    label="Waktu Transaksi"
                                    :value="$transaction->transaction_time ? $transaction->transaction_time->format('Y-m-d\TH:i') : ''"
                                    optional
                                />

                                <x-shared.forms.input
                                    name="settlement_time"
                                    type="datetime-local"
                                    label="Waktu Settlement"
                                    :value="$transaction->settlement_time ? $transaction->settlement_time->format('Y-m-d\TH:i') : ''"
                                    optional
                                />

                                <x-shared.forms.textarea
                                    name="status_message"
                                    label="Pesan Status"
                                    placeholder="Masukkan pesan status"
                                    :rows="3"
                                    :value="$transaction->status_message"
                                    optional
                                />

                                <x-shared.forms.textarea
                                    name="midtrans_response"
                                    label="Response Midtrans (JSON)"
                                    placeholder="Masukkan response dari Midtrans dalam format JSON"
                                    :rows="3"
                                    :value="$transaction->midtrans_response"
                                    optional
                                />
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Audit</h3>
                            
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Dibuat Pada
                                    </label>
                                    <div class="bg-gray-50 border border-gray-200 rounded-md px-3 py-2">
                                        <span class="text-sm text-gray-900">
                                            {{ $transaction->created_at->format('d M Y H:i:s') }}
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Terakhir Diupdate
                                    </label>
                                    <div class="bg-gray-50 border border-gray-200 rounded-md px-3 py-2">
                                        <span class="text-sm text-gray-900">
                                            {{ $transaction->updated_at->format('d M Y H:i:s') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4">
                        <div class="flex justify-between items-center">
                            <div class="flex space-x-3">
                                <x-shared.button
                                    variant="light"
                                    href="{{ route('admin.transactions.index') }}"
                                    icon='<svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
                                >
                                    Batal
                                </x-shared.button>

                                <x-shared.button
                                    variant="secondary"
                                    href="{{ route('admin.transactions.show', $transaction->id) }}"
                                    icon='<svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>'
                                >
                                    Lihat Detail
                                </x-shared.button>
                            </div>

                            <x-shared.button
                                type="submit"
                                variant="primary"
                                icon='<svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
                            >
                                Update Transaksi
                            </x-shared.button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function transactionForm() {
            return {
                // Validasi JSON untuk Midtrans response
                validateJson(value) {
                    if (!value.trim()) return true;
                    try {
                        JSON.parse(value);
                        return true;
                    } catch (e) {
                        return false;
                    }
                },
                
                // Format currency display
                formatCurrency(amount) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(amount);
                }
            }
        }
    </script>
</x-layouts.plain-app>