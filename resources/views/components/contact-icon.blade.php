@props(['name'])
@php
    // Inlines one of the icon files in resources/svg/contact so it follows the text colour.
    // The files are repository assets, never user input.
    $iconFile = resource_path('svg/contact/'.preg_replace('/[^a-z-]/', '', (string) $name).'.svg');
    $iconSvg = is_file($iconFile) ? file_get_contents($iconFile) : null;

    if ($iconSvg) {
        // Drop the size and class attributes the file ships with, then add ours.
        $iconSvg = preg_replace('/\s(?:class|width|height)="[^"]*"/', '', $iconSvg);
        $iconSvg = preg_replace('/<svg\b/', '<svg aria-hidden="true" focusable="false" '.$attributes->toHtml(), $iconSvg, 1);
    }
@endphp
{!! $iconSvg !!}