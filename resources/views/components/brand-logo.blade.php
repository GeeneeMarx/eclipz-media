@props(['variant' => 'black'])
@php
    // Picks public/images/eclipz-logo-white.(svg|png) or eclipz-logo-black.(svg|png).
    // The ?v= value is the file's last-modified time, so browsers fetch a fresh copy
    // whenever the artwork is replaced.
    $variant = $variant === 'white' ? 'white' : 'black';
    $logoUrl = null;

    foreach (['svg', 'png'] as $extension) {
        $logoPath = public_path("images/eclipz-logo-{$variant}.{$extension}");

        if (is_file($logoPath)) {
            $logoUrl = asset("images/eclipz-logo-{$variant}.{$extension}").'?v='.filemtime($logoPath);
            break;
        }
    }
@endphp
@if($logoUrl)
    <img src="{{ $logoUrl }}" {{ $attributes }}>
@else
    <span {{ $attributes->only(['x-show', 'x-cloak'])->merge(['class' => 'font-display text-2xl tracking-wide '.($variant === 'white' ? 'text-white' : 'text-jet')]) }}>ECLIPZ</span>
@endif