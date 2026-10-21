<?php

namespace Tests\Feature;

use App\Models\ClientPartner;
use App\Models\Inquiry;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminHelpTest extends TestCase
{
    use RefreshDatabase;

    private function signIn(): void
    {
        $this->actingAs(User::create([
            'name' => 'Help Admin',
            'email' => 'help-admin@example.test',
            'password' => 'not-a-real-password',
            'role' => 'admin',
            'is_active' => true,
        ]));
    }

    /** The leading run of plain letters, safe to look for inside an HTML attribute. */
    private function probe(string $text): string
    {
        $probe = explode('|', preg_replace('/[^A-Za-z0-9 ]/', '|', $text))[0];

        $this->assertGreaterThan(12, strlen($probe), 'Start this help text with 13 or more plain characters: '.$text);

        return $probe;
    }

    private function fieldNames(string $html): array
    {
        preg_match_all('/(?:wire:model(?:\.[a-z.]+)?="data\.|entangle\((?:\'|&#039;)data\.)([A-Za-z0-9_]+)/', $html, $matches);

        return array_values(array_unique($matches[1]));
    }

    public function test_every_registered_page_shows_its_intro_line_and_info_icon(): void
    {
        $this->signIn();

        foreach (config('admin-help.pages') as $key => $help) {
            if (str_ends_with($key, '.edit')) {
                continue; // needs a record: see the next test
            }

            // A ".*" entry is checked through the list page of its resource.
            $routeKey = str_ends_with($key, '.*') ? substr($key, 0, -1).'index' : $key;

            $this->get(route('filament.admin.'.$routeKey))
                ->assertOk()
                ->assertSee(e($help['short']), false)
                ->assertSee($this->probe($help['full']), false);
        }
    }

    public function test_edit_pages_show_their_intro(): void
    {
        $this->signIn();

        $post = Post::create(['title' => 'Help Post', 'slug' => 'help-post', 'body' => '<p>x</p>']);
        $inquiry = Inquiry::create([
            'name' => 'Ada Visitor',
            'email' => 'ada@example.test',
            'service_requested' => 'Social Media',
            'message' => 'We would like help planning our launch content.',
        ]);

        foreach ([
            ['resources.blog-posts.edit', route('filament.admin.resources.blog-posts.edit', $post)],
            ['resources.inquiries.edit', route('filament.admin.resources.inquiries.edit', $inquiry)],
        ] as [$key, $url]) {
            $help = config('admin-help.pages')[$key];

            $this->get($url)
                ->assertOk()
                ->assertSee(e($help['short']), false)
                ->assertSee($this->probe($help['full']), false);
        }
    }

    public function test_field_help_appears_on_the_blog_post_and_client_forms(): void
    {
        $this->signIn();

        foreach ([
            'filament.admin.resources.blog-posts.create' => 'PostResource',
            'filament.admin.resources.client-partners.create' => 'ClientPartnerResource',
        ] as $route => $scope) {
            $html = $this->get(route($route))->assertOk()->getContent();

            foreach (config("admin-help.fields.{$scope}") as $name => $help) {
                $this->assertStringContainsString($this->probe($help['full']), $html, "{$scope}.{$name} shows no info-icon text");
            }
        }
    }

    public function test_every_field_on_the_blog_post_and_client_forms_has_help(): void
    {
        $this->signIn();

        foreach ([
            'filament.admin.resources.blog-posts.create' => 'PostResource',
            'filament.admin.resources.client-partners.create' => 'ClientPartnerResource',
        ] as $route => $scope) {
            $names = $this->fieldNames($this->get(route($route))->assertOk()->getContent());

            $this->assertNotEmpty($names, "No form fields found on {$route}");

            $missing = array_values(array_diff($names, array_keys(config("admin-help.fields.{$scope}"))));

            $this->assertSame([], $missing, "These fields have no help text in config/admin-help.php: ".implode(', ', $missing));
        }
    }

    public function test_button_tooltips_come_from_the_help_file(): void
    {
        $this->signIn();

        Post::create(['title' => 'Tooltip Post', 'slug' => 'tooltip-post', 'body' => '<p>x</p>']);

        $this->get('/admin/blog-posts')
            ->assertOk()
            ->assertSee($this->probe(config('admin-help.actions.edit')), false)
            ->assertSee($this->probe(config('admin-help.actions.delete')), false);
    }

    public function test_client_list_shows_column_tooltips(): void
    {
        $this->signIn();

        ClientPartner::unguarded(fn () => ClientPartner::create(['name' => 'Tooltip Client', 'type' => 'client']));

        $this->get('/admin/client-partners')
            ->assertOk()
            ->assertSee($this->probe(config('admin-help.columns.is_visible')), false);
    }
}