<?php

namespace Tests\Feature;

use App\Enums\InquiryStatus;
use App\Filament\Resources\InquiryResource\Pages\EditInquiry;
use App\Filament\Resources\InquiryResource\Pages\ListInquiries;
use App\Mail\InquiryReceivedMail;
use App\Mail\InquiryStatusUpdateMail;
use App\Mail\NewInquiryAdminMail;
use App\Models\AdminActivityLog;
use App\Models\ContactSetting;
use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class AdminInquiryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.test',
            'password' => 'not-a-real-password',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($admin);

        ContactSetting::current()->update([
            'inquiry_notification_email' => 'notify@example.test',
            'email' => 'info@example.test',
        ]);
    }

    private function makeInquiry(array $overrides = []): Inquiry
    {
        return Inquiry::createFromSubmission(array_merge([
            'name' => 'Ada Visitor',
            'email' => 'ada@example.test',
            'service_requested' => 'Social Media',
            'message' => 'We would like help planning our launch content.',
        ], $overrides), (string) Str::uuid());
    }

    private function makeFailedInquiry(): Inquiry
    {
        $inquiry = $this->makeInquiry(['name' => 'Noor Visitor', 'email' => 'noor@example.test']);
        $inquiry->forceFill([
            'admin_notified_at' => now(),
            'visitor_confirmation_error' => 'TransportException, timed out',
        ])->save();

        return $inquiry;
    }

    public function test_list_shows_the_inquiry_id_and_email_delivery_state(): void
    {
        $failed = $this->makeFailedInquiry();

        Livewire::test(ListInquiries::class)
            ->assertCanSeeTableRecords([$failed])
            ->assertSee($failed->reference)
            ->assertSee('Failed');
    }

    public function test_list_can_be_searched_by_inquiry_id(): void
    {
        $one = $this->makeInquiry(['name' => 'First Visitor']);
        $two = $this->makeInquiry(['name' => 'Second Visitor', 'email' => 'second@example.test']);

        Livewire::test(ListInquiries::class)
            ->searchTable($one->reference)
            ->assertCanSeeTableRecords([$one])
            ->assertCanNotSeeTableRecords([$two]);
    }

    public function test_list_can_be_filtered_by_status(): void
    {
        $new = $this->makeInquiry(['name' => 'Newer Visitor']);
        $reviewing = $this->makeInquiry(['name' => 'Reviewing Visitor', 'email' => 'reviewing@example.test']);
        $reviewing->update(['status' => InquiryStatus::UnderReview]);

        Livewire::test(ListInquiries::class)
            ->filterTable('status', InquiryStatus::UnderReview->value)
            ->assertCanSeeTableRecords([$reviewing])
            ->assertCanNotSeeTableRecords([$new]);
    }

    public function test_resend_action_is_only_offered_for_enquiries_with_failed_emails(): void
    {
        $failed = $this->makeFailedInquiry();
        $clean = $this->makeInquiry(['name' => 'Clean Visitor', 'email' => 'clean@example.test']);

        Livewire::test(ListInquiries::class)
            ->assertTableActionVisible('resendEmails', $failed)
            ->assertTableActionHidden('resendEmails', $clean);
    }

    public function test_resend_action_retries_the_failed_email_and_logs_it(): void
    {
        Mail::fake();
        $failed = $this->makeFailedInquiry();

        Livewire::test(ListInquiries::class)->callTableAction('resendEmails', $failed);

        Mail::assertSent(InquiryReceivedMail::class, 1);
        Mail::assertNotSent(NewInquiryAdminMail::class);
        $this->assertNotNull($failed->fresh()->visitor_confirmed_at);
        $this->assertSame(1, AdminActivityLog::where('action', 'resent_emails')->where('entity_id', $failed->id)->count());
    }

    public function test_changing_status_on_the_edit_page_emails_the_visitor_once_and_logs_it(): void
    {
        Mail::fake();
        $inquiry = $this->makeInquiry();

        Livewire::test(EditInquiry::class, ['record' => $inquiry->getRouteKey()])
            ->fillForm(['status' => InquiryStatus::UnderReview->value])
            ->call('save')
            ->assertHasNoFormErrors()
            ->call('save');

        Mail::assertSent(InquiryStatusUpdateMail::class, 1);
        $this->assertSame(InquiryStatus::UnderReview, $inquiry->fresh()->status);
        $this->assertSame(1, AdminActivityLog::where('action', 'status_changed')->where('entity_id', $inquiry->id)->count());
    }

    public function test_edit_page_shows_the_inquiry_id_with_a_copy_button(): void
    {
        $inquiry = $this->makeInquiry();

        Livewire::test(EditInquiry::class, ['record' => $inquiry->getRouteKey()])
            ->assertSee($inquiry->reference)
            ->assertSee('Copy ID');
    }
}