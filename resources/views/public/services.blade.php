<x-layouts.public title="Services | Eclipz Media & Marketing Limited">
    <section class="max-w-7xl mx-auto px-6 lg:px-10 pt-40 pb-20">
        <h1 class="font-display text-5xl md:text-7xl tracking-wide">Services</h1>
        <p class="font-body mt-4 text-jet/60 max-w-xl">Media production, brand strategy, digital advertising, and event coverage, built around what businesses actually need to grow.</p>
    </section>

    <section class="max-w-7xl mx-auto px-6 lg:px-10 pb-28 space-y-20">
        @forelse($categories as $category)
            <div class="grid md:grid-cols-3 gap-10" data-reveal>
                <h2 class="font-display text-3xl tracking-wide">{{ $category->name }}</h2>
                <ul class="md:col-span-2 grid sm:grid-cols-2 gap-x-10 gap-y-3 font-body text-jet/70">
                    @foreach($category->services as $service)
                        <li class="border-b border-jet/10 pb-3">{{ $service->name }}</li>
                    @endforeach
                </ul>
            </div>
        @empty
            <p class="font-body text-jet/50">Service details are being updated - check back soon.</p>
        @endforelse
    </section>

    <x-cta-section :button-href="route('contact')">Need something specific?</x-cta-section>
</x-layouts.public>