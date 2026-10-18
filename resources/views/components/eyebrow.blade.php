@php($hasColor = str_contains($attributes->get('class', ''), 'text-'))
<p {{ $attributes->class(['font-body text-sm uppercase tracking-widest', 'text-electric-blue' => ! $hasColor]) }}>
    {{ $slot }}
</p>