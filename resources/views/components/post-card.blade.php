@props(['post', 'featured' => false, 'heading' => 'h2'])
@php
    $heading = in_array($heading, ['h2', 'h3'], true) ? $heading : 'h2';
    $coverUrl = $post->coverUrl();
    $published = $post->published_at?->timezone(config('inquiry.timezone'));
@endphp
<article {{ $attributes->class(['group', 'md:grid md:grid-cols-12 md:gap-10 md:items-center' => $featured]) }}>
    {{-- The picture repeats the title link below, so it is hidden from keyboards and screen readers. --}}
    <a href="{{ route('blog.show', $post->slug) }}" tabindex="-1" aria-hidden="true" class="block {{ $featured ? 'md:col-span-7' : '' }}">
        <div class="relative overflow-hidden bg-jet aspect-[16/10]">
            @if($coverUrl)
                <img src="{{ $coverUrl }}" alt="" loading="lazy" decoding="async" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.03]">
            @else
                <div class="absolute inset-0 flex items-center justify-center">
                    <x-brand-logo variant="white" class="h-8 w-auto opacity-30" alt="" />
                </div>
            @endif
        </div>
    </a>

    <div class="{{ $featured ? 'md:col-span-5 mt-6 md:mt-0' : 'mt-6' }}">
        <p class="font-body text-xs uppercase tracking-wider text-jet/60">
            @if($post->category)<span class="font-medium text-jet">{{ $post->category }}</span><span aria-hidden="true"> &middot; </span>@endif
            @if($published)<time datetime="{{ $published->toDateString() }}">{{ $published->format('j F Y') }}</time>@endif
        </p>

        <{{ $heading }} class="font-display tracking-wide leading-tight mt-3 {{ $featured ? 'text-4xl md:text-5xl' : 'text-3xl' }}">
            <a href="{{ route('blog.show', $post->slug) }}" class="transition-colors group-hover:text-electric-blue">{{ $post->title }}</a>
        </{{ $heading }}>

        @if($post->excerpt)
            <p class="font-body mt-4 text-jet/70 leading-relaxed line-clamp-3">{{ $post->excerpt }}</p>
        @endif

        <p class="font-body mt-4 text-sm text-jet/60">
            {{ $post->author?->name ?? 'The Eclipz team' }} <span aria-hidden="true">&middot;</span> {{ $post->readingMinutes() }} min read
        </p>

        <a href="{{ route('blog.show', $post->slug) }}" aria-label="Read {{ $post->title }}" class="mt-5 inline-flex items-center gap-2 border-b border-jet pb-0.5 font-body text-sm transition-colors hover:border-electric-blue hover:text-electric-blue">
            Read story
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
        </a>
    </div>
</article>