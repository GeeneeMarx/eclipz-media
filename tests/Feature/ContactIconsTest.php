<?php

namespace Tests\Feature;

use App\Models\ContactSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactIconsTest extends TestCase
{
    use RefreshDatabase;

    private const ICONS = ['envelope', 'telephone', 'geo-alt', 'whatsapp', 'instagram', 'facebook', 'linkedin', 'tiktok'];

    private function fillContact(array $overrides = []): void
    {
        ContactSetting::current()->update(array_merge([
            'email' => 'hello@example.test',
            'phone' => '+234 801 234 5678',
            'whatsapp' => '+234 801 234 5678',
            'address' => 'Example Street, Lagos',
            'instagram' => 'https://instagram.com/eclipz-test',
            'facebook' => 'https://facebook.com/eclipz-test',
            'linkedin' => 'https://linkedin.com/company/eclipz-test',
            'tiktok' => 'https://tiktok.com/@eclipz-test',
        ], $overrides));
    }

    private function section(string $path, string $tag): string
    {
        $html = $this->get($path)->assertOk()->getContent();

        preg_match('/<'.$tag.'\b.*<\/'.$tag.'>/s', $html, $match);

        $this->assertNotEmpty($match, "No <{$tag}> found on {$path}");

        return $match[0];
    }

    public function test_every_icon_file_exists_and_follows_the_text_colour(): void
    {
        foreach (self::ICONS as $icon) {
            $file = resource_path("svg/contact/{$icon}.svg");

            $this->assertFileExists($file);
            $this->assertStringContainsString('currentColor', file_get_contents($file), "{$icon}.svg should use currentColor");
        }
    }

    public function test_footer_shows_a_link_for_every_configured_contact_method(): void
    {
        $this->fillContact();

        $footer = $this->section('/', 'footer');

        foreach ([
            'href="mailto:hello@example.test"',
            'href="tel:+2348012345678"',
            'href="https://wa.me/2348012345678"',
            'href="https://instagram.com/eclipz-test"',
            'href="https://facebook.com/eclipz-test"',
            'href="https://linkedin.com/company/eclipz-test"',
            'href="https://tiktok.com/@eclipz-test"',
            'Example Street, Lagos',
        ] as $expected) {
            $this->assertStringContainsString($expected, $footer);
        }
    }

    public function test_icon_links_have_accessible_names_tooltips_and_hidden_pictures(): void
    {
        $this->fillContact();

        $footer = $this->section('/', 'footer');

        foreach (['Chat with Eclipz on WhatsApp', 'Eclipz on Instagram', 'Eclipz on Facebook', 'Eclipz on LinkedIn', 'Eclipz on TikTok'] as $label) {
            $this->assertStringContainsString('aria-label="'.$label.'"', $footer);
            $this->assertStringContainsString('title="'.$label.'"', $footer);
        }

        $this->assertStringContainsString('aria-label="Email hello@example.test"', $footer);
        $this->assertStringContainsString('<svg aria-hidden="true" focusable="false"', $footer);
    }

    public function test_icon_buttons_do_not_repeat_the_network_name_as_visible_text(): void
    {
        $this->fillContact();

        $this->assertDoesNotMatchRegularExpression(
            '/>\s*(WhatsApp|Instagram|Facebook|LinkedIn|TikTok)\s*</',
            $this->section('/', 'footer')
        );
    }

    public function test_social_and_whatsapp_links_open_safely(): void
    {
        $this->fillContact();

        $footer = $this->section('/', 'footer');

        $this->assertSame(5, substr_count($footer, 'target="_blank"'));
        $this->assertSame(5, substr_count($footer, 'rel="noopener noreferrer"'));
    }

    public function test_missing_contact_details_show_no_icons_or_links(): void
    {
        $footer = $this->section('/', 'footer');

        foreach (['mailto:', 'tel:', 'wa.me', 'instagram', 'facebook', 'linkedin', 'tiktok'] as $absent) {
            $this->assertStringNotContainsString($absent, strtolower($footer));
        }

        $this->assertStringContainsString(route('contact'), $footer);
    }

    public function test_a_whatsapp_number_with_a_leading_zero_shows_no_whatsapp_icon(): void
    {
        $this->fillContact(['whatsapp' => '0801 234 5678']);

        $footer = $this->section('/', 'footer');

        $this->assertStringNotContainsString('wa.me', $footer);
        $this->assertStringNotContainsString('Chat with Eclipz on WhatsApp', $footer);
    }

    public function test_social_links_without_a_scheme_get_https(): void
    {
        $this->fillContact(['instagram' => 'instagram.com/eclipz-test']);

        $this->assertStringContainsString('href="https://instagram.com/eclipz-test"', $this->section('/', 'footer'));
    }

    public function test_unsafe_social_links_are_ignored(): void
    {
        $this->fillContact(['instagram' => 'javascript:alert(1)', 'facebook' => 'ftp://example.test/page', 'tiktok' => '@eclipz']);

        $footer = $this->section('/', 'footer');

        $this->assertStringNotContainsString('javascript:', $footer);
        $this->assertStringNotContainsString('ftp://', $footer);
        $this->assertStringNotContainsString('Eclipz on Instagram', $footer);
        $this->assertStringNotContainsString('Eclipz on Facebook', $footer);
        $this->assertStringNotContainsString('Eclipz on TikTok', $footer);
    }

    public function test_social_url_helper_accepts_only_web_addresses(): void
    {
        $contact = new ContactSetting([
            'instagram' => ' https://instagram.com/a ',
            'facebook' => 'facebook.com/b',
            'linkedin' => 'javascript:alert(1)',
            'tiktok' => '',
        ]);

        $this->assertSame('https://instagram.com/a', $contact->socialUrl('instagram'));
        $this->assertSame('https://facebook.com/b', $contact->socialUrl('facebook'));
        $this->assertNull($contact->socialUrl('linkedin'));
        $this->assertNull($contact->socialUrl('tiktok'));
        $this->assertNull($contact->socialUrl('email'));
    }

    public function test_contact_page_uses_the_same_icons(): void
    {
        $this->fillContact();

        $main = $this->section('/contact', 'main');

        $this->assertStringContainsString('href="mailto:hello@example.test"', $main);
        $this->assertStringContainsString('href="tel:+2348012345678"', $main);
        $this->assertStringContainsString('aria-label="Chat with Eclipz on WhatsApp"', $main);
        $this->assertStringContainsString('aria-label="Eclipz on Instagram"', $main);
    }

    public function test_confirmation_page_whatsapp_button_uses_the_whatsapp_icon(): void
    {
        $this->fillContact();

        $response = $this->withSession(['inquiry_reference' => 'EZ-2026-ABC234'])
            ->get(route('contact.received'))
            ->assertOk()
            ->assertSee('Follow up on WhatsApp');

        $this->assertStringContainsString('<svg aria-hidden="true" focusable="false"', $response->getContent());
        $this->assertStringNotContainsString('M8 10h8M8 14h5', $response->getContent());
    }
}