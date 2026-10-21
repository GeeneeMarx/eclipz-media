<?php

namespace Tests\Feature;

use App\Filament\Resources\PostResource\Pages\CreatePost;
use App\Models\AdminActivityLog;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    private function admin(string $role = 'admin', bool $active = true, string $email = 'blog-admin@example.test'): User
    {
        return User::create([
            'name' => 'Blog Admin',
            'email' => $email,
            'password' => 'not-a-real-password',
            'role' => $role,
            'is_active' => $active,
        ]);
    }

    private function makePost(array $overrides = []): Post
    {
        static $counter = 0;
        $counter++;

        return Post::create(array_merge([
            'title' => "Story Number {$counter}",
            'slug' => "story-number-{$counter}",
            'excerpt' => "Excerpt for story {$counter}",
            'body' => "<p>Body of story {$counter}</p>",
            'is_published' => true,
            'published_at' => now()->subDay(),
        ], $overrides));
    }

    public function test_index_lists_only_live_posts(): void
    {
        $this->makePost(['title' => 'Live Story', 'slug' => 'live-story']);
        $this->makePost(['title' => 'Draft Story', 'slug' => 'draft-story', 'is_published' => false, 'published_at' => null]);
        $this->makePost(['title' => 'Scheduled Story', 'slug' => 'scheduled-story', 'published_at' => now()->addDay()]);
        $this->makePost(['title' => 'Switched Off Story', 'slug' => 'switched-off-story', 'is_published' => false, 'published_at' => now()->subDay()]);

        $this->get('/blog')
            ->assertOk()
            ->assertSee('Live Story')
            ->assertDontSee('Draft Story')
            ->assertDontSee('Scheduled Story')
            ->assertDontSee('Switched Off Story');
    }

    public function test_index_shows_an_empty_state_when_nothing_is_published(): void
    {
        $this->makePost(['is_published' => false, 'published_at' => null]);

        $this->get('/blog')
            ->assertOk()
            ->assertSee('No stories published yet');
    }

    public function test_post_page_shows_the_article(): void
    {
        $author = $this->admin();
        $post = $this->makePost([
            'title' => 'A Full Article',
            'slug' => 'a-full-article',
            'category' => 'Company News',
            'body' => '<h2>Heading in the body</h2><p>Paragraph in the body</p>',
            'user_id' => $author->id,
            'cover_image' => 'blog/covers/example.jpg',
            'cover_alt' => 'A described cover',
            'published_at' => now()->subDays(2),
        ]);

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('A Full Article')
            ->assertSee('Company News')
            ->assertSee('Blog Admin')
            ->assertSee('Heading in the body')
            ->assertSee('Paragraph in the body')
            ->assertSee('alt="A described cover"', false)
            ->assertSee('min read');
    }

    public function test_post_page_has_article_metadata(): void
    {
        $post = $this->makePost(['title' => 'Metadata Story', 'slug' => 'metadata-story']);

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('property="og:type" content="article"', false)
            ->assertSee('<link rel="canonical" href="'.route('blog.show', $post->slug).'">', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('"@type":"BlogPosting"', false)
            ->assertSee('"headline":"Metadata Story"', false);
    }

    public function test_unpublished_scheduled_and_switched_off_posts_are_not_public(): void
    {
        $this->makePost(['slug' => 'hidden-draft', 'is_published' => false, 'published_at' => null]);
        $this->makePost(['slug' => 'hidden-scheduled', 'published_at' => now()->addDay()]);
        $this->makePost(['slug' => 'hidden-switched-off', 'is_published' => false, 'published_at' => now()->subDay()]);

        foreach (['hidden-draft', 'hidden-scheduled', 'hidden-switched-off'] as $slug) {
            $this->get(route('blog.show', $slug))->assertNotFound();
        }

        $this->get('/blog/does-not-exist')->assertNotFound();
    }

    public function test_admin_can_preview_a_draft_but_it_is_not_indexable_or_listed(): void
    {
        $this->makePost(['title' => 'Secret Draft', 'slug' => 'secret-draft', 'is_published' => false, 'published_at' => null]);

        $this->actingAs($this->admin());

        $this->get(route('blog.show', 'secret-draft'))
            ->assertOk()
            ->assertSee('Secret Draft')
            ->assertSee('Preview only')
            ->assertSee('name="robots" content="noindex, nofollow"', false)
            ->assertDontSee('application/ld+json', false);

        $this->get('/blog')->assertOk()->assertDontSee('Secret Draft');
    }

    public function test_only_active_admins_can_preview_drafts(): void
    {
        $this->makePost(['slug' => 'guarded-draft', 'is_published' => false, 'published_at' => null]);

        $this->get(route('blog.show', 'guarded-draft'))->assertNotFound();

        $this->actingAs($this->admin('editor', true, 'editor@example.test'));
        $this->get(route('blog.show', 'guarded-draft'))->assertNotFound();

        $this->actingAs($this->admin('admin', false, 'inactive@example.test'));
        $this->get(route('blog.show', 'guarded-draft'))->assertNotFound();
    }

    public function test_article_html_is_cleaned_before_display(): void
    {
        $post = $this->makePost([
            'slug' => 'unsafe-html',
            'body' => '<p>Safe paragraph</p><script>alert(1)</script><a href="javascript:alert(2)" onclick="alert(3)">bad link</a><img src="/storage/x.jpg" onerror="alert(4)"><a href="https://example.test/page">good link</a>',
        ]);

        $response = $this->get(route('blog.show', $post->slug))->assertOk();

        $response->assertSee('<p>Safe paragraph</p>', false);
        $response->assertSee('good link', false);

        foreach (['alert(1)', 'alert(2)', 'alert(3)', 'alert(4)', 'onclick', 'onerror', 'javascript:'] as $unsafe) {
            $response->assertDontSee($unsafe, false);
        }
    }

    public function test_index_paginates_and_sends_out_of_range_pages_to_the_last_page(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $this->makePost([
                'title' => sprintf('Paged Story %02d', $i),
                'slug' => sprintf('paged-story-%02d', $i),
                'published_at' => now()->subDays($i),
            ]);
        }

        $this->get('/blog')
            ->assertOk()
            ->assertSee('Paged Story 01')
            ->assertSee('Paged Story 09')
            ->assertDontSee('Paged Story 10')
            ->assertSee('page=2', false);

        $this->get('/blog?page=2')
            ->assertOk()
            ->assertSee('Paged Story 10')
            ->assertDontSee('Paged Story 01');

        $this->get('/blog?page=99')->assertRedirect();
    }

    public function test_category_filter_works_and_ignores_unknown_categories(): void
    {
        $this->makePost(['title' => 'News Item', 'slug' => 'news-item', 'category' => 'News']);
        $this->makePost(['title' => 'Insight Item', 'slug' => 'insight-item', 'category' => 'Insights']);

        $this->get('/blog')->assertOk()->assertSee('Filter stories by category', false);

        $this->get('/blog?category=News')
            ->assertOk()
            ->assertSee('News Item')
            ->assertDontSee('Insight Item');

        $this->get('/blog?category=Nothing')
            ->assertOk()
            ->assertSee('News Item')
            ->assertSee('Insight Item');

        $this->get('/blog?category[]=News')->assertOk();
    }

    public function test_category_chips_only_appear_when_there_is_something_to_choose(): void
    {
        $this->makePost(['category' => 'News']);

        $this->get('/blog')->assertOk()->assertDontSee('Filter stories by category', false);
    }

    public function test_sitemap_lists_only_live_posts(): void
    {
        $this->makePost(['slug' => 'sitemap-live']);
        $this->makePost(['slug' => 'sitemap-draft', 'is_published' => false, 'published_at' => null]);
        $this->makePost(['slug' => 'sitemap-scheduled', 'published_at' => now()->addDay()]);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee(route('blog.index'), false)
            ->assertSee('sitemap-live', false)
            ->assertDontSee('sitemap-draft', false)
            ->assertDontSee('sitemap-scheduled', false);
    }

    public function test_publishing_without_a_date_sets_one_and_future_dates_schedule(): void
    {
        $now = $this->makePost(['slug' => 'publish-now', 'published_at' => null]);

        $this->assertNotNull($now->fresh()->published_at);
        $this->assertTrue($now->fresh()->isLive());
        $this->assertSame('Published', $now->fresh()->statusLabel());

        $later = $this->makePost(['slug' => 'publish-later', 'published_at' => now()->addWeek()]);

        $this->assertFalse($later->fresh()->isLive());
        $this->assertSame('Scheduled', $later->fresh()->statusLabel());

        $draft = $this->makePost(['slug' => 'just-a-draft', 'is_published' => false, 'published_at' => null]);

        $this->assertNull($draft->fresh()->published_at);
        $this->assertSame('Draft', $draft->fresh()->statusLabel());
    }

    public function test_reading_time_is_estimated_from_the_text(): void
    {
        $short = $this->makePost(['body' => '<p>Just a few words here.</p>']);
        $long = $this->makePost(['body' => '<p>'.str_repeat('word ', 450).'</p>']);

        $this->assertSame(1, $short->readingMinutes());
        $this->assertSame(3, $long->readingMinutes());
    }

    public function test_deleting_a_post_removes_its_cover_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('blog/covers/to-delete.jpg', 'image-bytes');

        $post = $this->makePost(['cover_image' => 'blog/covers/to-delete.jpg']);

        Storage::disk('public')->assertExists('blog/covers/to-delete.jpg');

        $post->delete();

        Storage::disk('public')->assertMissing('blog/covers/to-delete.jpg');
    }

    public function test_post_changes_are_written_to_the_activity_log(): void
    {
        $post = $this->makePost(['title' => 'Logged Story', 'slug' => 'logged-story', 'is_published' => false, 'published_at' => null]);

        $post->update(['is_published' => true]);

        $actions = AdminActivityLog::where('entity_type', 'Post')->pluck('action')->all();

        $this->assertContains('created', $actions);
        $this->assertContains('published', $actions);
    }

    public function test_admin_can_create_a_draft_from_the_panel(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        Livewire::test(CreatePost::class)
            ->fillForm([
                'title' => 'Panel Draft',
                'slug' => 'panel-draft',
                'body' => '<p>Written in the panel.</p>',
                'user_id' => $admin->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('posts', [
            'slug' => 'panel-draft',
            'is_published' => false,
            'user_id' => $admin->id,
        ]);

        $this->assertFalse(Post::where('slug', 'panel-draft')->firstOrFail()->isLive());
        $this->get('/blog')->assertOk()->assertDontSee('Panel Draft');

        // Signed in as an admin, the draft opens as a preview. Visitors get a 404 (see the tests above).
        $this->get(route('blog.show', 'panel-draft'))->assertOk()->assertSee('Preview only');
    }

    public function test_admin_can_publish_from_the_panel(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        Livewire::test(CreatePost::class)
            ->fillForm([
                'title' => 'Panel Published',
                'slug' => 'panel-published',
                'body' => '<p>Live content.</p>',
                'user_id' => $admin->id,
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertNotNull(Post::where('slug', 'panel-published')->firstOrFail()->published_at);

        $this->get(route('blog.show', 'panel-published'))->assertOk()->assertSee('Panel Published');
    }

    public function test_panel_rejects_invalid_and_duplicate_slugs(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);
        $this->makePost(['slug' => 'taken-address']);

        Livewire::test(CreatePost::class)
            ->fillForm(['title' => 'Bad Slug', 'slug' => 'Not A Slug!', 'body' => '<p>x</p>', 'user_id' => $admin->id])
            ->call('create')
            ->assertHasFormErrors(['slug']);

        Livewire::test(CreatePost::class)
            ->fillForm(['title' => 'Duplicate', 'slug' => 'taken-address', 'body' => '<p>x</p>', 'user_id' => $admin->id])
            ->call('create')
            ->assertHasFormErrors(['slug']);
    }

    public function test_blog_management_requires_an_admin_login(): void
    {
        $this->get('/admin/blog-posts')->assertRedirect('/admin/login');
    }
}