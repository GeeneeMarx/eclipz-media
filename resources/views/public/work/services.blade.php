<x-layouts.public title="Services - Eclipz Media & Marketing">
    <section class="max-w-7xl mx-auto px-6 lg:px-10 pt-40 pb-20">
        <h1 class="font-display text-5xl md:text-7xl tracking-wide">Services</h1>
        <p class="font-body mt-4 text-jet/60 max-w-xl">Media production, brand strategy, digital advertising, and event coverage, built around what businesses actually need to grow.</p>
    </section>

    <section class="max-w-7xl mx-auto px-6 lg:px-10 pb-28 space-y-20">
        @foreach($pillars as $pillar => $items)
            <div class="grid md:grid-cols-3 gap-10">
                <h2 class="font-display text-3xl tracking-wide">{{ $pillar }}</h2>
                <ul class="md:col-span-2 grid sm:grid-cols-2 gap-x-10 gap-y-3 font-body text-jet/70">
                    @foreach($items as $item)
                        <li class="border-b border-jet/10 pb-3">{{ $item->name }}</li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </section>

    <section class="bg-electric-blue text-white py-24 text-center">
        <h2 class="font-display text-4xl md:text-5xl tracking-wide">Need something specific?</h2>
        <a href="{{ route('contact') }}" class="inline-flex mt-8 bg-white text-jet px-8 py-4 font-body hover:bg-warm-gold transition-colors">Start a Project</a>
    </section>
</x-layouts.public>