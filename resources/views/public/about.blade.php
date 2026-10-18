<x-layouts.public title="About | Eclipz Media & Marketing Limited" :meta-description="$company->short_description ?? $company->mission">
    <section class="max-w-7xl mx-auto px-6 lg:px-10 pt-40 pb-20">
        <h1 class="font-display text-5xl md:text-7xl tracking-wide">About Eclipz</h1>
        @if($company->short_description)
            <p class="font-body mt-4 text-jet/60 max-w-2xl">{{ $company->short_description }}</p>
        @endif
    </section>

    @if($company->story)
        <section class="max-w-5xl mx-auto px-6 lg:px-10 pb-20" data-reveal>
            <x-eyebrow class="mb-3">How We Started</x-eyebrow>
            <p class="font-body text-jet/70 leading-relaxed text-lg">{{ $company->story }}</p>
        </section>
    @endif

    @if($company->what_we_do)
        <section class="max-w-5xl mx-auto px-6 lg:px-10 pb-20" data-reveal>
            <x-eyebrow class="mb-3">What We Do</x-eyebrow>
            <p class="font-body text-jet/70 leading-relaxed text-lg">{{ $company->what_we_do }}</p>
        </section>
    @endif

    <section class="max-w-5xl mx-auto px-6 lg:px-10 pb-20 grid md:grid-cols-2 gap-16" data-reveal>
        <div>
            <h2 class="font-display text-2xl tracking-wide mb-4">Mission</h2>
            <p class="font-body text-jet/70 leading-relaxed">{{ $company->mission }}</p>
        </div>
        <div>
            <h2 class="font-display text-2xl tracking-wide mb-4">Vision</h2>
            <p class="font-body text-jet/70 leading-relaxed">{{ $company->vision }}</p>
        </div>
    </section>

    @if($company->differentiator)
        <section class="bg-jet/[0.03] py-20" data-reveal>
            <x-container class="max-w-5xl">
                <x-eyebrow class="mb-3">What Makes Eclipz Different</x-eyebrow>
                <p class="font-body text-jet/70 leading-relaxed text-lg">{{ $company->differentiator }}</p>
            </x-container>
        </section>
    @endif

    <section class="max-w-7xl mx-auto px-6 lg:px-10 py-20" data-reveal>
        <x-eyebrow class="mb-6">Who We Work With</x-eyebrow>
        <div class="flex flex-wrap gap-3">
            @foreach(['Hospitality','Real Estate','Travel & Tourism','Media','Technology','Wellness','Food & Hospitality','Professional Services','Mobility','Events & Conferences'] as $industry)
                <span class="font-body text-sm border border-jet/15 px-4 py-2">{{ $industry }}</span>
            @endforeach
        </div>
    </section>

    <section class="max-w-5xl mx-auto px-6 lg:px-10 pb-28 grid md:grid-cols-2 gap-16 items-center" data-reveal>
        <x-founder-photo alt="{{ $company->founder_name ?? 'Richard Ese' }}" />
        <div>
            <p class="font-body text-electric-blue text-sm uppercase tracking-widest mb-3">{{ $company->founder_title ?? 'Founder & Creative Director' }}</p>
            <h2 class="font-display text-3xl tracking-wide mb-4">{{ $company->founder_name ?? 'Richard Ese' }}</h2>
            @if($company->founder_bio)
                <p class="font-body text-jet/70 leading-relaxed mb-4">{{ $company->founder_bio }}</p>
            @endif
            @if($company->founder_quote)
                <blockquote class="font-body text-jet/60 italic border-l-2 border-warm-gold pl-4 mb-4">&ldquo;{{ $company->founder_quote }}&rdquo;</blockquote>
            @endif
            @if($company->specializationsList())
                <div class="flex flex-wrap gap-2 mt-4">
                    @foreach($company->specializationsList() as $spec)
                        <span class="font-body text-xs uppercase tracking-wider border border-jet/15 px-3 py-1.5">{{ $spec }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</x-layouts.public>