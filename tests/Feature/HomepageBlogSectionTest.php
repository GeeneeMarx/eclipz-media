<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageBlogSectionTest extends TestCase
{
    use RefreshDatabase;

    private function makePost(string $title, string $slug, array $overrides = []): Post
    {
        return Post::create(array_merge([
            'title' => $title,
            'slug' => $slug,
            'excerpt' => "Excerpt for {$title}",
            'body' => "<p>Body of {$title}</p>",
            'is_published' => true,
            'published_at' => now()->subDay(),
        ], $overrides));
    }

    private function section(): ?string
    {
        $html = $this->get('/')->assertOk()->getContent();

        preg_match('/<section id="latest-stories".*?<\/section>/s', $html, $match);

        return $match[0] ?? null;
    }

    public function test_homepage_shows_the_three_newest_live_posts_newest_first(): void
    {
        $this->makePost('Oldest Story', 'oldest-story', ['published_at' => now()->subDays(4)]);
        $this->makePost('Third Story', 'third-story', ['published_at' => now()->subDays(3)]);
        $this->makePost('Second Story', 'second-story', ['published_at' => now()->subDays(2)]);
        $this->makePost('Newest Story', 'newest-story', ['published_at' => now()->subDay()]);

        $section = $this->section();

        $this->assertNotNull($section);
        $this->assertStringContainsString('Newest Story', $section);
        $this->assertStringContainsString('Second Story', $section);
        $this->assertStringContainsString('Third Story', $section);
        $this->assertStringNotContainsString('Oldest Story', $section);

        $this->assertLessThan(strpos($section, 'Second Story'), strpos($section, 'Newest Story'));
        $this->assertLessThan(strpos($section, 'Third Story'), strpos($section, 'Second Story'));
    }

    public function test_homepage_never_shows_drafts_scheduled_or_switched_off_posts(): void
    {
        $this->makePost('Live Story', 'live-story');
        $this->makePost('Draft Story', 'draft-story', ['is_published' => false, 'published_at' => null]);
        $this->makePost('Scheduled Story', 'scheduled-story', ['published_at' => now()->addDay()]);
        $this->makePost('Switched Off Story', 'switched-off-story', ['is_published' => false]);

        $section = $this->section();

        $this->assertNotNull($section);
        $this->assertStringContainsString('Live Story', $section);

        foreach (['Draft Story', 'Scheduled Story', 'Switched Off Story'] as $hidden) {
            $this->assertStringNotContainsString($hidden, $section);
            $this->get('/')->assertDontSee($hidden);
        }
    }

    public function test_homepage_section_links_to_each_story_and_to_the_blog(): void
    {
        $this->makePost('Linked Story', 'linked-story');

        $section = $this->section();

        $this->assertNotNull($section);
        $this->assertStringContainsString('href="'.route('blog.show', 'linked-story').'"', $section);
        $this->assertStringContainsString('href="'.route('blog.index').'"', $section);
        $this->assertStringContainsString('View all stories', $section);
        $this->assertStringContainsString('Insights &amp; Updates', $section);
    }

    public function test_homepage_leaves_the_section_out_when_there_are_no_live_posts(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('id="latest-stories"', false)
            ->assertDontSee('Latest from Eclipz');

        $this->makePost('Only A Draft', 'only-a-draft', ['is_published' => false, 'published_at' => null]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('id="latest-stories"', false)
            ->assertDontSee('Only A Draft');
    }

    public function test_homepage_section_follows_publishing_changes(): void
    {
        $post = $this->makePost('Toggled Story', 'toggled-story');

        $this->assertNotNull($this->section());

        $post->update(['is_published' => false]);

        $this->assertNull($this->section());

        $post->update(['is_published' => true]);

        $this->assertNotNull($this->section());
    }
}