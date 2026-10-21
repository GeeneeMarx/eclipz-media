<?php

namespace Tests\Feature;

use App\Models\ContactSetting;
use Tests\TestCase;

class SocialUrlSafetyTest extends TestCase
{
    private function clean(string $value): ?string
    {
        return (new ContactSetting(['instagram' => $value]))->socialUrl('instagram');
    }

    public function test_bare_handles_and_words_are_not_links(): void
    {
        foreach (['@eclipz', '@eclipz.official', 'eclipz', 'eclipz official'] as $value) {
            $this->assertNull($this->clean($value), "{$value} should not become a link");
        }
    }

    public function test_links_with_embedded_credentials_are_rejected(): void
    {
        $this->assertNull($this->clean('https://instagram.com@evil.example/page'));
        $this->assertNull($this->clean('https://user:pass@instagram.com/eclipz'));
    }

    public function test_script_and_other_schemes_are_rejected(): void
    {
        foreach (['javascript:alert(1)', 'JAVASCRIPT:alert(1)', 'data:text/html,hi', 'vbscript:run', 'ftp://example.test/page'] as $value) {
            $this->assertNull($this->clean($value), "{$value} should be rejected");
        }
    }

    public function test_addresses_with_spaces_or_junk_are_rejected(): void
    {
        foreach (['https://exa mple.com', 'not a url', 'https://', '///'] as $value) {
            $this->assertNull($this->clean($value), "{$value} should be rejected");
        }
    }

    public function test_real_profile_addresses_are_accepted(): void
    {
        $this->assertSame('https://www.tiktok.com/@eclipz', $this->clean('https://www.tiktok.com/@eclipz'));
        $this->assertSame('https://instagram.com/eclipz', $this->clean('instagram.com/eclipz'));
        $this->assertSame('https://linkedin.com/company/eclipz-media', $this->clean(' https://linkedin.com/company/eclipz-media '));
        $this->assertSame('http://facebook.com/eclipz', $this->clean('http://facebook.com/eclipz'));
    }
}