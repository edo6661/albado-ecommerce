
@props([
    'name',
    'options' => [],
    'value' => '',
    'label' => null,
    'disabled' => false,
    'required' => false,
    'inline' => false,
    'helpText' => null,
    'containerClass' => '',
    'labelClass' => 'block text-sm font-medium text-gray-700 mb-2',
    'radioWrapperClass' => '',
    'radioClass' => 'h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300',
    'radioLabelClass' => 'ml-2 block text-sm text-gray-900',
    'errorClass' => 'mt-2 text-sm text-red-600',
    'helpClass' => 'mt-1 text-xs text-gray-500'
])

@php
    $selectedValue = old($name, $value);
    $baseWrapperClass = $inline ? 'flex items-center space-x-6' : 'space-y-2';
    $itemWrapperClass = 'flex items-center';
    
    $finalRadioClass = $radioClass;
    if ($disabled) {
        $finalRadioClass .= ' cursor-not-allowed opacity-50';
    }
@endphp

<div class="{{ $containerClass }}">
    @if($label)
        <label class="{{ $labelClass }}">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="{{ $baseWrapperClass }} {{ $radioWrapperClass }}">
        @foreach($options as $optionValue => $optionLabel)
            <div class="{{ $itemWrapperClass }}">
                <input 
                    type="radio"
                    name="{{ $name }}"
                    id="{{ $name }}_{{ $optionValue }}"
                    value="{{ $optionValue }}"
                    class="{{ $finalRadioClass }}"
                    @if($selectedValue == $optionValue) checked @endif
                    @if($disabled) disabled @endif
                    @if($required) required @endif
                />
                <label for="{{ $name }}_{{ $optionValue }}" class="{{ $radioLabelClass }}">
                    {{ $optionLabel }}
                </label>
            </div>
        @endforeach
    </div>
    
    @error($name)
        <p class="{{ $errorClass }}">{{ $message }}</p>
    @enderror
    
    @if($helpText)
        <p class="{{ $helpClass }}">{{ $helpText }}</p>
    @endif
</div>