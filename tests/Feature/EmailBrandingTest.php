<?php

namespace Tests\Feature;

use Tests\TestCase;

class EmailBrandingTest extends TestCase
{
    public function test_email_header_uses_the_transparent_white_website_logo(): void
    {
        $this->assertFileExists(public_path('images/eclipz-logo-white.png'));

        $layout = file_get_contents(resource_path('views/emails/layout.blade.php'));

        $this->assertStringContainsString("public_path('images/eclipz-logo-white.png')", $layout);
        $this->assertStringContainsString('$headerBackground = \'#0A0A0A\'', $layout);
    }
}