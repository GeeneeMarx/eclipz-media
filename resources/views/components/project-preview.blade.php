@props(['project'])
<a href="{{ route('work.show', $project->slug) }}" class="group block" data-reveal>
    <x-responsive-media :src="$project->cover_image" :alt="$project->name" ratio="4/5" />
    <div class="mt-4 flex items-start justify-between gap-3">
        <div>
            <p class="font-body text-xs uppercase tracking-wider text-jet/50">{{ $project->industry?->name }}</p>
            <p class="font-display text-2xl mt-1">{{ $project->name }}</p>
        </div>
    </div>
    @if($project->short_description)
        <p class="font-body text-sm text-jet/60 mt-2">{{ $project->short_description }}</p>
    @endif
</a>