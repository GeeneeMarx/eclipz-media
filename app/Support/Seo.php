<?php

namespace App\Support;

use Carbon\CarbonInterface;

/**
 * Decides which web address search engines should treat as "the site", and builds the sitemap
 * XML. The address comes from config/seo.php, never from the incoming request, so a visit
 * through www., an IP address or a temporary host name cannot leak into the sitemap, robots.txt
 * or canonical links.
 */
class Seo
{
    public static function productionUrl(): string
    {
        // The fallback keeps the sitemap correct even if a cached configuration predates config/seo.php.
        return rtrim((string) (config('seo.production_url') ?: 'https://eclipzmedia.com'), '/');
    }

    /** The public address, without a trailing slash, used for every canonical URL. */
    public static function baseUrl(): string
    {
        $configured = trim((string) config('seo.canonical_url'));

        if ($configured === '') {
            $configured = app()->environment('local', 'testing')
                ? (string) config('app.url')
                : static::productionUrl();
        }

        $configured = rtrim($configured, '/');

        // A local address is never published while the site is served from a real domain.
        $configuredIsLocal = static::isLocalHost((string) parse_url($configured, PHP_URL_HOST));

        if ($configured === '' || ($configuredIsLocal && ! static::isLocalHost(request()->getHost()))) {
            return static::productionUrl();
        }

        return $configured;
    }

    /** An address on the canonical site, from a path such as "/work". */
    public static function url(string $path = '/'): string
    {
        return static::baseUrl().'/'.ltrim($path, '/');
    }

    /** The canonical address of a named route. */
    public static function route(string $name, array $parameters = []): string
    {
        return static::url(route($name, $parameters, false));
    }

    /** Moves a page address to the canonical domain, keeping its path and query. */
    public static function canonicalize(?string $url): string
    {
        $parts = parse_url((string) $url);

        if (! is_array($parts)) {
            return static::url('/');
        }

        $query = isset($parts['query']) && $parts['query'] !== '' ? '?'.$parts['query'] : '';

        return static::url($parts['path'] ?? '/').$query;
    }

    /**
     * Like canonicalize(), but for files such as share images: relative addresses and
     * addresses on this same site move to the canonical domain, and anything hosted
     * elsewhere is left exactly as it is.
     */
    public static function assetUrl(string $url): string
    {
        $parts = parse_url($url);

        if (! is_array($parts)) {
            return $url;
        }

        $host = strtolower((string) ($parts['host'] ?? ''));

        if ($host === '' || $host === strtolower(request()->getHost()) || static::isLocalHost($host)) {
            return static::canonicalize($url);
        }

        return $url;
    }

    public static function isLocalHost(string $host): bool
    {
        $host = strtolower(trim($host, '[]'));

        if ($host === '') {
            return false;
        }

        if (in_array($host, ['localhost', '::1', '0.0.0.0'], true) || str_starts_with($host, '127.')) {
            return true;
        }

        foreach (['.test', '.local', '.localhost'] as $suffix) {
            if (str_ends_with($host, $suffix)) {
                return true;
            }
        }

        return false;
    }

    /** A sitemap date in the W3C format, always in UTC, for example 2026-10-21T14:03:00+00:00. */
    public static function lastmod(?CarbonInterface $date): ?string
    {
        return $date?->clone()->setTimezone('UTC')->format('Y-m-d\TH:i:sP');
    }

    /**
     * @param  array<int, array{loc: string, lastmod?: string|null}>  $entries
     */
    public static function sitemapXml(array $entries): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($entries as $entry) {
            $xml .= '  <url><loc>'.static::escape($entry['loc']).'</loc>';

            if (! empty($entry['lastmod'])) {
                $xml .= '<lastmod>'.static::escape($entry['lastmod']).'</lastmod>';
            }

            $xml .= '</url>'."\n";
        }

        return $xml.'</urlset>'."\n";
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}