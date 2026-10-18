<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /** Public pages plus every published project. Drafts are never listed. */
    public function sitemap(): Response
    {
        $urls = [
            ['loc' => route('home'), 'priority' => '1.0'],
            ['loc' => route('work.index'), 'priority' => '0.9'],
            ['loc' => route('services'), 'priority' => '0.8'],
            ['loc' => route('about'), 'priority' => '0.7'],
            ['loc' => route('process'), 'priority' => '0.6'],
            ['loc' => route('clients'), 'priority' => '0.6'],
            ['loc' => route('contact'), 'priority' => '0.7'],
        ];

        Project::query()
            ->where('is_published', true)
            ->orderBy('id')
            ->get(['slug', 'updated_at'])
            ->each(function (Project $project) use (&$urls) {
                $urls[] = [
                    'loc' => route('work.show', ['project' => $project->slug]),
                    'lastmod' => $project->updated_at?->toAtomString(),
                    'priority' => '0.7',
                ];
            });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= '  <url><loc>'.e($url['loc']).'</loc>';

            if (! empty($url['lastmod'])) {
                $xml .= '<lastmod>'.e($url['lastmod']).'</lastmod>';
            }

            $xml .= '<priority>'.$url['priority'].'</priority></url>'."\n";
        }

        $xml .= '</urlset>'."\n";

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /livewire',
            'Disallow: /contact/received',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}