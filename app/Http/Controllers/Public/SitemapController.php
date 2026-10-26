<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Project;
use App\Support\Seo;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Every public, indexable page: the fixed pages, each published project and each live
     * blog post. Drafts, scheduled posts, redirects (such as /process), the thank-you page,
     * the admin and filtered or paged listings are never included.
     */
    public function sitemap(): Response
    {
        $entries = [];

        foreach (['home', 'work.index', 'services', 'about', 'clients', 'blog.index', 'contact'] as $name) {
            $entries[] = ['loc' => Seo::route($name)];
        }

        $projects = Project::query()
            ->where('is_published', true)
            ->orderBy('id')
            ->get(['slug', 'updated_at']);

        foreach ($projects as $project) {
            $entries[] = [
                'loc' => Seo::route('work.show', ['project' => $project->slug]),
                'lastmod' => Seo::lastmod($project->updated_at),
            ];
        }

        $posts = Post::query()
            ->live()
            ->orderBy('id')
            ->get(['slug', 'updated_at']);

        foreach ($posts as $post) {
            $entries[] = [
                'loc' => Seo::route('blog.show', ['post' => $post->slug]),
                'lastmod' => Seo::lastmod($post->updated_at),
            ];
        }

        return response(Seo::sitemapXml($entries), 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * Served by Laravel, so a static public/robots.txt must not exist: the web server would
     * hand out that file instead of this response.
     */
    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /contact/received',
            '',
            'Sitemap: '.Seo::route('sitemap'),
        ];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}