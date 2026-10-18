@props(['src' => null, 'alt' => '', 'ratio' => '4/5'])
<div class="overflow-hidden bg-jet/5" style="aspect-ratio: {{ $ratio }}">
    @if($src)
        <img
            src="{{ \Illuminate\Support\Facades\Storage::url($src) }}"
            alt="{{ $alt }}"
            loading="lazy"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
        >
    @else
        <div class="w-full h-full flex items-center justify-center text-jet/30 font-body text-sm text-center px-4">
            Image coming soon
        </div>
    @endif
</div>
