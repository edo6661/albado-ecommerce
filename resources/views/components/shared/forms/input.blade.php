@props([
    'type' => 'text',
    'name',
    'id' => $name,
    'label' => null,
    'placeholder' => '',
    'value' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'min' => null,
    'max' => null,
    'step' => null,
    'accept' => null,
    'multiple' => false,
    'prefix' => null,
    'suffix' => null,
    'helpText' => null,
    'containerClass' => '',
    'labelClass' => 'block text-sm font-medium text-gray-700',
    'inputClass' => '',
    'errorClass' => 'mt-2 text-sm text-red-600',
    'helpClass' => 'mt-1 text-xs text-gray-500'
])

@php
    $hasError = $errors->has($name);
    $baseInputClass = 'appearance-none block w-full px-3 py-2 border rounded-md placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm';
    
    if ($hasError) {
        $baseInputClass .= ' border-red-300';
    } else {
        $baseInputClass .= ' border-gray-300';
    }
    
    if ($disabled) {
        $baseInputClass .= ' bg-gray-50 cursor-not-allowed';
    }
    
    $finalInputClass = $baseInputClass . ' ' . $inputClass;
    
    if ($prefix) {
        $finalInputClass = str_replace('px-3', 'pl-10 pr-3', $finalInputClass);
    }
    if ($suffix) {
        $finalInputClass = str_replace('px-3', 'pl-3 pr-10', $finalInputClass);
    }
    if ($prefix && $suffix) {
        $finalInputClass = str_replace('px-3', 'pl-10 pr-10', $finalInputClass);
    }
@endphp

<div class="{{ $containerClass }}">
    @if($label)
        <label for="{{ $id }}" class="{{ $labelClass }}">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="mt-1 relative">
        @if($prefix)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">{{ $prefix }}</span>
            </div>
        @endif
        
        <input 
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $id }}"
            class="{{ $finalInputClass }}"
            placeholder="{{ $placeholder }}"
            value="{{ old($name, $value) }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            @if($min) min="{{ $min }}" @endif
            @if($max) max="{{ $max }}" @endif
            @if($step) step="{{ $step }}" @endif
            @if($accept) accept="{{ $accept }}" @endif
            @if($multiple) multiple @endif
            {{ $attributes }}
        />
        
        @if($suffix)
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">{{ $suffix }}</span>
            </div>
        @endif
    </div>
    
    @error($name)
        <p class="{{ $errorClass }}">{{ $message }}</p>
    @enderror
    
    @if($helpText)
        <p class="{{ $helpClass }}">{{ $helpText }}</p>
    @endif
</div>