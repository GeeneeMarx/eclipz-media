<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandLogoTest extends TestCase
{
    use RefreshDatabase;

    private function logoFiles(): array
    {
        return array_merge(
            glob(public_path('images/eclipz-logo-white.*')) ?: [],
            glob(public_path('images/eclipz-logo-black.*')) ?: [],
        );
    }

    public function test_both_logo_variants_exist(): void
    {
        foreach (['white', 'black'] as $variant) {
            $this->assertNotEmpty(
                glob(public_path("images/eclipz-logo-{$variant}.*")),
                "Missing the {$variant} logo file in public/images"
            );
        }
    }

    public function test_logo_files_are_transparent_and_efficient(): void
    {
        $files = $this->logoFiles();

        $this->assertNotEmpty($files);

        foreach ($files as $file) {
            $name = basename($file);
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $contents = file_get_contents($file);

            if ($extension === 'svg') {
                // A real vector, not a picture wrapped in an SVG (which carries its background).
                $this->assertStringNotContainsString('data:image', $contents, "{$name} embeds a bitmap");
                $this->assertStringNotContainsString('<image', $contents, "{$name} embeds a bitmap");
                $this->assertLessThan(150 * 1024, filesize($file), "{$name} is unexpectedly heavy for a vector logo");
            } elseif ($extension === 'png') {
                // PNG colour type 4 or 6 carries an alpha channel; palette PNGs need a tRNS chunk.
                $colourType = ord($contents[25]);

                $this->assertTrue(
                    in_array($colourType, [4, 6], true) || str_contains($contents, 'tRNS'),
                    "{$name} has no transparency"
                );
            } else {
                $this->fail("{$name} must be an SVG or a PNG");
            }
        }
    }

    public function test_header_offers_both_variants_and_footer_uses_the_white_one(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        preg_match('/<header.*?<\/header>/s', $html, $header);
        preg_match('/<footer.*<\/footer>/s', $html, $footer);

        $this->assertNotEmpty($header);
        $this->assertNotEmpty($footer);

        $this->assertStringContainsString('eclipz-logo-white', $header[0]);
        $this->assertStringContainsString('eclipz-logo-black', $header[0]);
        $this->assertStringNotContainsString('eclipz-icon.svg', $header[0]);

        $this->assertStringContainsString('eclipz-logo-white', $footer[0]);
        $this->assertStringNotContainsString('>ECLIPZ<', $footer[0]);
    }

    public function test_logo_addresses_carry_a_cache_busting_version(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('eclipz-logo-white', false);

        $this->assertMatchesRegularExpression(
            '/eclipz-logo-(white|black)\.(svg|png)\?v=\d+/',
            $this->get('/')->getContent()
        );
    }
}