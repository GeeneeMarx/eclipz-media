<x-layouts.public title="Work - Eclipz Media & Marketing">
    <section class="max-w-7xl mx-auto px-6 lg:px-10 pt-40 pb-16">
        <h1 class="font-display text-5xl md:text-7xl tracking-wide">Our Work</h1>
        <p class="font-body mt-4 text-jet/60 max-w-xl">Selected campaigns and creative work across hospitality, travel, media, and more.</p>
    </section>

    @if($industries->isNotEmpty() || $services->isNotEmpty())
        <section class="max-w-7xl mx-auto px-6 lg:px-10 pb-12">
            <form method="GET" class="flex flex-wrap gap-4 font-body text-sm">
                @if($industries->isNotEmpty())
                    <select name="industry" onchange="this.form.submit()" class="border border-jet/20 px-4 py-2">
                        <option value="">All Industries</option>
                        @foreach($industries as $industry)
                            <option value="{{ $industry->id }}" @selected(request('industry') == $industry->id)>{{ $industry->name }}</option>
                        @endforeach
                    </select>
                @endif
                <select name="type" onchange="this.form.submit()" class="border border-jet/20 px-4 py-2">
                    <option value="">All Types</option>
                    <option value="visual" @selected(request('type') === 'visual')>Campaign / Visual</option>
                    <option value="case_study" @selected(request('type') === 'case_study')>Case Study</option>
                    <option value="digital" @selected(request('type') === 'digital')>Digital Experience</option>
                </select>
                @if($services->isNotEmpty())
                    <select name="service" onchange="this.form.submit()" class="border border-jet/20 px-4 py-2">
                        <option value="">All Services</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" @selected(request('service') == $service->id)>{{ $service->name }}</option>
                        @endforeach
                    </select>
                @endif
                @if(request()->hasAny(['industry', 'type', 'service']))
                    <a href="{{ route('work.index') }}" class="font-body text-sm underline self-center">Clear filters</a>
                @endif
            </form>
        </section>
    @endif

    <section class="max-w-7xl mx-auto px-6 lg:px-10 pb-28">
        @if($projects->isEmpty())
            <p class="font-body text-jet/50">
                @if(request()->hasAny(['industry', 'type', 'service']))
                    No published work matches these filters yet.
                @else
                    No published work yet - check back soon.
                @endif
            </p>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($projects as $project)
                    <x-project-preview :project="$project" />
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.public>