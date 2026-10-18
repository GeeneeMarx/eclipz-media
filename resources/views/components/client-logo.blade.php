@props(['client'])
<div class="flex items-center justify-center h-20 px-4">
    @if($client->logo)
        <img
            src="{{ \Illuminate\Support\Facades\Storage::url($client->logo) }}"
            alt="{{ $client->name }}"
            loading="lazy"
            style="--logo-scale: {{ $client->display_scale ?? 1 }};"
            class="client-logo max-h-full max-w-full object-contain"
        >
    @else
        <p class="font-body text-sm text-jet/40 text-center">{{ $client->name }}</p>
    @endif
</div>