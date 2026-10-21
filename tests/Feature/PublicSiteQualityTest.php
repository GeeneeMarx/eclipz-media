<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteQualityTest extends TestCase
{
    use RefreshDatabase;

    private function makeProject(array $attributes): Project
    {
        return Project::unguarded(fn () => Project::create(array_merge([
            'is_published' => true,
            'published_at' => now()->subDay(),
        ], $attributes)));
    }

    public function test_sitemap_lists_public_pages_and_only_published_projects(): void
    {
        $this->makeProject(['name' => 'Live Project', 'slug' => 'live-project']);
        $this->makeProject(['name' => 'Draft Project', 'slug' => 'draft-project', 'is_published' => false]);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee(route('home'), false)
            ->assertSee(route('work.index'), false)
            ->assertSee(route('contact'), false)
            ->assertSee('live-project', false)
            ->assertDontSee('draft-project', false);
    }

    public function test_robots_blocks_the_admin_and_points_to_the_sitemap(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /admin', false)
            ->assertSee('Sitemap: '.route('sitemap'), false);
    }

    public function test_scroll_reveal_content_is_hidden_only_while_javascript_runs(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee("classList.add('js')", false)
            ->assertSee('og:site_name', false);

        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertStringContainsString('.js [data-reveal]', $css);
        $this->assertDoesNotMatchRegularExpression('/^\[data-reveal\]\s*\{/m', $css);
    }

    public function test_fonts_load_from_the_page_head_instead_of_a_css_import(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('rel="preconnect" href="https://fonts.googleapis.com"', false)
            ->assertSee('fonts.googleapis.com/css2', false);

        $this->assertStringNotContainsString('@import', file_get_contents(resource_path('css/app.css')));
    }

    public function test_unknown_pages_show_the_branded_404(): void
    {
        $this->get('/there-is-no-such-page')
            ->assertNotFound()
            ->assertSee('Page not found');
    }

    public function test_forbidden_pages_show_the_branded_403(): void
    {
        $this->actingAs(User::create([
            'name' => 'Test Editor',
            'email' => 'editor-403@example.test',
            'password' => 'not-a-real-password',
            'role' => 'editor',
            'is_active' => true,
        ]));

        $this->get('/admin/inquiries')
            ->assertForbidden()
            ->assertSee('Access denied');
    }

    public function test_work_filters_ignore_malformed_values_instead_of_failing(): void
    {
        $this->get('/work?industry[]=1&service=abc&type=nonsense')->assertOk();
        $this->get('/work?industry=99999999999999999999')->assertOk();
    }

    public function test_work_type_filter_narrows_the_list(): void
    {
        $this->makeProject(['name' => 'Visual Piece One', 'slug' => 'visual-piece-one', 'project_type' => 'visual']);
        $this->makeProject(['name' => 'Case Study Piece One', 'slug' => 'case-study-piece-one', 'project_type' => 'case_study']);

        $this->get('/work?type=case_study')
            ->assertOk()
            ->assertSee('Case Study Piece One')
            ->assertDontSee('Visual Piece One');
    }

    public function test_draft_projects_are_not_reachable(): void
    {
        $this->makeProject(['name' => 'Hidden Draft', 'slug' => 'hidden-draft', 'is_published' => false]);

        $this->get('/work/hidden-draft')->assertNotFound();
    }

    public function test_footer_links_to_every_public_section(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        preg_match('/<footer.*<\/footer>/s', $html, $footer);

        $this->assertNotEmpty($footer);

        foreach (['work.index', 'services', 'about', 'clients', 'contact'] as $name) {
            $this->assertStringContainsString(route($name), $footer[0], "Footer is missing the {$name} link");
        }
    }

    public function test_confirmation_page_is_not_indexable(): void
    {
        $this->withSession(['inquiry_reference' => 'EZ-2026-ABC234'])
            ->get(route('contact.received'))
            ->assertOk()
            ->assertSee('name="robots" content="noindex, nofollow"', false);
    }
}