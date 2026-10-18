@props(['eyebrow' => null, 'align' => 'left'])
<div {{ $attributes->merge(['class' => ($align === 'center' ? 'text-center mx-auto ' : '').'max-w-2xl']) }}>
    @if($eyebrow)
        <x-eyebrow>{{ $eyebrow }}</x-eyebrow>
    @endif
    <h2 class="font-display text-4xl md:text-5xl tracking-wide mt-3">{{ $slot }}</h2>
</div>