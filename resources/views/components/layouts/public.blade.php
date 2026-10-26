@props(['title' => null, 'metaDescription' => null, 'image' => null, 'noindex' => false, 'ogType' => 'website', 'canonical' => null])
@php
    $seo = \App\Models\SeoSetting::current();
    $pageTitle = $title ?? $seo->default_meta_title ?? 'Eclipz Media & Marketing Limited';
    $pageDescription = $metaDescription ?? $seo->default_meta_description ?? 'Eclipz Media & Marketing Limited - Crafting Timeless Visual Stories. Creative media, digital marketing, and brand strategy from Lagos, Nigeria.';
    $ogImagePath = $image ?? $seo->default_og_image;
    // Canonical links and share tags always use the public domain (config/seo.php).
    $ogImageUrl = $ogImagePath ? \App\Support\Seo::assetUrl(\Illuminate\Support\Facades\Storage::url($ogImagePath)) : null;
    $canonicalUrl = \App\Support\Seo::canonicalize($canonical ?? url()->current());

    // Favicon: the same icon-only Eclipz mark the admin panel uses (public/images/eclipz-icon.svg).
    // The ?v= value is the file's last-modified time, so browsers fetch a fresh copy
    // whenever the file changes, without keeping a second copy of the asset.
    $faviconFile = public_path('images/eclipz-icon.svg');
    $faviconUrl = asset('images/eclipz-icon.svg').(file_exists($faviconFile) ? '?v='.filemtime($faviconFile) : '');
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Lets the CSS hide scroll-reveal content only while JavaScript is running. If the
         page script has not started within 4 seconds, everything is shown again. --}}
    <script>
        document.documentElement.classList.add('js');
        setTimeout(function () {
            if (!window.__eclipzReady) { document.documentElement.classList.remove('js'); }
        }, 4000);
    </script>

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    @if($noindex)
        <meta name="robots" content="noindex, nofollow">
    @endif
    <link rel="canonical" href="{{ $canonicalUrl }}">

    <link rel="icon" type="image/svg+xml" href="{{ $faviconUrl }}">

    <meta property="og:site_name" content="Eclipz Media &amp; Marketing">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    @if($ogImageUrl)
        <meta property="og:image" content="{{ $ogImageUrl }}">
    @endif
    <meta name="twitter:card" content="{{ $ogImageUrl ? 'summary_large_image' : 'summary' }}">

    {{-- Bebas Neue (display) and Archivo (body and UI, standing in for Akzidenz-Grotesk). --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700&family=Bebas+Neue&display=swap">

    {{ $head ?? '' }}

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-jet font-body antialiased">

    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[100] focus:bg-white focus:text-jet focus:px-4 focus:py-2 focus:outline focus:outline-2 focus:outline-electric-blue">
        Skip to content
    </a>

    @include('layouts.partials.nav')

    <main id="main-content">
        {{ $slot }}
    </main>

    @include('layouts.partials.footer')

</body>
</html>