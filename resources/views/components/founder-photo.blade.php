@props(['alt' => 'Richard Ese, Founder & Creative Director', 'ratio' => '1/1'])
@php
    $founderPhotoPath = public_path('images/richard-ese.jpg');
    $hasFounderPhoto = file_exists($founderPhotoPath);
@endphp
<div class="overflow-hidden bg-jet/5" style="aspect-ratio: {{ $ratio }}">
    @if($hasFounderPhoto)
        <img
            src="{{ asset('images/richard-ese.jpg') }}"
            alt="{{ $alt }}"
            loading="lazy"
            class="w-full h-full object-cover"
        >
    @else
        <div class="w-full h-full flex items-center justify-center text-jet/30 font-body text-sm text-center px-4">
            Founder photograph coming soon
        </div>
    @endif
</div>