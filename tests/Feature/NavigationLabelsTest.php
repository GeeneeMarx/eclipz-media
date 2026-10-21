<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationLabelsTest extends TestCase
{
    use RefreshDatabase;

    private function assertLinkTexts(string $area, array $labels, string $where): void
    {
        foreach ($labels as $label) {
            $this->assertMatchesRegularExpression(
                '/>\s*'.preg_quote($label, '/').'\s*<\/a>/',
                $area,
                "The {$where} is missing a link reading \"{$label}\""
            );
        }
    }

    public function test_header_link_labels_are_spelled_correctly(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        preg_match('/<header.*?<\/header>/s', $html, $header);

        $this->assertNotEmpty($header);
        $this->assertLinkTexts($header[0], ['Home', 'Work', 'Services', 'About', 'Clients', 'Blog'], 'header');
    }

    public function test_footer_link_labels_are_spelled_correctly(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        preg_match('/<footer.*<\/footer>/s', $html, $footer);

        $this->assertNotEmpty($footer);
        $this->assertLinkTexts($footer[0], ['Work', 'Services', 'About', 'Clients', 'Blog', 'Contact Us'], 'footer');
        $this->assertStringContainsString(route('blog.index'), $footer[0]);
    }

    public function test_contact_page_uses_the_enquiry_call_to_action(): void
    {
        $this->get('/contact')
            ->assertOk()
            ->assertSee('<title>Contact Us | Eclipz Media &amp; Marketing Limited</title>', false)
            ->assertSee('Send an Enquiry');
    }
}