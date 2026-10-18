<?php

namespace Tests\Feature;

use App\Mail\InquiryReceivedMail;
use App\Mail\NewInquiryAdminMail;
use App\Models\ContactSetting;
use App\Models\Inquiry;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

class InquiryNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $category = ServiceCategory::create([
            'name' => 'Social Media',
            'slug' => 'social-media',
            'sort_order' => 0,
            'is_visible' => true,
        ]);

        Service::create([
            'name' => 'Social media strategy',
            'slug' => 'social-media-strategy',
            'service_category_id' => $category->id,
            'sort_order' => 0,
            'is_visible' => true,
        ]);

        ContactSetting::current()->update([
            'inquiry_notification_email' => 'notify@example.test',
            'email' => 'info@example.test',
            'phone' => '+234 800 000 0000',
        ]);
    }

    private function data(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Ada Visitor',
            'company' => 'Example Co',
            'email' => 'ada@example.test',
            'phone' => '+234 801 234 5678',
            'service_requested' => 'Social Media',
            'budget' => 'under_500k',
            'message' => 'We would like help planning our launch content.',
        ], $overrides);
    }

    private function submit(array $overrides = [])
    {
        return $this->from(route('contact'))->post(
            route('contact.store'),
            array_merge($this->data(), ['form_token' => (string) Str::uuid()], $overrides)
        );
    }

    public function test_new_enquiry_sends_admin_notification_and_visitor_confirmation(): void
    {
        Mail::fake();

        $this->submit();
        $inquiry = Inquiry::firstOrFail();

        Mail::assertSent(NewInquiryAdminMail::class, fn ($mail) => $mail->hasTo('notify@example.test') && $mail->inquiry->is($inquiry));
        Mail::assertSent(InquiryReceivedMail::class, fn ($mail) => $mail->hasTo('ada@example.test') && $mail->inquiry->is($inquiry));
    }

    public function test_delivery_is_recorded_on_the_inquiry(): void
    {
        Mail::fake();

        $this->submit();
        $inquiry = Inquiry::firstOrFail();

        $this->assertNotNull($inquiry->admin_notified_at);
        $this->assertNull($inquiry->admin_notification_error);
        $this->assertNotNull($inquiry->visitor_confirmed_at);
        $this->assertNull($inquiry->visitor_confirmation_error);
    }

    public function test_admin_notification_contains_the_enquiry_details_and_admin_link(): void
    {
        $inquiry = Inquiry::createFromSubmission($this->data(), (string) Str::uuid());
        $mail = new NewInquiryAdminMail($inquiry);

        $mail->assertSeeInHtml($inquiry->reference);
        $mail->assertSeeInHtml('Ada Visitor');
        $mail->assertSeeInHtml('ada@example.test');
        $mail->assertSeeInHtml('Social Media');
        $mail->assertSeeInHtml(config('inquiry.budget_options.under_500k'));
        $mail->assertSeeInHtml('We would like help planning our launch content.');
        $mail->assertSeeInHtml(route('filament.admin.resources.inquiries.edit', ['record' => $inquiry->id]));
        $mail->assertSeeInText($inquiry->reference);
        $mail->assertSeeInText('Ada Visitor');
    }

    public function test_budget_line_is_omitted_when_the_visitor_gave_none(): void
    {
        $data = $this->data();
        unset($data['budget']);

        $inquiry = Inquiry::createFromSubmission($data, (string) Str::uuid());

        (new NewInquiryAdminMail($inquiry))->assertDontSeeInHtml('Budget');
    }

    public function test_visitor_confirmation_has_the_reference_and_contact_details_but_no_response_time_promise(): void
    {
        $inquiry = Inquiry::createFromSubmission($this->data(), (string) Str::uuid());
        $mail = new InquiryReceivedMail($inquiry);

        $mail->assertSeeInHtml($inquiry->reference);
        $mail->assertSeeInHtml('info@example.test');
        $mail->assertSeeInHtml('quote');
        $mail->assertSeeInText($inquiry->reference);
        $mail->assertDontSeeInHtml('within');
        $mail->assertDontSeeInHtml('hours');
    }

    public function test_enquiry_is_kept_and_logs_stay_private_when_mail_delivery_fails(): void
    {
        Log::spy();
        Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP down for ada@example.test'));

        $this->submit()->assertRedirect(route('contact.received'));

        $inquiry = Inquiry::firstOrFail();

        $this->assertNull($inquiry->admin_notified_at);
        $this->assertNull($inquiry->visitor_confirmed_at);
        $this->assertNotNull($inquiry->admin_notification_error);
        $this->assertNotNull($inquiry->visitor_confirmation_error);

        Log::shouldHaveReceived('warning')->withArgs(function ($message, $context = []) {
            return $message === 'Inquiry email failed'
                && ! str_contains(json_encode($context), 'ada@example.test')
                && str_contains((string) $context['detail'], '[email]');
        });
    }

    public function test_missing_recipient_is_recorded_and_the_visitor_is_still_confirmed(): void
    {
        ContactSetting::current()->update(['inquiry_notification_email' => null]);
        Mail::fake();

        $this->submit();

        Mail::assertNotSent(NewInquiryAdminMail::class);
        Mail::assertSent(InquiryReceivedMail::class);
        $this->assertNotNull(Inquiry::firstOrFail()->admin_notification_error);
    }

    public function test_double_submission_sends_each_email_only_once(): void
    {
        Mail::fake();
        $token = (string) Str::uuid();

        $this->submit(['form_token' => $token]);
        $this->submit(['form_token' => $token]);

        $this->assertDatabaseCount('inquiries', 1);
        Mail::assertSent(NewInquiryAdminMail::class, 1);
        Mail::assertSent(InquiryReceivedMail::class, 1);
    }

    public function test_subject_lines_cannot_be_used_for_header_injection(): void
    {
        $inquiry = Inquiry::createFromSubmission(
            $this->data(['name' => "Ada\r\nBcc: attacker@example.test"]),
            (string) Str::uuid()
        );

        $subject = (new NewInquiryAdminMail($inquiry))->envelope()->subject;

        $this->assertStringNotContainsString("\n", $subject);
        $this->assertStringNotContainsString("\r", $subject);
    }

    public function test_invalid_enquiry_sends_no_email(): void
    {
        Mail::fake();

        $this->submit(['email' => 'not-an-email']);

        Mail::assertNothingSent();
    }

    public function test_public_pages_never_show_the_private_notification_email(): void
    {
        $this->get(route('contact'))->assertOk()->assertDontSee('notify@example.test');
        $this->get(route('home'))->assertOk()->assertDontSee('notify@example.test');
    }
}