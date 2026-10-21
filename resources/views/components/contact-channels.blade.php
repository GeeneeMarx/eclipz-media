@props(['contact', 'tone' => 'dark'])
@php
    $buttonClass = $tone === 'dark'
        ? 'border-white/25 text-white/80 hover:border-warm-gold hover:text-warm-gold'
        : 'border-jet/25 text-jet/80 hover:border-electric-blue hover:text-electric-blue';

    $channels = [];

    if ($whatsappUrl = $contact->whatsappUrl()) {
        $channels[] = ['icon' => 'whatsapp', 'href' => $whatsappUrl, 'label' => 'Chat with Eclipz on WhatsApp'];
    }

    foreach (['instagram' => 'Instagram', 'facebook' => 'Facebook', 'linkedin' => 'LinkedIn', 'tiktok' => 'TikTok'] as $field => $network) {
        if ($socialUrl = $contact->socialUrl($field)) {
            $channels[] = ['icon' => $field, 'href' => $socialUrl, 'label' => 'Eclipz on '.$network];
        }
    }
@endphp
@if(count($channels))
    <ul role="list" aria-label="Eclipz on WhatsApp and social media" {{ $attributes->merge(['class' => 'flex flex-wrap gap-3']) }}>
        @foreach($channels as $channel)
            <li>
                <a
                    href="{{ $channel['href'] }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="{{ $channel['label'] }}"
                    title="{{ $channel['label'] }}"
                    class="inline-flex items-center justify-center w-11 h-11 border transition-colors {{ $buttonClass }}"
                >
                    <x-contact-icon :name="$channel['icon']" class="w-5 h-5" />
                </a>
            </li>
        @endforeach
    </ul>
@endif