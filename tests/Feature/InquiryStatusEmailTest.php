<?php

namespace Tests\Feature;

use App\Enums\InquiryStatus;
use App\Mail\InquiryReceivedMail;
use App\Mail\InquiryStatusUpdateMail;
use App\Mail\NewInquiryAdminMail;
use App\Models\ContactSetting;
use App\Models\Inquiry;
use App\Services\InquiryNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

class InquiryStatusEmailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        ContactSetting::current()->update([
            'inquiry_notification_email' => 'notify@example.test',
            'email' => 'info@example.test',
            'phone' => '+234 800 000 0000',
        ]);
    }

    private function makeInquiry(): Inquiry
    {
        return Inquiry::createFromSubmission([
            'name' => 'Ada Visitor',
            'email' => 'ada@example.test',
            'service_requested' => 'Social Media',
            'message' => 'We would like help planning our launch content.',
        ], (string) Str::uuid());
    }

    public function test_moving_to_under_review_emails_the_visitor_once(): void
    {
        Mail::fake();
        $inquiry = $this->makeInquiry();

        $inquiry->update(['status' => InquiryStatus::UnderReview]);

        Mail::assertSent(InquiryStatusUpdateMail::class, 1);
        Mail::assertSent(InquiryStatusUpdateMail::class, fn ($mail) => $mail->hasTo('ada@example.test') && $mail->status === InquiryStatus::UnderReview);

        $fresh = $inquiry->fresh();
        $this->assertSame('under_review', $fresh->last_notified_status);
        $this->assertNotNull($fresh->status_notified_at);
        $this->assertNull($fresh->status_notification_error);
    }

    public function test_saving_the_same_status_again_does_not_email(): void
    {
        Mail::fake();
        $inquiry = $this->makeInquiry();

        $inquiry->update(['status' => InquiryStatus::UnderReview]);
        $inquiry->update(['status' => InquiryStatus::UnderReview]);
        $inquiry->save();

        Mail::assertSent(InquiryStatusUpdateMail::class, 1);
    }

    public function test_internal_edits_do_not_email(): void
    {
        Mail::fake();
        $inquiry = $this->makeInquiry();

        $inquiry->update(['name' => 'Renamed Visitor', 'company' => 'New Co']);

        Mail::assertNothingSent();
    }

    public function test_each_forward_step_is_emailed_once(): void
    {
        Mail::fake();
        $inquiry = $this->makeInquiry();
        $steps = [InquiryStatus::UnderReview, InquiryStatus::Responded, InquiryStatus::Closed];

        foreach ($steps as $status) {
            $inquiry->update(['status' => $status]);
        }

        Mail::assertSent(InquiryStatusUpdateMail::class, 3);

        foreach ($steps as $status) {
            Mail::assertSent(InquiryStatusUpdateMail::class, fn ($mail) => $mail->status === $status);
        }
    }

    public function test_moving_backward_does_not_email(): void
    {
        Mail::fake();
        $inquiry = $this->makeInquiry();

        $inquiry->update(['status' => InquiryStatus::Closed]);
        $inquiry->update(['status' => InquiryStatus::Responded]);

        Mail::assertSent(InquiryStatusUpdateMail::class, 1);
        $this->assertSame('closed', $inquiry->fresh()->last_notified_status);
    }

    public function test_returning_to_new_never_emails(): void
    {
        Mail::fake();
        $inquiry = $this->makeInquiry();

        $inquiry->update(['status' => InquiryStatus::UnderReview]);
        $inquiry->update(['status' => InquiryStatus::New]);

        Mail::assertSent(InquiryStatusUpdateMail::class, 1);
    }

    public function test_status_emails_state_only_what_the_status_means(): void
    {
        $inquiry = $this->makeInquiry();

        $underReview = new InquiryStatusUpdateMail($inquiry, InquiryStatus::UnderReview);
        $responded = new InquiryStatusUpdateMail($inquiry, InquiryStatus::Responded);
        $closed = new InquiryStatusUpdateMail($inquiry, InquiryStatus::Closed);

        $underReview->assertSeeInHtml('under review');
        $responded->assertSeeInHtml('marked your enquiry as responded');
        $closed->assertSeeInHtml('has been closed');

        foreach ([$underReview, $responded, $closed] as $mail) {
            $mail->assertSeeInHtml($inquiry->reference);
            $mail->assertSeeInText($inquiry->reference);
            $mail->assertDontSeeInHtml('proposal');
            $mail->assertDontSeeInHtml('We called');
            $this->assertStringContainsString($inquiry->reference, $mail->envelope()->subject);
        }
    }

    public function test_failed_status_email_is_recorded_and_the_status_still_saves(): void
    {
        Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP down for ada@example.test'));
        $inquiry = $this->makeInquiry();

        $inquiry->update(['status' => InquiryStatus::UnderReview]);

        $fresh = $inquiry->fresh();
        $this->assertSame(InquiryStatus::UnderReview, $fresh->status);
        $this->assertNotNull($fresh->status_notification_error);
        $this->assertNull($fresh->last_notified_status);
        $this->assertStringNotContainsString('ada@example.test', $fresh->status_notification_error);
    }

    public function test_resend_retries_only_what_failed_and_only_once(): void
    {
        Mail::fake();
        $inquiry = $this->makeInquiry();
        $inquiry->forceFill([
            'admin_notified_at' => now(),
            'visitor_confirmation_error' => 'TransportException, timed out',
        ])->save();

        $notifier = app(InquiryNotifier::class);
        $first = $notifier->resendFailed($inquiry);
        $second = $notifier->resendFailed($inquiry);

        Mail::assertSent(InquiryReceivedMail::class, 1);
        Mail::assertNotSent(NewInquiryAdminMail::class);
        $this->assertSame(['visitor'], $first['sent']);
        $this->assertSame([], $first['failed']);
        $this->assertSame([], $second['attempted']);
        $this->assertNotNull($inquiry->fresh()->visitor_confirmed_at);
    }

    public function test_resend_does_nothing_when_no_failure_is_recorded(): void
    {
        Mail::fake();
        // Like an enquiry created before delivery tracking: no timestamps, no errors.
        $inquiry = $this->makeInquiry();

        $result = app(InquiryNotifier::class)->resendFailed($inquiry);

        Mail::assertNothingSent();
        $this->assertSame([], $result['attempted']);
    }

    public function test_resend_is_blocked_while_another_resend_is_running(): void
    {
        Mail::fake();
        $inquiry = $this->makeInquiry();
        $inquiry->forceFill(['visitor_confirmation_error' => 'TransportException, timed out'])->save();

        $lock = Cache::lock('inquiry-resend:'.$inquiry->getKey(), 60);
        $this->assertTrue($lock->get());

        $result = app(InquiryNotifier::class)->resendFailed($inquiry);

        $this->assertTrue($result['busy']);
        Mail::assertNothingSent();

        $lock->release();
    }

    public function test_failed_status_email_can_be_resent_once(): void
    {
        Mail::fake();
        $inquiry = $this->makeInquiry();
        $inquiry->forceFill([
            'status' => InquiryStatus::UnderReview,
            'status_notification_error' => 'TransportException, timed out',
        ])->saveQuietly();

        $notifier = app(InquiryNotifier::class);
        $notifier->resendFailed($inquiry);
        $notifier->resendFailed($inquiry);

        Mail::assertSent(InquiryStatusUpdateMail::class, 1);

        $fresh = $inquiry->fresh();
        $this->assertSame('under_review', $fresh->last_notified_status);
        $this->assertNull($fresh->status_notification_error);
    }

    public function test_smtp_timeout_is_short_and_configurable(): void
    {
        $this->assertSame(15, config('mail.mailers.smtp.timeout'));
    }
}