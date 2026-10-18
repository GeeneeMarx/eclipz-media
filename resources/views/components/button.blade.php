@props(['href' => null, 'variant' => 'outline-dark', 'type' => 'button'])
@php
    $variantClasses = match ($variant) {
        'outline-light' => 'border border-white text-white hover:bg-white hover:text-jet',
        'solid' => 'bg-white text-jet hover:bg-warm-gold',
        default => 'border border-jet text-jet hover:bg-jet hover:text-white',
    };
@endphp
@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "inline-flex items-center gap-3 px-7 py-3.5 font-body transition-colors duration-300 $variantClasses"]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => "inline-flex items-center gap-3 px-7 py-3.5 font-body transition-colors duration-300 $variantClasses"]) }}>
        {{ $slot }}
    </button>
@endif