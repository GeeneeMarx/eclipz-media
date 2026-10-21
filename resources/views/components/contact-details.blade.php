@props(['contact', 'tone' => 'dark', 'address' => false])
@php
    $hoverClass = $tone === 'dark' ? 'hover:text-warm-gold' : 'hover:text-electric-blue';
    $items = [];

    if (filled($contact->email)) {
        $items[] = ['icon' => 'envelope', 'text' => $contact->email, 'href' => 'mailto:'.$contact->email, 'label' => 'Email '.$contact->email];
    }

    if (filled($contact->phone)) {
        $items[] = ['icon' => 'telephone', 'text' => $contact->phone, 'href' => 'tel:'.preg_replace('/[^\d+]/', '', $contact->phone), 'label' => 'Call '.$contact->phone];
    }

    if ($address && filled($contact->address)) {
        $items[] = ['icon' => 'geo-alt', 'text' => $contact->address, 'href' => null, 'label' => null];
    }
@endphp
@if(count($items))
    <ul {{ $attributes->merge(['class' => 'space-y-2']) }}>
        @foreach($items as $item)
            <li class="flex items-start gap-3">
                <x-contact-icon :name="$item['icon']" class="w-5 h-5 mt-1.5 shrink-0 opacity-70" />
                @if($item['href'])
                    <a href="{{ $item['href'] }}" aria-label="{{ $item['label'] }}" class="inline-block py-1 min-w-0 [overflow-wrap:anywhere] transition-colors {{ $hoverClass }}">{{ $item['text'] }}</a>
                @else
                    <span class="py-1 min-w-0 [overflow-wrap:anywhere]">{{ $item['text'] }}</span>
                @endif
            </li>
        @endforeach
    </ul>
@endif