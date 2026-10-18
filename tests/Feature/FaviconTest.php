<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaviconTest extends TestCase
{
    use RefreshDatabase;

    private const ICON = 'images/eclipz-icon.svg';

    private const PUBLIC_PAGES = ['/', '/work', '/services', '/about', '/process', '/clients', '/contact'];

    /** Every <link rel="icon"> (or "shortcut icon") tag in a page's HTML. */
    private function iconLinks(string $html): array
    {
        preg_match_all('/<link\b[^>]*>/i', $html, $tags);

        return array_values(array_filter(
            $tags[0],
            fn (string $tag) => (bool) preg_match('/\brel=["\'](?:shortcut\s+)?icon["\']/i', $tag)
        ));
    }

    private function href(string $tag): string
    {
        preg_match('/\bhref=["\']([^"\']+)["\']/i', $tag, $match);

        return html_entity_decode($match[1] ?? '');
    }

    private function signInAsAdmin(): void
    {
        $this->actingAs(User::create([
            'name' => 'Test Admin',
            'email' => 'favicon-admin@example.test',
            'password' => 'not-a-real-password',
            'role' => 'admin',
            'is_active' => true,
        ]));
    }

    public function test_every_public_page_declares_exactly_one_favicon_and_it_is_the_icon(): void
    {
        $this->assertFileExists(public_path(self::ICON));

        foreach (self::PUBLIC_PAGES as $path) {
            $links = $this->iconLinks($this->get($path)->assertOk()->getContent());

            $this->assertCount(1, $links, "Expected exactly one favicon link on {$path}");

            $href = $this->href($links[0]);
            $this->assertStringContainsString(self::ICON, $href, "Wrong favicon on {$path}");
            $this->assertStringNotContainsString('eclipz-logo.svg', $href, "Old logo favicon on {$path}");
        }
    }

    public function test_the_public_favicon_url_carries_a_cache_busting_version(): void
    {
        $links = $this->iconLinks($this->get('/')->getContent());

        $this->assertMatchesRegularExpression('/eclipz-icon\.svg\?v=\d+$/', $this->href($links[0]));
    }

    public function test_admin_pages_declare_exactly_one_favicon_and_it_is_the_icon(): void
    {
        $login = $this->iconLinks($this->get('/admin/login')->assertOk()->getContent());
        $this->assertCount(1, $login);
        $this->assertStringContainsString(self::ICON, $this->href($login[0]));

        $this->signInAsAdmin();

        $inquiries = $this->iconLinks($this->get('/admin/inquiries')->assertOk()->getContent());
        $this->assertCount(1, $inquiries);
        $this->assertStringContainsString(self::ICON, $this->href($inquiries[0]));
    }

    public function test_public_and_admin_use_the_same_icon_file(): void
    {
        $public = parse_url($this->href($this->iconLinks($this->get('/')->getContent())[0]), PHP_URL_PATH);
        $admin = parse_url($this->href($this->iconLinks($this->get('/admin/login')->getContent())[0]), PHP_URL_PATH);

        $this->assertSame('/'.self::ICON, $admin);
        $this->assertSame($admin, $public);
    }
}