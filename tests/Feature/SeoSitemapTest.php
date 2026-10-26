<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Project;
use App\Support\Seo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use SimpleXMLElement;
use Tests\TestCase;

class SeoSitemapTest extends TestCase
{
    use RefreshDatabase;

    private const SITE = 'https://eclipzmedia.com';

    private const NS = 'http://www.sitemaps.org/schemas/sitemap/0.9';

    protected function setUp(): void
    {
        parent::setUp();

        config(['seo.canonical_url' => self::SITE]);
    }

    private function makeProject(string $slug, array $overrides = []): Project
    {
        return Project::unguarded(fn () => Project::create(array_merge([
            'name' => 'Project '.$slug,
            'slug' => $slug,
            'is_published' => true,
            'published_at' => now()->subDay(),
        ], $overrides)));
    }

    private function makePost(string $slug, array $overrides = []): Post
    {
        return Post::create(array_merge([
            'title' => 'Post '.$slug,
            'slug' => $slug,
            'body' => '<p>Body</p>',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ], $overrides));
    }

    private function parse(string $content): SimpleXMLElement
    {
        $xml = simplexml_load_string($content);

        $this->assertInstanceOf(SimpleXMLElement::class, $xml, 'The sitemap is not valid XML');
        $xml->registerXPathNamespace('s', self::NS);

        return $xml;
    }

    /** @return array<int, string> */
    private function locs(string $url = '/sitemap.xml'): array
    {
        $response = $this->get($url)->assertOk();

        return array_map('strval', $this->parse($response->getContent())->xpath('//s:url/s:loc') ?: []);
    }

    public function test_sitemap_is_valid_xml_served_as_application_xml(): void
    {
        $response = $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $xml = $this->parse($response->getContent());

        $this->assertSame('urlset', $xml->getName());
        $this->assertSame(self::NS, $xml->getNamespaces()[''] ?? null);
        $this->assertStringStartsWith('<?xml version="1.0" encoding="UTF-8"?>', $response->getContent());
    }

    public function test_sitemap_lists_exactly_the_public_pages_when_there_is_no_content(): void
    {
        $expected = [
            self::SITE.'/',
            self::SITE.'/work',
            self::SITE.'/services',
            self::SITE.'/about',
            self::SITE.'/clients',
            self::SITE.'/blog',
            self::SITE.'/contact',
        ];

        $locs = $this->locs();

        sort($expected);
        sort($locs);

        $this->assertSame($expected, $locs);
    }

    public function test_sitemap_never_lists_private_redirecting_or_filtered_addresses(): void
    {
        $this->makeProject('some-project');
        $this->makePost('some-post');

        $locs = $this->locs();

        $this->assertSame($locs, array_values(array_unique($locs)), 'The sitemap lists an address twice');

        foreach ($locs as $loc) {
            $path = (string) parse_url($loc, PHP_URL_PATH);

            $this->assertNotContains($path, ['/contact/received', '/process', '/sitemap.xml', '/robots.txt', '/up']);
            $this->assertStringStartsNotWith('/admin', $path);
            $this->assertStringStartsNotWith('/livewire', $path);
            $this->assertNull(parse_url($loc, PHP_URL_QUERY), "{$loc} carries a query string");
            $this->assertNull(parse_url($loc, PHP_URL_FRAGMENT), "{$loc} carries a fragment");
        }
    }

    public function test_only_published_projects_and_live_posts_are_listed_with_valid_lastmod_dates(): void
    {
        $this->makeProject('live-project');
        $this->makeProject('draft-project', ['is_published' => false]);
        $this->makePost('live-post');
        $this->makePost('draft-post', ['is_published' => false, 'published_at' => null]);
        $this->makePost('scheduled-post', ['published_at' => now()->addDay()]);
        $this->makePost('switched-off-post', ['is_published' => false]);

        $response = $this->get('/sitemap.xml')->assertOk();
        $xml = $this->parse($response->getContent());
        $locs = array_map('strval', $xml->xpath('//s:url/s:loc'));

        $this->assertContains(self::SITE.'/work/live-project', $locs);
        $this->assertContains(self::SITE.'/blog/live-post', $locs);

        foreach (['draft-project', 'draft-post', 'scheduled-post', 'switched-off-post'] as $hidden) {
            $this->assertStringNotContainsString($hidden, $response->getContent());
        }

        foreach (['/work/live-project', '/blog/live-post'] as $path) {
            $dates = $xml->xpath("//s:url[s:loc='".self::SITE.$path."']/s:lastmod");

            $this->assertCount(1, $dates, "{$path} has no lastmod");
            $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+00:00$/', (string) $dates[0]);
        }

        // The fixed pages carry no invented dates.
        $this->assertCount(0, $xml->xpath("//s:url[s:loc='".self::SITE."/services']/s:lastmod"));
    }

    public function test_a_local_setting_never_reaches_the_production_sitemap(): void
    {
        config(['seo.canonical_url' => 'http://127.0.0.1:8000']);
        $this->makeProject('prod-project');

        $locs = $this->locs('https://eclipzmedia.com/sitemap.xml');

        $this->assertNotEmpty($locs);

        foreach ($locs as $loc) {
            $this->assertStringStartsWith(self::SITE.'/', $loc);
            $this->assertStringNotContainsString('127.0.0.1', $loc);
            $this->assertStringNotContainsString('localhost', $loc);
        }
    }

    public function test_a_local_setting_is_used_when_the_site_is_served_locally(): void
    {
        config(['seo.canonical_url' => 'http://127.0.0.1:8000']);

        $locs = $this->locs('http://127.0.0.1:8000/sitemap.xml');

        $this->assertNotEmpty($locs);

        foreach ($locs as $loc) {
            $this->assertStringStartsWith('http://127.0.0.1:8000/', $loc);
        }
    }

    public function test_special_characters_are_escaped_in_the_sitemap_xml(): void
    {
        $loc = 'https://eclipzmedia.com/search?a=1&b=2&c=<tag>"quoted"\'single\'';

        $xml = Seo::sitemapXml([['loc' => $loc, 'lastmod' => '2026-01-01T00:00:00+00:00']]);

        $this->assertStringNotContainsString('<tag>', $xml);
        $this->assertStringContainsString('&amp;', $xml);

        $locs = $this->parse($xml)->xpath('//s:loc');

        $this->assertSame($loc, (string) $locs[0]);
    }

    public function test_unusual_slugs_still_produce_valid_xml_and_correct_addresses(): void
    {
        $this->makeProject("a&b's-café");
        $this->makePost("o'neil & sons");

        $locs = collect($this->locs());

        $project = $locs->first(fn (string $loc) => str_contains($loc, '/work/'));
        $post = $locs->first(fn (string $loc) => str_contains($loc, '/blog/'));

        $this->assertSame("a&b's-café", rawurldecode(substr($project, strlen(self::SITE.'/work/'))));
        $this->assertSame("o'neil & sons", rawurldecode(substr($post, strlen(self::SITE.'/blog/'))));
    }

    public function test_robots_txt_is_served_dynamically_and_points_to_the_production_sitemap(): void
    {
        $response = $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8');

        $lines = preg_split('/\R/', trim($response->getContent()));

        $this->assertContains('User-agent: *', $lines);
        $this->assertContains('Disallow: /admin', $lines);
        $this->assertContains('Disallow: /contact/received', $lines);
        $this->assertContains('Sitemap: '.self::SITE.'/sitemap.xml', $lines);

        // Public pages and the files they need stay crawlable.
        foreach (['/', '/work', '/blog', '/services', '/about', '/clients', '/contact', '/storage', '/build', '/images', '/livewire'] as $path) {
            $this->assertNotContains("Disallow: {$path}", $lines, "robots.txt must not block {$path}");
        }
    }

    public function test_robots_txt_never_advertises_a_local_sitemap_on_a_real_domain(): void
    {
        config(['seo.canonical_url' => 'http://127.0.0.1:8000']);

        $this->get('https://eclipzmedia.com/robots.txt')
            ->assertOk()
            ->assertSee('Sitemap: https://eclipzmedia.com/sitemap.xml', false)
            ->assertDontSee('127.0.0.1', false);
    }

    public function test_no_static_robots_file_shadows_the_dynamic_route(): void
    {
        $this->assertFileDoesNotExist(
            public_path('robots.txt'),
            'public/robots.txt would be served instead of the Laravel route. After checking what it says, rename it to robots.txt.default.'
        );
    }

    public function test_pages_declare_a_canonical_address_on_the_production_domain(): void
    {
        $this->makePost('canonical-post');

        foreach ([
            ['/', self::SITE.'/'],
            ['/work?industry=1&type=case_study', self::SITE.'/work'],
            ['/services', self::SITE.'/services'],
            ['/blog/canonical-post', self::SITE.'/blog/canonical-post'],
        ] as [$path, $expected]) {
            $html = $this->get($path)->assertOk()->getContent();

            $this->assertStringContainsString('<link rel="canonical" href="'.$expected.'">', $html, "Wrong canonical on {$path}");
            $this->assertStringContainsString('<meta property="og:url" content="'.$expected.'">', $html, "Wrong og:url on {$path}");
        }
    }

    public function test_address_helpers_move_same_site_addresses_to_the_canonical_domain_only(): void
    {
        $this->assertSame(self::SITE.'/blog?category=News', Seo::canonicalize('http://127.0.0.1:8000/blog?category=News#top'));
        $this->assertSame(self::SITE.'/', Seo::canonicalize('http://localhost'));
        $this->assertSame(self::SITE.'/work', Seo::canonicalize('/work'));
        $this->assertSame(self::SITE.'/', Seo::route('home'));
        $this->assertSame(self::SITE.'/work/a-b', Seo::route('work.show', ['project' => 'a-b']));

        $this->assertSame(self::SITE.'/storage/blog/covers/x.jpg', Seo::assetUrl('/storage/blog/covers/x.jpg'));
        $this->assertSame(self::SITE.'/storage/x.jpg', Seo::assetUrl('http://127.0.0.1:8000/storage/x.jpg'));
        $this->assertSame('https://cdn.example.com/x.jpg', Seo::assetUrl('https://cdn.example.com/x.jpg'));
    }
}
