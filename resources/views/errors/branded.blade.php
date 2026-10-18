<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>@yield('title') | Eclipz Media &amp; Marketing</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500&family=Bebas+Neue&display=swap">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; background: #0A0A0A; color: #FFFFFF; font-family: Archivo, Arial, Helvetica, sans-serif; }
        main { width: 100%; max-width: 640px; margin: 0 auto; padding: 48px 24px; }
        .code { margin: 0; font-family: 'Bebas Neue', Impact, 'Arial Narrow', sans-serif; font-size: clamp(5rem, 22vw, 11rem); line-height: 0.9; letter-spacing: 0.02em; color: #D4A017; }
        h1 { margin: 12px 0 16px; font-family: 'Bebas Neue', Impact, 'Arial Narrow', sans-serif; font-weight: normal; font-size: clamp(2rem, 6vw, 3rem); letter-spacing: 0.03em; }
        .message { margin: 0 0 32px; color: rgba(255, 255, 255, 0.78); line-height: 1.6; }
        .button { display: inline-block; padding: 14px 26px; border: 1px solid #FFFFFF; color: #FFFFFF; text-decoration: none; }
        .button:hover, .button:focus-visible { background: #FFFFFF; color: #0A0A0A; }
        .button:focus-visible { outline: 2px solid #007BFF; outline-offset: 3px; }
    </style>
</head>
<body>
    <main>
        <p class="code" aria-hidden="true">@yield('code')</p>
        <h1>@yield('heading')</h1>
        <p class="message">@yield('message')</p>
        <a class="button" href="{{ url('/') }}">Back to home</a>
    </main>
</body>
</html>