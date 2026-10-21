<header
    x-data="{
        open: false,
        scrolled: false,
        light: false,
        init() {
            const hero = document.querySelector('#main-content > section:first-child');
            this.light = !!hero && hero.classList.contains('bg-jet');
            this.scrolled = window.scrollY > 24;
            window.addEventListener('scroll', () => { this.scrolled = window.scrollY > 24; });
        },
        get onDark() { return this.light && !this.scrolled && !this.open; },
        get ctaClass() { return this.onDark ? 'border-white hover:bg-white hover:text-jet' : 'border-jet hover:bg-jet hover:text-white'; },
    }"
    x-effect="document.documentElement.style.overflow = open ? 'hidden' : ''"
    :class="onDark ? 'bg-transparent text-white' : 'bg-white/95 backdrop-blur border-b border-jet/10 text-jet'"
    class="fixed top-0 inset-x-0 z-50 transition-colors duration-300"
>
    <div class="max-w-7xl mx-auto px-6 lg:px-10 flex items-center justify-between h-20">
        <a href="{{ route('home') }}" aria-label="Eclipz Media & Marketing - Home" class="inline-flex items-center py-2">
            {{-- White logo over the dark hero, black logo everywhere else. --}}
            <x-brand-logo variant="white" x-show="onDark" x-cloak class="h-8 md:h-10 w-auto" alt="" />
            <x-brand-logo variant="black" x-show="!onDark" class="h-8 md:h-10 w-auto" alt="Eclipz Media & Marketing" />
        </a>

        <nav aria-label="Primary" class="hidden md:flex items-center gap-10 font-body text-sm">
            <a href="{{ route('home') }}" class="hover:text-electric-blue transition-colors {{ request()->routeIs('home') ? 'text-electric-blue' : '' }}">Home</a>
            <a href="{{ route('work.index') }}" class="hover:text-electric-blue transition-colors {{ request()->routeIs('work.*') ? 'text-electric-blue' : '' }}">Work</a>
            <a href="{{ route('services') }}" class="hover:text-electric-blue transition-colors {{ request()->routeIs('services') ? 'text-electric-blue' : '' }}">Services</a>
            <a href="{{ route('about') }}" class="hover:text-electric-blue transition-colors {{ request()->routeIs('about') ? 'text-electric-blue' : '' }}">About</a>
            <a href="{{ route('clients') }}" class="hover:text-electric-blue transition-colors {{ request()->routeIs('clients') ? 'text-electric-blue' : '' }}">Clients</a>
            <a href="{{ route('blog.index') }}" class="hover:text-electric-blue transition-colors {{ request()->routeIs('blog.*') ? 'text-electric-blue' : '' }}">Blog</a>
        </nav>

        <a
            href="{{ route('contact') }}"
            :class="ctaClass"
            class="hidden md:inline-flex items-center gap-2 border px-5 py-2.5 text-sm font-body transition-colors"
        >
            Start a Project
        </a>

        <button
            @click="open = !open"
            :aria-expanded="open.toString()"
            aria-controls="mobile-nav"
            aria-label="Toggle menu"
            class="md:hidden"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                <path x-show="open" x-cloak stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav
        id="mobile-nav"
        aria-label="Mobile"
        x-show="open"
        x-cloak
        x-transition
        class="md:hidden bg-white text-jet border-t border-jet/10 px-6 py-6 flex flex-col gap-5 font-body text-base"
    >
        <a href="{{ route('home') }}" @click="open = false">Home</a>
        <a href="{{ route('work.index') }}" @click="open = false">Work</a>
        <a href="{{ route('services') }}" @click="open = false">Services</a>
        <a href="{{ route('about') }}" @click="open = false">About</a>
        <a href="{{ route('clients') }}" @click="open = false">Clients</a>
        <a href="{{ route('blog.index') }}" @click="open = false">Blog</a>
        <a href="{{ route('contact') }}" @click="open = false" class="font-medium text-electric-blue">Start a Project</a>
    </nav>
</header>