@php
    $contact = \App\Models\ContactSetting::current();
    $hasDetails = filled($contact->email) || filled($contact->phone) || filled($contact->address);
@endphp

<footer class="bg-jet text-white/80">
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-16 grid gap-12 md:grid-cols-4">
        <div class="md:col-span-2">
            <a href="{{ route('home') }}" aria-label="Eclipz Media & Marketing - Home" class="inline-block">
                <x-brand-logo variant="white" class="h-10 w-auto" alt="Eclipz Media & Marketing" />
            </a>
            <p class="font-body mt-4 max-w-sm text-white/60">Crafting Timeless Visual Stories - creative media &amp; digital marketing from Lagos, Nigeria.</p>
        </div>

        <nav aria-label="Footer">
            <p class="font-body text-sm uppercase tracking-wider text-white/60 mb-4">Navigate</p>
            <ul class="space-y-3 font-body text-sm">
                <li><a href="{{ route('work.index') }}" class="hover:text-warm-gold transition-colors">Work</a></li>
                <li><a href="{{ route('services') }}" class="hover:text-warm-gold transition-colors">Services</a></li>
                <li><a href="{{ route('about') }}" class="hover:text-warm-gold transition-colors">About</a></li>
                <li><a href="{{ route('clients') }}" class="hover:text-warm-gold transition-colors">Clients</a></li>
                <li><a href="{{ route('blog.index') }}" class="hover:text-warm-gold transition-colors">Blog</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-warm-gold transition-colors">Contact Us</a></li>
            </ul>
        </nav>

        <div>
            <p class="font-body text-sm uppercase tracking-wider text-white/60 mb-4">Contact</p>
            @if($hasDetails)
                <x-contact-details :contact="$contact" :address="true" tone="dark" class="font-body text-sm" />
            @else
                <a href="{{ route('contact') }}" class="font-body text-sm hover:text-warm-gold transition-colors">Send us an enquiry</a>
            @endif
        </div>
    </div>

    <div class="border-t border-white/10 py-6 px-6 lg:px-10 flex flex-col md:flex-row items-center justify-between gap-4 max-w-7xl mx-auto">
        <p class="font-body text-xs text-white/60">&copy; {{ date('Y') }} Eclipz Media &amp; Marketing Limited. All rights reserved.</p>
        <x-contact-channels :contact="$contact" tone="dark" class="justify-center md:justify-end" />
    </div>
</footer>