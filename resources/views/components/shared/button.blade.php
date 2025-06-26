
@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'loading' => false,
    'loadingText' => 'Loading...',
    'icon' => null,
    'iconPosition' => 'left',
    'fullWidth' => false,
    'href' => null,
    'target' => null,
    'buttonClass' => ''
])

@php
    $baseClass = 'items-center justify-center border font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out';
    
    
    $sizeClasses = [
        'xs' => 'px-2.5 py-1.5 text-xs',
        'sm' => 'px-3 py-2 text-sm leading-4',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-4 py-2 text-base',
        'xl' => 'px-6 py-3 text-base',
    ];
    
    
    $variantClasses = [
        'primary' => 'border-transparent text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500',
        'secondary' => 'border-transparent text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:ring-indigo-500',
        'success' => 'border-transparent text-white bg-green-600 hover:bg-green-700 focus:ring-green-500',
        'danger' => 'border-transparent text-white bg-red-600 hover:bg-red-700 focus:ring-red-500',
        'warning' => 'border-transparent text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500',
        'info' => 'border-transparent text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
        'light' => 'border-gray-300 text-gray-700 bg-white hover:bg-gray-50 focus:ring-indigo-500',
        'outline-primary' => 'border-indigo-300 text-indigo-700 bg-transparent hover:bg-indigo-50 focus:ring-indigo-500',
        'outline-secondary' => 'border-gray-300 text-gray-700 bg-transparent hover:bg-gray-50 focus:ring-indigo-500',
        'outline-danger' => 'border-red-300 text-red-700 bg-transparent hover:bg-red-50 focus:ring-red-500',
        'ghost' => 'border-transparent text-gray-700 bg-transparent hover:bg-gray-100 focus:ring-indigo-500',
    ];
    
    $classes = $baseClass . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md']) . ' ' . ($variantClasses[$variant] ?? $variantClasses['primary']);
    
    if ($fullWidth) {
        $classes .= ' w-full';
    }
    
    if ($disabled || $loading) {
        $classes .= ' opacity-50 cursor-not-allowed';
    }
    
    $classes .= ' ' . $buttonClass;
    
    $tag = $href ? 'a' : 'button';
@endphp

@if($tag === 'a')
    <a 
        href="{{ $href }}"
        @if($target) target="{{ $target }}" @endif
        class="{{ $classes }} {{ $icon ? 'flex items-center gap-2' : '' }}"
        @if($disabled) 
            onclick="event.preventDefault(); return false;"
            tabindex="-1"
            aria-disabled="true"
        @endif
        {{ $attributes }}
    >
        @if($loading)
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" xmlns="http:
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ $loadingText }}
        @else
            @if($icon && $iconPosition === 'left')
                {!! $icon !!}
            @endif
            {{ $slot }}
            @if($icon && $iconPosition === 'right')
                {!! $icon !!}
            @endif
        @endif
    </a>
@else
    <div
        class="flex {{ $fullWidth ? 'w-full' : '' }} {{ $buttonClass }} {{ $icon ? 'items-center gap-2' : '' }}"
        @if($loading) x-data="{ loading: true }" @else x-data="{ loading: false }" @endif
    >
        <button 
        type="{{ $type }}"
        class="{{ $classes }}"
        @if($disabled || $loading) disabled @endif
        {{ $attributes }}
        >
            @if($loading)
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" xmlns="http:
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ $loadingText }}
            @else
                @if($icon && $iconPosition === 'left')
                    {!! $icon !!}
                @endif
                <span>
                    {{ $slot }}
                </span>
                @if($icon && $iconPosition === 'right')
                    {!! $icon !!}
                @endif
            @endif
        </button>
    </div>
@endif