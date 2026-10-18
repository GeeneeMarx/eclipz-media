@props(['buttonLabel' => 'Start a Project', 'buttonHref' => null])
<section class="bg-electric-blue text-white py-28 text-center" data-reveal>
    <x-container>
        <h2 class="font-display text-4xl md:text-6xl tracking-wide">{{ $slot }}</h2>
        @if($buttonHref)
            <x-button :href="$buttonHref" variant="solid" class="mt-10">{{ $buttonLabel }}</x-button>
        @endif
    </x-container>
</section>