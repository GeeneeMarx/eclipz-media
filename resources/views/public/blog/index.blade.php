@php
    $canonical = $posts->currentPage() > 1
        ? $posts->url($posts->currentPage())
        : route('blog.index', $activeCategory ? ['category' => $activeCategory] : []);
    $pageTitle = 'Blog'.($posts->currentPage() > 1 ? ' - Page '.$posts->currentPage() : '').' | Eclipz Media & Marketing Limited';
@endphp
<x-layouts.public
    :title="$pageTitle"
    meta-description="News, ideas and behind-the-scenes stories from Eclipz Media & Marketing, a creative media and digital marketing agency in Lagos, Nigeria."
    :canonical="$canonical"
>
    <section class="max-w-7xl mx-auto px-6 lg:px-10 pt-40 pb-12">
        <x-eyebrow class="mb-4">Insights &amp; Updates</x-eyebrow>
        <h1 class="font-display text-5xl md:text-7xl tracking-wide">Stories from Eclipz</h1>
        <p class="font-body mt-4 text-jet/60 max-w-xl">News, ideas and behind-the-scenes updates from the Eclipz team.</p>

        @if($categories->count() > 1)
            <nav aria-label="Filter stories by category" class="mt-10 flex flex-wrap gap-3 font-body text-sm">
                <a
                    href="{{ route('blog.index') }}"
                    @if(! $activeCategory) aria-current="page" @endif
                    class="inline-flex items-center px-4 py-2 border transition-colors {{ ! $activeCategory ? 'bg-jet text-white border-jet' : 'border-jet/20 hover:border-jet' }}"
                >All</a>
                @foreach($categories as $category)
                    <a
                        href="{{ route('blog.index', ['category' => $category]) }}"
                        @if($activeCategory === $category) aria-current="page" @endif
                        class="inline-flex items-center px-4 py-2 border transition-colors {{ $activeCategory === $category ? 'bg-jet text-white border-jet' : 'border-jet/20 hover:border-jet' }}"
                    >{{ $category }}</a>
                @endforeach
            </nav>
        @endif
    </section>

    <section class="max-w-7xl mx-auto px-6 lg:px-10 pb-28">
        @if($posts->isEmpty())
            <div class="border border-jet/15 px-8 py-16 text-center">
                <p class="font-display text-3xl tracking-wide">No stories published yet</p>
                <p class="font-body mt-3 text-jet/60 max-w-md mx-auto">We are preparing our first updates. In the meantime, see what we have been working on.</p>
                <a href="{{ route('work.index') }}" class="mt-8 inline-flex items-center border border-jet px-7 py-3.5 font-body transition-colors hover:bg-jet hover:text-white">View our work</a>
            </div>
        @else
            @php
                $featured = $posts->currentPage() === 1 ? $posts->getCollection()->first() : null;
                $others = $featured ? $posts->getCollection()->slice(1) : $posts->getCollection();
            @endphp

            @if($featured)
                <x-post-card :post="$featured" :featured="true" class="mb-16 pb-16 border-b border-jet/10" />
            @endif

            @if($others->isNotEmpty())
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-14">
                    @foreach($others as $post)
                        <x-post-card :post="$post" />
                    @endforeach
                </div>
            @endif

            <div class="mt-16">
                {{ $posts->links('vendor.pagination.eclipz') }}
            </div>
        @endif
    </section>

    <x-cta-section button-label="Send an Enquiry" :button-href="route('contact')">Ready to tell your story?</x-cta-section>
</x-layouts.public>