<x-layouts.public title="Clients & Partners | Eclipz Media & Marketing Limited">
    <section class="max-w-7xl mx-auto px-6 lg:px-10 pt-40 pb-20">
        <h1 class="font-display text-5xl md:text-7xl tracking-wide">Clients &amp; Partners</h1>
        <p class="font-body mt-4 text-jet/60 max-w-xl">Brands we've worked with across hospitality, travel, media, and more.</p>
    </section>

    @if($partners->isEmpty() && $clients->isEmpty())
        <section class="max-w-7xl mx-auto px-6 lg:px-10 pb-28">
            <p class="font-body text-jet/50">Client logos are being added - check back soon.</p>
        </section>
    @endif

    @if($partners->isNotEmpty())
        <section class="max-w-7xl mx-auto px-6 lg:px-10 pb-20" data-reveal>
            <x-section-heading eyebrow="Partners" class="mb-10">Our Partners</x-section-heading>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-10 items-center">
                @foreach($partners as $client)
                    <x-client-logo :client="$client" />
                @endforeach
            </div>
        </section>
    @endif

    @if($clients->isNotEmpty())
        <section class="max-w-7xl mx-auto px-6 lg:px-10 pb-28" data-reveal>
            <x-section-heading eyebrow="Clients" class="mb-10">Our Clients</x-section-heading>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-10 items-center">
                @foreach($clients as $client)
                    <x-client-logo :client="$client" />
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.public>