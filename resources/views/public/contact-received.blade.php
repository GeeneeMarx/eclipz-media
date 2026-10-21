<x-layouts.public title="Enquiry received | Eclipz Media & Marketing Limited" :noindex="true">
    <section class="max-w-3xl mx-auto px-6 lg:px-10 pt-40 pb-28">
        <x-eyebrow class="mb-4">Enquiry received</x-eyebrow>
        <h1 class="font-display text-5xl md:text-7xl tracking-wide">Thank you</h1>
        <p class="font-body mt-4 text-jet/70 max-w-xl leading-relaxed">
            Your enquiry has been received. The Eclipz team will review your request and get back to you.
        </p>

        <div
            class="mt-12 border border-jet/15 p-8"
            x-data="{
                copied: false,
                failed: false,
                async copy() {
                    const text = this.$refs.reference.textContent.trim();
                    try {
                        if (navigator.clipboard && window.isSecureContext) {
                            await navigator.clipboard.writeText(text);
                        } else {
                            const field = document.createElement('textarea');
                            field.value = text;
                            field.style.position = 'fixed';
                            field.style.opacity = '0';
                            document.body.appendChild(field);
                            field.select();
                            const ok = document.execCommand('copy');
                            document.body.removeChild(field);
                            if (!ok) { throw new Error('copy failed'); }
                        }
                        this.copied = true;
                        this.failed = false;
                        setTimeout(() => { this.copied = false; }, 2000);
                    } catch (error) {
                        this.failed = true;
                    }
                }
            }"
        >
            <p class="font-body text-xs uppercase tracking-wider text-jet/50">Your Inquiry ID</p>
            <p x-ref="reference" class="font-display text-5xl md:text-6xl tracking-wide mt-2 break-all">{{ $reference }}</p>

            <div class="mt-6 flex flex-wrap items-center gap-4">
                <button
                    type="button"
                    @click="copy()"
                    class="inline-flex items-center gap-2 border border-jet px-5 py-2.5 text-sm font-body hover:bg-jet hover:text-white transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span x-text="copied ? 'Copied' : 'Copy ID'">Copy ID</span>
                </button>
                <p x-show="failed" x-cloak class="font-body text-sm text-red-600">Could not copy automatically. Please select the ID and copy it manually.</p>
                <span class="sr-only" aria-live="polite" x-text="copied ? 'Inquiry ID copied' : ''"></span>
            </div>

            <p class="font-body mt-8 text-jet/70 leading-relaxed">
                Please quote this ID whenever you follow up with the Eclipz team about this enquiry.
            </p>
        </div>

        <div class="mt-10 flex flex-wrap gap-4">
            @if($whatsappUrl)
                <a
                    href="{{ $whatsappUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-3 bg-jet text-white px-7 py-3.5 font-body hover:bg-electric-blue transition-colors"
                >
                    <x-contact-icon name="whatsapp" class="w-4 h-4" />
                    Follow up on WhatsApp
                </a>
            @endif
            <a href="{{ route('home') }}" class="inline-flex items-center border border-jet px-7 py-3.5 font-body hover:bg-jet hover:text-white transition-colors">
                Back to home
            </a>
        </div>
    </section>
</x-layouts.public>