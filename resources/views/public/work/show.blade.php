<x-layouts.public
    :title="$project->name . ' - Eclipz Media & Marketing'"
    :meta-description="$project->short_description ?? $project->description"
    :image="$project->cover_image"
>
    <section class="relative bg-jet text-white pt-40 pb-24">
        <div class="max-w-7xl mx-auto px-6 lg:px-10">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                @if($project->industry)
                    <span class="font-body text-warm-gold text-sm uppercase tracking-widest">{{ $project->industry->name }}</span>
                @endif
                <span class="font-body text-white/40 text-sm uppercase tracking-widest">{{ $project->project_type->label() }}</span>
            </div>
            <h1 class="font-display text-5xl md:text-7xl tracking-wide">{{ $project->name }}</h1>
            <div class="flex flex-wrap gap-x-8 gap-y-2 mt-6 font-body text-white/60 text-sm">
                @if($project->client)
                    <span>{{ $project->client->name }}</span>
                @endif
                @if($project->location)
                    <span>{{ $project->location }}</span>
                @endif
                @if($project->year)
                    <span>{{ $project->year }}</span>
                @endif
            </div>
        </div>
    </section>

    @if($project->cover_image)
        <img src="{{ Storage::url($project->cover_image) }}" alt="{{ $project->name }}" class="w-full aspect-video object-cover">
    @endif

    <section class="max-w-5xl mx-auto px-6 lg:px-10 py-20 grid md:grid-cols-3 gap-12">
        <div class="md:col-span-2 font-body text-jet/80 leading-relaxed space-y-6">
            @if($project->description)
                <p>{{ $project->description }}</p>
            @endif

            @if($project->project_url)
                <a href="{{ $project->project_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-electric-blue hover:underline">
                    Visit the live project
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            @endif

            @if($project->caseStudyDetail)
                @if($project->caseStudyDetail->challenge)
                    <div><h2 class="font-display text-2xl tracking-wide mb-2">The Challenge</h2><p>{{ $project->caseStudyDetail->challenge }}</p></div>
                @endif
                @if($project->caseStudyDetail->strategy)
                    <div><h2 class="font-display text-2xl tracking-wide mb-2">The Strategy</h2><p>{{ $project->caseStudyDetail->strategy }}</p></div>
                @endif
                @if($project->caseStudyDetail->execution)
                    <div><h2 class="font-display text-2xl tracking-wide mb-2">Execution</h2><p>{{ $project->caseStudyDetail->execution }}</p></div>
                @endif
            @endif
        </div>

        <div class="space-y-8">
            @if($project->services->isNotEmpty())
                <div>
                    <p class="font-body text-xs uppercase tracking-wider text-jet/40 mb-3">Services</p>
                    <ul class="font-body text-sm space-y-1">
                        @foreach($project->services as $service)
                            <li>{{ $service->name }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($project->metrics->isNotEmpty())
                <div>
                    <p class="font-body text-xs uppercase tracking-wider text-jet/40 mb-3">Results</p>
                    <dl class="space-y-3">
                        @foreach($project->metrics as $metric)
                            <div>
                                <dt class="font-display text-3xl text-electric-blue">{{ $metric->value }}</dt>
                                <dd class="font-body text-xs text-jet/50">{{ $metric->label }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            @endif
        </div>
    </section>

    @if($project->images->isNotEmpty())
        <section class="max-w-7xl mx-auto px-6 lg:px-10 pb-20 grid md:grid-cols-2 gap-6">
            @foreach($project->images as $image)
                <img src="{{ Storage::url($image->path) }}" alt="{{ $image->alt_text ?? $project->name }}" loading="lazy" class="w-full object-cover">
            @endforeach
        </section>
    @endif

    <x-video-embed :project="$project" />

    @if($relatedProjects->isNotEmpty())
        <section class="bg-jet/[0.03] py-28" data-reveal>
            <x-container>
                <x-section-heading eyebrow="Related" class="mb-14">More Work</x-section-heading>
                <div class="grid md:grid-cols-3 gap-8">
                    @foreach($relatedProjects as $related)
                        <x-project-preview :project="$related" />
                    @endforeach
                </div>
            </x-container>
        </section>
    @endif

    @if($previousProject || $nextProject)
        <nav aria-label="Project navigation" class="max-w-7xl mx-auto px-6 lg:px-10 py-16 flex items-center justify-between font-body text-sm border-t border-jet/10">
            @if($previousProject)
                <a href="{{ route('work.show', $previousProject->slug) }}" class="inline-flex items-center gap-2 hover:text-electric-blue transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                    </svg>
                    {{ $previousProject->name }}
                </a>
            @else
                <span></span>
            @endif
            @if($nextProject)
                <a href="{{ route('work.show', $nextProject->slug) }}" class="inline-flex items-center gap-2 hover:text-electric-blue transition-colors">
                    {{ $nextProject->name }}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            @endif
        </nav>
    @endif

    <x-cta-section :button-href="route('contact')">Ready to start your project?</x-cta-section>
</x-layouts.public>