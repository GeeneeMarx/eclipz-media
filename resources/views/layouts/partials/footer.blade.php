@php
    $contact = \App\Models\ContactSetting::current();
    $whatsappUrl = $contact->whatsappUrl();
@endphp

<footer class="bg-jet text-white/80">
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-16 grid gap-12 md:grid-cols-4">
        <div class="md:col-span-2">
            <p class="font-display text-3xl text-white tracking-wide">ECLIPZ</p>
            <p class="font-body mt-3 max-w-sm text-white/60">Crafting Timeless Visual Stories - creative media &amp; digital marketing from Lagos, Nigeria.</p>
        </div>

        <nav aria-label="Footer">
            <p class="font-body text-sm uppercase tracking-wider text-white/60 mb-4">Navigate</p>
            <ul class="space-y-3 font-body text-sm">
                <li><a href="{{ route('work.index') }}" class="hover:text-warm-gold transition-colors">Work</a></li>
                <li><a href="{{ route('services') }}" class="hover:text-warm-gold transition-colors">Services</a></li>
                <li><a href="{{ route('about') }}" class="hover:text-warm-gold transition-colors">About</a></li>
                <li><a href="{{ route('process') }}" class="hover:text-warm-gold transition-colors">Process</a></li>
                <li><a href="{{ route('clients') }}" class="hover:text-warm-gold transition-colors">Clients</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-warm-gold transition-colors">Contact</a></li>
            </ul>
        </nav>

        <div>
            <p class="font-body text-sm uppercase tracking-wider text-white/60 mb-4">Contact</p>
            <ul class="space-y-3 font-body text-sm">
                @if($contact->email)
                    <li><a href="mailto:{{ $contact->email }}" class="hover:text-warm-gold transition-colors">{{ $contact->email }}</a></li>
                @endif
                @if($contact->phone)
                    <li><a href="tel:{{ preg_replace('/[^\d+]/', '', $contact->phone) }}" class="hover:text-warm-gold transition-colors">{{ $contact->phone }}</a></li>
                @endif
                @if($whatsappUrl)
                    <li><a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-warm-gold transition-colors">WhatsApp</a></li>
                @endif
                @if($contact->address)
                    <li class="text-white/60">{{ $contact->address }}</li>
                @endif
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10 py-6 px-6 lg:px-10 flex flex-col md:flex-row items-center justify-between gap-4 max-w-7xl mx-auto">
        <p class="font-body text-xs text-white/60">&copy; {{ date('Y') }} Eclipz Media &amp; Marketing Limited. All rights reserved.</p>
        <div class="flex gap-5">
            @if($contact->instagram)<a href="{{ $contact->instagram }}" target="_blank" rel="noopener noreferrer" class="text-white/60 hover:text-white transition-colors text-xs">Instagram</a>@endif
            @if($contact->facebook)<a href="{{ $contact->facebook }}" target="_blank" rel="noopener noreferrer" class="text-white/60 hover:text-white transition-colors text-xs">Facebook</a>@endif
            @if($contact->linkedin)<a href="{{ $contact->linkedin }}" target="_blank" rel="noopener noreferrer" class="text-white/60 hover:text-white transition-colors text-xs">LinkedIn</a>@endif
            @if($contact->tiktok)<a href="{{ $contact->tiktok }}" target="_blank" rel="noopener noreferrer" class="text-white/60 hover:text-white transition-colors text-xs">TikTok</a>@endif
        </div>
    </div>
</footer>