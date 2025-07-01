@props([
    'totalCount' => 0
])

<div class="px-6 py-4 border-b border-gray-200 bg-white">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <h3 class="text-lg font-medium text-gray-900">Daftar Kategori</h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                {{ $totalCount }} kategori
            </span>
        </div>
    </div>
</div>