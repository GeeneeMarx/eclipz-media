<?php

namespace Tests\Feature;

use App\Enums\InquiryStatus;
use App\Mail\InquiryReceivedMail;
use App\Mail\InquiryStatusUpdateMail;
use App\Models\ContactSetting;
use App\Models\Inquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailBrandingTest extends TestCase
{
    use RefreshDatabase;

    private function inquiry(): Inquiry
    {
        return Inquiry::create([
            'name' => 'Ada Visitor',
            'email' => 'ada@example.test',
            'service_requested' => 'Social Media',
            'message' => 'We would like help planning our launch content.',
        ]);
    }

    public function test_email_header_uses_the_transparent_white_website_logo(): void
    {
        $this->assertFileExists(public_path('images/eclipz-logo-white.png'));

        $layout = file_get_contents(resource_path('views/emails/layout.blade.php'));

        $this->assertStringContainsString("public_path('images/eclipz-logo-white.png')", $layout);
        $this->assertStringContainsString('$headerBackground = \'#0A0A0A\'', $layout);
    }

    public function test_the_whatsapp_icon_for_emails_is_a_small_transparent_png(): void
    {
        $file = public_path('images/email-whatsapp.png');

        $this->assertFileExists($file);
        $this->assertLessThan(20 * 1024, filesize($file), 'The email icon should be a few KB');

        $contents = file_get_contents($file);

        $this->assertSame("\x89PNG\r\n\x1a\n", substr($contents, 0, 8), 'Not a PNG file');
        // PNG colour type 4 or 6 carries an alpha channel.
        $this->assertContains(ord($contents[25]), [4, 6], 'The icon has no transparency');

        [$width, $height] = getimagesize($file);

        $this->assertSame($width, $height);
        $this->assertGreaterThanOrEqual(48, $width);
        $this->assertLessThanOrEqual(192, $width);
    }

    public function test_both_emails_share_one_whatsapp_button(): void
    {
        foreach (['inquiry-received', 'inquiry-status-update'] as $view) {
            $source = file_get_contents(resource_path("views/emails/{$view}.blade.php"));

            $this->assertStringContainsString("@include('emails.partials.whatsapp-button')", $source);
            $this->assertStringNotContainsString('Follow up on WhatsApp', $source);
        }

        $partial = file_get_contents(resource_path('views/emails/partials/whatsapp-button.blade.php'));

        $this->assertStringContainsString('email-whatsapp.png', $partial);
        $this->assertStringContainsString('Follow up on WhatsApp', $partial);
        $this->assertStringContainsString('#0A0A0A', $partial);
    }

    public function test_both_emails_show_the_whatsapp_button_when_a_number_is_configured(): void
    {
        ContactSetting::current()->update(['whatsapp' => '+2348012345678']);

        $inquiry = $this->inquiry();

        foreach ([
            (new InquiryReceivedMail($inquiry))->render(),
            (new InquiryStatusUpdateMail($inquiry, InquiryStatus::UnderReview))->render(),
        ] as $html) {
            $this->assertStringContainsString('Follow up on WhatsApp', $html);
            $this->assertStringContainsString('https://wa.me/2348012345678', $html);
        }
    }

    public function test_the_button_is_left_out_when_no_whatsapp_number_is_set(): void
    {
        $inquiry = $this->inquiry();

        $this->assertStringNotContainsString('Follow up on WhatsApp', (new InquiryReceivedMail($inquiry))->render());
        $this->assertStringNotContainsString('Follow up on WhatsApp', (new InquiryStatusUpdateMail($inquiry, InquiryStatus::UnderReview))->render());
    }
}