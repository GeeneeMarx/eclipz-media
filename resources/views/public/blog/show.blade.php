@php
    $coverUrl = $post->coverUrl();
    $published = $post->published_at?->timezone(config('inquiry.timezone'));
    $authorName = $post->author?->name ?? 'The Eclipz team';

    $jsonLd = json_encode(array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $post->title,
        'description' => $post->snippet(),
        'image' => $coverUrl ? url($coverUrl) : null,
        'datePublished' => $post->published_at?->toAtomString(),
        'dateModified' => $post->updated_at?->toAtomString(),
        'author' => ['@type' => 'Person', 'name' => $authorName],
        'publisher' => ['@type' => 'Organization', 'name' => 'Eclipz Media & Marketing Limited'],
        'mainEntityOfPage' => route('blog.show', $post->slug),
    ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
@endphp
<x-layouts.public
    :title="$post->title.' | Eclipz Media & Marketing Limited'"
    :meta-description="$post->snippet()"
    :image="$post->cover_image"
    og-type="article"
    :canonical="route('blog.show', $post->slug)"
    :noindex="$isPreview"
>
    <x-slot:head>
        @unless($isPreview)
            <script type="application/ld+json">{!! $jsonLd !!}</script>
        @endunless
    </x-slot:head>

    @if($isPreview)
        <div role="status" class="fixed bottom-4 left-4 z-40 bg-warm-gold text-jet px-4 py-2 font-body text-sm">
            Preview only: this post is not visible to visitors.
        </div>
    @endif

    <article>
        <header class="max-w-4xl mx-auto px-6 lg:px-10 pt-40 pb-10">
            <nav aria-label="Breadcrumb" class="font-body text-xs uppercase tracking-wider text-jet/60 mb-6">
                <a href="{{ route('blog.index') }}" class="transition-colors hover:text-electric-blue">Blog</a>
                @if($post->category)
                    <span aria-hidden="true"> / </span>
                    <a href="{{ route('blog.index', ['category' => $post->category]) }}" class="transition-colors hover:text-electric-blue">{{ $post->category }}</a>
                @endif
            </nav>

            <h1 class="font-display text-5xl md:text-7xl tracking-wide leading-[0.98]">{{ $post->title }}</h1>

            @if($post->excerpt)
                <p class="font-body mt-6 text-lg text-jet/70 max-w-2xl leading-relaxed">{{ $post->excerpt }}</p>
            @endif

            <p class="font-body mt-6 text-sm text-jet/60">
                {{ $authorName }}
                @if($published)
                    <span aria-hidden="true">&middot;</span>
                    <time datetime="{{ $published->toDateString() }}">{{ $published->format('j F Y') }}</time>
                @endif
                <span aria-hidden="true">&middot;</span>
                {{ $post->readingMinutes() }} min read
            </p>
        </header>

        @if($coverUrl)
            <figure class="max-w-6xl mx-auto px-6 lg:px-10">
                <img
                    src="{{ $coverUrl }}"
                    alt="{{ $post->cover_alt ?: $post->title }}"
                    fetchpriority="high"
                    decoding="async"
                    class="w-full aspect-[16/9] object-cover bg-jet"
                >
            </figure>
        @endif

        <div class="max-w-3xl mx-auto px-6 lg:px-10 py-14 article-body">
            {!! $post->safeBody() !!}
        </div>

        <footer class="max-w-3xl mx-auto px-6 lg:px-10 pb-20">
            <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 font-body text-sm border-b border-jet pb-0.5 transition-colors hover:border-electric-blue hover:text-electric-blue">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                </svg>
                All stories
            </a>
        </footer>
    </article>

    @if($morePosts->isNotEmpty())
        <section class="bg-jet/[0.03] py-24" aria-labelledby="more-stories">
            <div class="max-w-7xl mx-auto px-6 lg:px-10">
                <h2 id="more-stories" class="font-display text-4xl tracking-wide mb-12">More stories</h2>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-14">
                    @foreach($morePosts as $morePost)
                        <x-post-card :post="$morePost" heading="h3" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-cta-section button-label="Send an Enquiry" :button-href="route('contact')">Ready to tell your story?</x-cta-section>
</x-layouts.public>