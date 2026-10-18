<x-layouts.public title="Our Process | Eclipz Media & Marketing Limited">
    <section class="max-w-7xl mx-auto px-6 lg:px-10 pt-40 pb-20">
        <h1 class="font-display text-5xl md:text-7xl tracking-wide">Our Process</h1>
        <p class="font-body mt-4 text-jet/60 max-w-xl">A clear path from first conversation to finished campaign.</p>
    </section>

    <section class="max-w-5xl mx-auto px-6 lg:px-10 pb-28">
        @if($steps->isEmpty())
            <p class="font-body text-jet/50">Process details are being updated - check back soon.</p>
        @else
            <ol class="space-y-0">
                @foreach($steps as $i => $step)
                    <li class="grid md:grid-cols-12 gap-6 py-8 border-t border-jet/10 @if($loop->last) border-b @endif items-baseline" data-reveal>
                        <span class="md:col-span-2 font-display text-4xl text-jet/20">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="md:col-span-3 font-display text-2xl tracking-wide">{{ $step->title }}</span>
                        <span class="md:col-span-7 font-body text-jet/60">{{ $step->description }}</span>
                    </li>
                @endforeach
            </ol>
        @endif
    </section>
</x-layouts.public>