<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Cleans HTML written in the admin's rich text editor before it is shown to visitors.
 * Scripts, event handlers (onclick and the like), iframes and javascript: links are
 * removed; ordinary formatting, links and images are kept.
 */
class SafeHtml
{
    private static ?HtmlSanitizer $sanitizer = null;

    public static function clean(?string $html): string
    {
        if (blank($html)) {
            return '';
        }

        self::$sanitizer ??= new HtmlSanitizer(
            (new HtmlSanitizerConfig())
                ->allowSafeElements()
                ->allowRelativeLinks()
                ->allowRelativeMedias()
                ->allowLinkSchemes(['https', 'http', 'mailto', 'tel'])
                ->allowMediaSchemes(['https', 'http'])
                ->forceAttribute('a', 'rel', 'noopener noreferrer')
                // The default limit (20,000 characters) would cut long articles short.
                ->withMaxInputLength(500000)
        );

        return self::$sanitizer->sanitize($html);
    }
}