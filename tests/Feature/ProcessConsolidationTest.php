<?php

namespace Tests\Feature;

use App\Models\ProcessStep;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcessConsolidationTest extends TestCase
{
    use RefreshDatabase;

    private function step(array $attributes = []): ProcessStep
    {
        return ProcessStep::create(array_merge([
            'title' => 'Discovery',
            'description' => 'We learn about your brand, audience and goals.',
            'sort_order' => 1,
            'is_visible' => true,
        ], $attributes));
    }

    public function test_the_old_process_url_permanently_redirects_to_the_services_steps(): void
    {
        $this->get('/process')
            ->assertStatus(301)
            ->assertRedirect(url('/services').'#how-we-work');
    }

    public function test_services_page_lists_only_visible_steps_in_the_how_we_work_section(): void
    {
        $this->step();
        $this->step(['title' => 'Hidden Step', 'description' => 'Not for visitors.', 'sort_order' => 2, 'is_visible' => false]);

        $this->get('/services')
            ->assertOk()
            ->assertSee('id="how-we-work"', false)
            ->assertSee('Discovery')
            ->assertSee('We learn about your brand, audience and goals.')
            ->assertDontSee('Hidden Step');
    }

    public function test_services_page_hides_the_section_when_there_are_no_steps(): void
    {
        $this->get('/services')
            ->assertOk()
            ->assertDontSee('id="how-we-work"', false);
    }

    public function test_homepage_shows_a_short_overview_without_repeating_the_long_descriptions(): void
    {
        $this->step();

        $this->get('/')
            ->assertOk()
            ->assertSee('How We Work')
            ->assertSee('Discovery')
            ->assertSee('#how-we-work', false)
            ->assertDontSee('We learn about your brand, audience and goals.');
    }

    public function test_homepage_hides_the_overview_when_there_are_no_steps(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('How We Work')
            ->assertDontSee('See the full process');
    }

    public function test_navigation_and_footer_no_longer_link_to_the_old_process_page(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString(route('process'), $html);
        $this->assertDoesNotMatchRegularExpression('/>\s*Process\s*</', $html);
    }

    public function test_sitemap_no_longer_lists_the_old_process_page(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertDontSee('/process', false);
    }
}