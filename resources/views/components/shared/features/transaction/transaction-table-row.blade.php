{{-- resources/views/components/shared/features/transaction/transaction-table-row.blade.php --}}
@props(['transaction'])

<tr class="hover:bg-gray-50">
    <td class="px-6 py-4 whitespace-nowrap">
        <input type="checkbox" 
               x-model="selectedTransactions" 
               :value="{{ $transaction->id }}"
               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="text-sm font-medium text-gray-900">
            {{ $transaction->transaction_id }}
        </div>
        @if($transaction->order_id_midtrans)
            <div class="text-sm text-gray-500">
                Midtrans: {{ $transaction->order_id_midtrans }}
            </div>
        @endif
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="text-sm text-gray-900">
            <a href="{{ route('admin.orders.show', $transaction->order->id) }}" 
               class="text-indigo-600 hover:text-indigo-900 font-medium">
                {{ $transaction->order->order_number }}
            </a>
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
            <div class="flex-shrink-0 h-8 w-8">
                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                    <span class="text-sm font-medium text-gray-700">
                        {{ strtoupper(substr($transaction->order->user->name, 0, 1)) }}
                    </span>
                </div>
            </div>
            <div class="ml-3">
                <div class="text-sm font-medium text-gray-900">
                    {{ $transaction->order->user->name }}
                </div>
                <div class="text-sm text-gray-500">
                    {{ $transaction->order->user->email }}
                </div>
            </div>
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="text-sm text-gray-900">
            {{ $transaction->created_at->format('d M Y') }}
        </div>
        <div class="text-sm text-gray-500">
            {{ $transaction->created_at->format('H:i') }}
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <span :class="getStatusBadgeClass('{{ $transaction->status }}')" 
              class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
            {{ $transaction->status->label() }}
        </span>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <span :class="getPaymentTypeBadgeClass('{{ $transaction->payment_type }}')" 
              class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
            {{  $transaction->payment_type->label()}}
        </span>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="text-sm font-medium text-gray-900">
            <span x-text="formatCurrency({{ $transaction->gross_amount }})"></span>
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
        <div class="flex items-center space-x-2">
            <x-shared.button
                href="{{ route('admin.transactions.show', $transaction->id) }}"
                variant="light"
                size="sm"
                icon='<i class="fas fa-eye"></i>'
            >
            </x-shared.button>
            
            <x-shared.button
                @click="confirmStatusUpdate({{ $transaction->id }}, '{{ $transaction->transaction_id }}', '{{ $transaction->status }}')"
                variant="primary"
                size="sm"
                icon='<i class="fas fa-edit"></i>'
            >
            </x-shared.button>
        </div>        
    </td>
</tr>