{{-- resources/views/components/shared/features/transaction/transaction-table-header.blade.php --}}
@props([
    'totalCount' => 0
])

<div class="px-6 py-4 border-b border-gray-200 bg-white">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <h3 class="text-lg font-medium text-gray-900">
                Daftar Transaksi
            </h3>
            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                {{ $totalCount }} transaksi
            </span>
        </div>
    </div>
</div>