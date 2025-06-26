
@props([
    'name',
    'id' => $name,
    'label' => null,
    'value' => '1',
    'checked' => false,
    'disabled' => false,
    'required' => false,
    'helpText' => null,
    'containerClass' => '',
    'wrapperClass' => 'flex items-center',
    'checkboxClass' => 'h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded',
    'labelClass' => 'ml-2 block text-sm text-gray-900',
    'errorClass' => 'mt-2 text-sm text-red-600',
    'helpClass' => 'mt-1 text-xs text-gray-500 ml-6'
])

@php
    $isChecked = old($name, $checked);
    $finalCheckboxClass = $checkboxClass;
    
    if ($disabled) {
        $finalCheckboxClass .= ' cursor-not-allowed opacity-50';
    }
@endphp

<div class="{{ $containerClass }}">
    <div class="{{ $wrapperClass }}">
        <input 
            type="checkbox"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ $value }}"
            class="{{ $finalCheckboxClass }}"
            @if($isChecked) checked @endif
            @if($disabled) disabled @endif
            @if($required) required @endif
            {{ $attributes }}
        />
        @if($label)
            <label for="{{ $id }}" class="{{ $labelClass }}">
                {{ $label }}
                @if($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>
        @endif
    </div>
    
    @error($name)
        <p class="{{ $errorClass }}">{{ $message }}</p>
    @enderror
    
    @if($helpText)
        <p class="{{ $helpClass }}">{{ $helpText }}</p>
    @endif
</div>