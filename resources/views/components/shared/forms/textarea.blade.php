
@props([
    'name',
    'id' => $name,
    'label' => null,
    'placeholder' => '',
    'value' => '',
    'rows' => 4,
    'cols' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'maxlength' => null,
    'helpText' => null,
    'containerClass' => '',
    'labelClass' => 'block text-sm font-medium text-gray-700',
    'textareaClass' => '',
    'errorClass' => 'mt-2 text-sm text-red-600',
    'helpClass' => 'mt-1 text-xs text-gray-500',
    'showCounter' => false
])

@php
    $hasError = $errors->has($name);
    $baseTextareaClass = 'block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm';
    
    if ($hasError) {
        $baseTextareaClass .= ' border-red-300';
    } else {
        $baseTextareaClass .= ' border-gray-300';
    }
    
    if ($disabled) {
        $baseTextareaClass .= ' bg-gray-50 cursor-not-allowed';
    }
    
    $finalTextareaClass = $baseTextareaClass . ' ' . $textareaClass;
    $currentValue = old($name, $value);
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
    
    <div class="mt-1">
        <textarea 
            name="{{ $name }}"
            id="{{ $id }}"
            rows="{{ $rows }}"
            @if($cols) cols="{{ $cols }}" @endif
            class="{{ $finalTextareaClass }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            @if($maxlength) maxlength="{{ $maxlength }}" @endif
            @if($showCounter && $maxlength) 
                x-data="{ count: {{ strlen($currentValue) }} }"
                x-init="$watch('count', value => { if(value > {{ $maxlength }}) count = {{ $maxlength }} })"
                @input="count = $event.target.value.length"
            @endif
            {{ $attributes }}
        >{{ $currentValue }}</textarea>
    </div>
    
    @if($showCounter && $maxlength)
        <div class="mt-1 text-right">
            <span class="text-xs text-gray-500" x-text="count + '/' + {{ $maxlength }}"></span>
        </div>
    @endif
    
    @error($name)
        <p class="{{ $errorClass }}">{{ $message }}</p>
    @enderror
    
    @if($helpText)
        <p class="{{ $helpClass }}">{{ $helpText }}</p>
    @endif
</div>