@props([
    'product'
])

<tr class="hover:bg-gray-50" 
    x-show="shouldShowProduct({{ json_encode([
        'id' => $product->id,
        'name' => $product->name,
        'category' => $product->category->name,
        'is_active' => $product->is_active
    ]) }})"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 transform scale-95"
    x-transition:enter-end="opacity-100 transform scale-100">
    
    {{-- Checkbox --}}
    <td class="px-6 py-4 whitespace-nowrap">
        <input type="checkbox" 
               :value="{{ $product->id }}"
               x-model="selectedProducts"
               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
    </td>
    
    {{-- Produk Info --}}
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
            <div class="flex-shrink-0 h-12 w-12">
                @if($product->images->first())
                    <img class="h-12 w-12 rounded-lg object-cover cursor-pointer hover:opacity-75 transition duration-150" 
                         src="{{ $product->images->first()->path_url }}" 
                         alt="{{ $product->name }}"
                         @click="onClickShowImageModal('{{ $product->images->first()->path_url }}', '{{ $product->name }}')">
                @else
                    <div class="h-12 w-12 bg-gray-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-image text-gray-400"></i>
                    </div>
                @endif
            </div>
            <div class="ml-4">
                <div class="text-sm font-medium text-gray-900">
                    {{ $product->name }}
                </div>
                <div class="text-sm text-gray-500">
                    {{ Str::limit($product->description, 40) }}
                </div>
            </div>
        </div>
    </td>
    
    {{-- Kategori --}}
    <td class="px-6 py-4 whitespace-nowrap">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
            {{ $product->category->name }}
        </span>
    </td>
    
    {{-- Harga --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
        <div>
            @if($product->discount_price)
                <span class="text-red-600 font-medium">
                    Rp {{ number_format($product->discount_price, 0, ',', '.') }}
                </span>
                <span class="text-gray-500 line-through ml-2">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </span>
            @else
                <span class="font-medium">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </span>
            @endif
        </div>
    </td>
    
    {{-- Stok --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
        <span class="font-medium {{ $product->stock < 10 ? 'text-red-600' : 'text-gray-900' }}">
            {{ $product->stock }}
        </span>
        @if($product->stock < 10)
            <span class="text-xs text-red-500 block">Stok rendah</span>
        @endif
    </td>
    
    {{-- Status --}}
    <td class="px-6 py-4 whitespace-nowrap">
        @if($product->is_active)
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                <i class="fas fa-circle text-green-400 mr-1.5 text-xs"></i>
                Aktif
            </span>
        @else
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                <i class="fas fa-circle text-gray-400 mr-1.5 text-xs"></i>
                Nonaktif
            </span>
        @endif
    </td>
    
    {{-- Aksi --}}
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.products.show', $product->id) }}" 
               class="text-indigo-600 hover:text-indigo-900 transition duration-150 ease-in-out"
               title="Lihat Detail">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('admin.products.edit', $product->id) }}" 
               class="text-yellow-600 hover:text-yellow-900 transition duration-150 ease-in-out"
               title="Edit Produk">
                <i class="fas fa-edit"></i>
            </a>
            <button
                class="text-red-600 hover:text-red-900 transition duration-150 ease-in-out"
                @click="confirmDelete({{ $product->id }}, '{{ e($product->name) }}')"
                title="Hapus Produk">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </td>
</tr>