<x-layouts.public :title="$company->company_name" :meta-description="$company->mission">

    {{-- HERO --}}
    @php
        $heroImagePath = public_path('images/hero-eclipz.jpg');
        $hasHeroImage = file_exists($heroImagePath);
    @endphp
    <section
        class="relative min-h-screen flex items-end bg-jet text-white overflow-hidden hero-media"
        @if($hasHeroImage)
            style="background-image: linear-gradient(to right, rgba(10,10,10,0.88), rgba(10,10,10,0.5) 55%, rgba(10,10,10,0.2)), url('{{ asset('images/hero-eclipz.jpg') }}');"
        @endif
    >
        <x-container class="relative pb-24 pt-40">
            <x-eyebrow class="text-warm-gold mb-6">Lagos, Nigeria</x-eyebrow>
            <h1 class="font-display text-6xl md:text-8xl leading-[0.95] tracking-wide max-w-4xl">
                {{ $company->tagline ?? 'Crafting Timeless Visual Stories' }}
            </h1>
            <p class="font-body mt-8 max-w-xl text-white/70 text-lg">
                Creative media, digital marketing, advertising and strategic storytelling for brands that want to be seen and remembered.
            </p>
            <div class="flex flex-wrap gap-4 mt-10">
                <x-button href="{{ route('contact') }}" variant="outline-light">
                    Start a Project
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </x-button>
                <x-button href="{{ route('work.index') }}" variant="outline-light">
                    View Our Portfolio
                </x-button>
            </div>
        </x-container>
    </section>

    {{-- SELECTED WORK --}}
    <x-container class="py-28" data-reveal>
        <div class="flex items-end justify-between mb-14">
            <x-section-heading eyebrow="Portfolio">Selected Work</x-section-heading>
            <a href="{{ route('work.index') }}" class="hidden md:inline-flex items-center gap-2 font-body text-sm hover:text-electric-blue transition-colors">
                View all work
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>

        @if($featuredProjects->isEmpty())
            <p class="font-body text-jet/50">Featured work is being prepared - check back soon.</p>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($featuredProjects as $project)
                    <x-project-preview :project="$project" />
                @endforeach
            </div>
        @endif
    </x-container>

    {{-- CAPABILITIES (driven by the same service categories as /services) --}}
    <section class="bg-jet text-white py-28" data-reveal>
        <x-container>
            <x-section-heading eyebrow="What We Do" class="mb-14 [&_h2]:text-white">Capabilities</x-section-heading>
            @if($categories->isEmpty())
                <p class="font-body text-white/50">Service details are being updated - check back soon.</p>
            @else
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-x-10 gap-y-12">
                    @foreach($categories as $category)
                        <div class="border-t border-white/20 pt-6">
                            <p class="font-display text-xl tracking-wide">{{ $category->name }}</p>
                            <p class="font-body text-sm text-white/50 mt-2">
                                {{ $category->services->take(3)->pluck('name')->implode(', ') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-container>
    </section>

    {{-- WHY ECLIPZ --}}
    <x-container class="py-28 grid md:grid-cols-2 gap-16 items-center" data-reveal>
        <div>
            <x-eyebrow class="mb-4">Why Eclipz</x-eyebrow>
            <h2 class="font-display text-4xl md:text-5xl tracking-wide mb-6">Strategy, content, advertising and storytelling, working together.</h2>
            <p class="font-body text-jet/70 leading-relaxed">{{ $company->mission }}</p>
        </div>
        <div>
            <x-eyebrow class="mb-4">Vision</x-eyebrow>
            <p class="font-body text-jet/70 leading-relaxed text-lg">{{ $company->vision }}</p>
        </div>
    </x-container>

    {{-- INDUSTRIES --}}
    <section class="bg-jet/[0.03] py-28" data-reveal>
        <x-container>
            <x-section-heading eyebrow="Who We Work With" class="mb-14">Industries</x-section-heading>
            <div class="flex flex-wrap gap-3">
                @foreach(['Hospitality & Shortlets','Real Estate & Property','Travel & Tourism','Corporate & Technology','Media, Lifestyle & Podcasts','Wellness','Food & Hospitality','Mobility','Events & Conferences','Professional Services'] as $industry)
                    <span class="font-body text-sm border border-jet/15 px-4 py-2">{{ $industry }}</span>
                @endforeach
            </div>
        </x-container>
    </section>

    {{-- CLIENTS --}}
    @if($clients->isNotEmpty())
        <x-container class="py-28" data-reveal>
            <x-section-heading eyebrow="Credibility" class="mb-14">Trusted By</x-section-heading>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-10 items-center">
                @foreach($clients as $client)
                    <x-client-logo :client="$client" />
                @endforeach
            </div>
        </x-container>
    @endif

    {{-- TESTIMONIALS --}}
    @if($testimonials->isNotEmpty())
        <section class="bg-jet/[0.03] py-28" data-reveal>
            <x-container class="max-w-5xl">
                <x-section-heading eyebrow="Client Words" align="center" class="mb-14">What Clients Say</x-section-heading>
                <div class="grid md:grid-cols-3 gap-10">
                    @foreach($testimonials as $testimonial)
                        <blockquote class="font-body">
                            <p class="text-jet/80 leading-relaxed">&ldquo;{{ $testimonial->quote }}&rdquo;</p>
                            <footer class="mt-4 text-sm text-jet/50">
                                <span class="font-medium text-jet">{{ $testimonial->client_name }}</span>
                                @if($testimonial->company) - {{ $testimonial->company }}@endif
                            </footer>
                        </blockquote>
                    @endforeach
                </div>
            </x-container>
        </section>
    @endif

    {{-- FOUNDER --}}
    <x-container class="py-28 grid md:grid-cols-2 gap-16 items-center" data-reveal>
        <x-founder-photo alt="Richard Ese, Founder & Creative Director" />
        <div>
            <x-eyebrow class="mb-3">Founder &amp; Creative Director</x-eyebrow>
            <h2 class="font-display text-3xl tracking-wide mb-4">Richard Ese</h2>
            <p class="font-body text-jet/70 leading-relaxed">Leads Eclipz's creative direction across every project, from concept to campaign.</p>
        </div>
    </x-container>

    {{-- FINAL CTA --}}
    <x-cta-section button-label="Work With Us" :button-href="route('contact')">
        Ready to tell your story?
    </x-cta-section>

</x-layouts.public>