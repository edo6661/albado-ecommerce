@props([
    'for' => null,
    'required' => false,
    'optional' => false,
    'labelClass' => 'block text-sm font-medium text-gray-700'
])

<label 
    @if($for) for="{{ $for }}" @endif
    class="{{ $labelClass }}"
    {{ $attributes }}
>
    {{ $slot }}
    @if($required)
        <span class="text-red-500">*</span>
    @endif
    @if($optional)
        <span class="text-gray-500 font-normal">(Opsional)</span>
    @endif
</label>