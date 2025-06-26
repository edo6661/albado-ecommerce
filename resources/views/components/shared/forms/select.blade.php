
@props([
    'name',
    'id' => $name,
    'label' => null,
    'options' => [],
    'value' => '',
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'multiple' => false,
    'helpText' => null,
    'containerClass' => '',
    'labelClass' => 'block text-sm font-medium text-gray-700',
    'selectClass' => '',
    'errorClass' => 'mt-2 text-sm text-red-600',
    'helpClass' => 'mt-1 text-xs text-gray-500'
])

@php
    $hasError = $errors->has($name);
    $baseSelectClass = 'block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm';
    
    if ($hasError) {
        $baseSelectClass .= ' border-red-300';
    } else {
        $baseSelectClass .= ' border-gray-300';
    }
    
    if ($disabled) {
        $baseSelectClass .= ' bg-gray-50 cursor-not-allowed';
    }
    
    $finalSelectClass = $baseSelectClass . ' ' . $selectClass;
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
        <select 
            name="{{ $name }}{{ $multiple ? '[]' : '' }}"
            id="{{ $id }}"
            class="{{ $finalSelectClass }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($multiple) multiple @endif
            {{ $attributes }}
        >
            @if($placeholder && !$multiple)
                <option value="">{{ $placeholder }}</option>
            @endif
            
            @foreach($options as $optionValue => $optionLabel)
                @if(is_array($optionLabel))
                    <optgroup label="{{ $optionValue }}">
                        @foreach($optionLabel as $subValue => $subLabel)
                            <option value="{{ $subValue }}" 
                                @if($multiple)
                                    @if(is_array(old($name, $value)) && in_array($subValue, old($name, $value)))
                                        selected
                                    @endif
                                @else
                                    @if(old($name, $value) == $subValue)
                                        selected
                                    @endif
                                @endif
                            >
                                {{ $subLabel }}
                            </option>
                        @endforeach
                    </optgroup>
                @else
                    <option value="{{ $optionValue }}" 
                        @if($multiple)
                            @if(is_array(old($name, $value)) && in_array($optionValue, old($name, $value)))
                                selected
                            @endif
                        @else
                            @if(old($name, $value) == $optionValue)
                                selected
                            @endif
                        @endif
                    >
                        {{ $optionLabel }}
                    </option>
                @endif
            @endforeach
        </select>
    </div>
    
    @error($name)
        <p class="{{ $errorClass }}">{{ $message }}</p>
    @enderror
    
    @if($helpText)
        <p class="{{ $helpClass }}">{{ $helpText }}</p>
    @endif
</div>