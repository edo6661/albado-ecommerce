@props([
    'show' => 'false',
    'size' => 'md', 
    'type' => 'default', 
    'title' => null,
    'icon' => null,
    'iconType' => 'outline', 
    'closable' => true,
    'backdrop' => true, 
    'maxWidth' => null,
    'maxHeight' => null,
    'position' => 'center', 
    'animation' => true,
    'zIndex' => 'z-50',
    'onClose' => null,
    'containerClass' => '',
    'overlayClass' => '',
    'modalClass' => '',
    'headerClass' => '',
    'bodyClass' => '',
    'footerClass' => ''
])

@php
    
    $sizeClasses = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg', 
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        'full' => 'max-w-full'
    ];
    
    
    $positionClasses = [
        'center' => 'items-center justify-center',
        'top' => 'items-start justify-center pt-16',
        'bottom' => 'items-end justify-center pb-16'
    ];
    
    
    $typeConfig = [
        'default' => [
            'iconBg' => 'bg-gray-100',
            'iconColor' => 'text-gray-600',
            'defaultIcon' => 'fa-info-circle'
        ],
        'danger' => [
            'iconBg' => 'bg-red-100', 
            'iconColor' => 'text-red-600',
            'defaultIcon' => 'fa-exclamation-triangle'
        ],
        'success' => [
            'iconBg' => 'bg-green-100',
            'iconColor' => 'text-green-600', 
            'defaultIcon' => 'fa-check-circle'
        ],
        'warning' => [
            'iconBg' => 'bg-yellow-100',
            'iconColor' => 'text-yellow-600',
            'defaultIcon' => 'fa-exclamation-triangle'
        ],
        'info' => [
            'iconBg' => 'bg-blue-100',
            'iconColor' => 'text-blue-600',
            'defaultIcon' => 'fa-info-circle'
        ]
    ];
    
    $currentTypeConfig = $typeConfig[$type] ?? $typeConfig['default'];
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    $positionClass = $positionClasses[$position] ?? $positionClasses['center'];
    
    
    $overlayClasses = "fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full {$zIndex} flex {$positionClass} " . $overlayClass;
    
    $modalClasses = "relative bg-white rounded-lg shadow-xl {$sizeClass} w-full mx-4 " . $modalClass;
    
    if ($maxWidth) {
        $modalClasses .= " max-w-[{$maxWidth}]";
    }
    if ($maxHeight) {
        $modalClasses .= " max-h-[{$maxHeight}] overflow-y-auto";
    }
    
    
    $enterAnimation = $animation ? 'transition ease-out duration-300' : '';
    $enterStartAnimation = $animation ? 'opacity-0 transform scale-95' : '';
    $enterEndAnimation = $animation ? 'opacity-100 transform scale-100' : '';
    $leaveAnimation = $animation ? 'transition ease-in duration-200' : '';
    $leaveStartAnimation = $animation ? 'opacity-100 transform scale-100' : '';
    $leaveEndAnimation = $animation ? 'opacity-0 transform scale-95' : '';
    
    
    $closeHandler = $onClose ?? 'false';
    $backdropHandler = $backdrop ? $closeHandler : '';
@endphp

<div x-show="{{ $show }}"
     @if($animation)
     x-transition:enter="{{ $enterAnimation }}"
     x-transition:enter-start="{{ $enterStartAnimation }}"
     x-transition:enter-end="{{ $enterEndAnimation }}"
     x-transition:leave="{{ $leaveAnimation }}"
     x-transition:leave-start="{{ $leaveStartAnimation }}"
     x-transition:leave-end="{{ $leaveEndAnimation }}"
     @endif
     @if($backdropHandler)
     @click.self="{{ $backdropHandler }}"
     @endif
     class="{{ $overlayClasses }}"
     {{ $attributes->whereStartsWith('x-') }}
     style="display: none;">
    
    <div class="{{ $modalClasses }} {{ $containerClass }}">
        
        @if($title || $icon || $closable)
        <div class="flex items-center justify-between p-6 border-b border-gray-200 {{ $headerClass }}">
            <div class="flex items-center">
                @if($icon || ($type !== 'default'))
                <div class="flex-shrink-0 mx-auto flex items-center justify-center h-12 w-12 rounded-full {{ $currentTypeConfig['iconBg'] }} mr-4">
                    @if($icon)
                        <i class="fas {{ $icon }} h-6 w-6 {{ $currentTypeConfig['iconColor'] }}"></i>
                    @else
                        <i class="fas {{ $currentTypeConfig['defaultIcon'] }} h-6 w-6 {{ $currentTypeConfig['iconColor'] }}"></i>
                    @endif
                </div>
                @endif
                
                @if($title)
                <h3 class="text-lg font-medium text-gray-900">
                    {{ $title }}
                </h3>
                @endif
            </div>
            
            @if($closable)
            <button @click="{{ $closeHandler }}" 
                    class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                <i class="fas fa-times h-5 w-5"></i>
            </button>
            @endif
        </div>
        @endif
        
        <div class="p-6 {{ $bodyClass }}">
            {{ $slot }}
        </div>
        
        @if(isset($footer))
        <div class="flex items-center justify-end px-6 py-3 bg-gray-50 border-t border-gray-200 rounded-b-lg space-x-3 {{ $footerClass }}">
            {{ $footer }}
        </div>
        @endif
    </div>
</div>