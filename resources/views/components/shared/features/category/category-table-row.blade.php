@props([
    'category'
])

<tr class="hover:bg-gray-50" 
    x-show="shouldShowCategory({{ json_encode([
        'id' => $category->id,
        'name' => $category->name,
        'slug' => $category->slug,
        'products_count' => $category->products->count()
    ]) }})"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 transform scale-95"
    x-transition:enter-end="opacity-100 transform scale-100">
    
    {{-- Checkbox --}}
    <td class="px-6 py-4 whitespace-nowrap">
        <input type="checkbox" 
               :value="{{ $category->id }}"
               x-model="selectedCategories"
               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
    </td>
    
    {{-- Kategori Info --}}
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
            <div class="flex-shrink-0 h-12 w-12">
                @if($category->image)
                    <img class="h-12 w-12 rounded-lg object-cover cursor-pointer hover:opacity-75 transition duration-150" 
                         src="{{ $category->image_url }}" 
                         alt="{{ $category->name }}"
                         @click="onClickShowImageModal('{{ $category->image_url }}', '{{ $category->name }}')">
                @else
                    <div class="h-12 w-12 bg-gray-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-folder text-gray-400"></i>
                    </div>
                @endif
            </div>
            <div class="ml-4">
                <div class="text-sm font-medium text-gray-900">
                    {{ $category->name }}
                </div>
            </div>
        </div>
    </td>
    
    {{-- Slug --}}
    <td class="px-6 py-4 whitespace-nowrap">
        <span class="text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded font-mono">
            {{ $category->slug }}
        </span>
    </td>
    
    {{-- Jumlah Produk --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
        <div class="flex items-center">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                {{ $category->products->count() > 0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                <i class="fas fa-box mr-1"></i>
                {{ $category->products->count() }} produk
            </span>
        </div>
    </td>
    
    {{-- Tanggal Dibuat --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        <div>
            <div>{{ $category->created_at->format('d M Y') }}</div>
            <div class="text-xs text-gray-400">{{ $category->created_at->format('H:i') }}</div>
        </div>
    </td>
    
    {{-- Aksi --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
        <div class="flex items-center space-x-2">
            <x-shared.button
                href="{{ route('admin.categories.show', $category->id) }}"
                variant="primary"
                size="sm"
                icon='<i class="fas fa-eye"></i>'
            >
            </x-shared.button>
            <x-shared.button
                href="{{ route('admin.categories.edit', $category->id) }}"
                variant="warning"
                size="sm"
                icon='<i class="fas fa-edit"></i>'
            >
            </x-shared.button>
            <x-shared.button
                @click="confirmDelete({{ $category->id }}, '{{ e($category->name) }}')"
                variant="danger"
                size="sm"
                icon='<i class="fas fa-trash"></i>'
            >
            </x-shared.button>
        </div>
    </td>
</tr>