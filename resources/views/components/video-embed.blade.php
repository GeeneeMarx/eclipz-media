@props(['project'])
@if($project->video_url)
    <section class="max-w-5xl mx-auto px-6 lg:px-10 pb-20" data-reveal>
        @if($project->videoEmbedUrl())
            <div class="aspect-video bg-jet/5">
                <iframe
                    src="{{ $project->videoEmbedUrl() }}"
                    title="{{ $project->name }} video"
                    loading="lazy"
                    allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    class="w-full h-full"
                ></iframe>
            </div>
        @elseif($project->isDirectVideoFile())
            <video controls preload="metadata" class="w-full aspect-video bg-jet">
                <source src="{{ $project->video_url }}">
            </video>
        @else
            <a href="{{ $project->video_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 font-body text-electric-blue hover:underline">
                Watch the video
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </a>
        @endif
    </section>
@endif