@props([
    'totalCount' => 0
])

<div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Daftar Pesanan
            </h3>
            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {{ number_format($totalCount) }} total
            </span>
        </div>
    </div>
</div>