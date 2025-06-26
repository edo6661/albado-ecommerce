@props(['order'])

<tr class="hover:bg-gray-50">
    <td class="px-6 py-4 whitespace-nowrap">
        <input type="checkbox" 
               x-model="selectedOrders"
               :value="{{ $order->id }}"
               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="text-sm font-medium text-gray-900">
            {{ $order->order_number }}
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="text-sm text-gray-900">{{ $order->user->name }}</div>
        <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
        {{ $order->created_at->format('d/m/Y H:i') }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" 
              :class="getStatusBadgeClass('{{ $order->status->value }}')">
            {{ $order->status->label() }}
        </span>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
        <span x-text="formatCurrency({{ $order->total }})"></span>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
        <div class="flex items-center space-x-2">
            <x-shared.button
                href="{{ route('admin.orders.show', $order->id) }}"
                variant="primary"
                size="sm"
                icon='<i class="fas fa-eye"></i>'
            >
            </x-shared.button>
            
            <x-shared.button
                @click="confirmStatusUpdate({{ $order->id }}, '{{ $order->order_number }}', '{{ $order->status->value }}')"
                variant="warning"
                size="sm"
                icon='<i class="fas fa-edit"></i>'
            >
            </x-shared.button>
          
        </div>
    </td>
</tr>