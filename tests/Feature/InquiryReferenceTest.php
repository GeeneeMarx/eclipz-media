<?php

namespace Tests\Feature;

use App\Enums\InquiryStatus;
use App\Models\ContactSetting;
use App\Models\Inquiry;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class InquiryReferenceTest extends TestCase
{
    use RefreshDatabase;

    private function data(): array
    {
        return [
            'name' => 'Test Visitor',
            'email' => 'visitor@example.test',
            'service_requested' => 'Not sure yet / Other',
            'message' => 'A test message for the reference tests.',
        ];
    }

    public function test_generated_references_follow_the_required_format(): void
    {
        for ($i = 0; $i < 50; $i++) {
            $this->assertMatchesRegularExpression('/^EZ-\d{4}-[A-Z0-9]{6}$/', Inquiry::generateReference());
        }
    }

    public function test_year_in_the_reference_uses_the_lagos_timezone(): void
    {
        // 23:30 UTC on 31 December is already 00:30 on 1 January in Lagos.
        $this->travelTo(Carbon::parse('2026-12-31 23:30:00', 'UTC'));

        $this->assertStringStartsWith('EZ-2027-', Inquiry::generateReference());
    }

    public function test_references_are_unique_at_database_level(): void
    {
        $row = ['reference' => 'EZ-2026-AAAAAA', 'name' => 'A', 'email' => 'a@example.test', 'status' => 'new'];

        DB::table('inquiries')->insert($row);

        $this->expectException(QueryException::class);

        DB::table('inquiries')->insert(array_merge($row, ['name' => 'B']));
    }

    public function test_each_saved_inquiry_gets_a_distinct_reference(): void
    {
        $references = [];

        for ($i = 0; $i < 20; $i++) {
            $references[] = Inquiry::createFromSubmission($this->data(), (string) Str::uuid())->reference;
        }

        $this->assertCount(20, array_unique($references));
    }

    public function test_reference_never_changes_when_an_inquiry_is_edited(): void
    {
        $inquiry = Inquiry::createFromSubmission($this->data(), (string) Str::uuid());
        $original = $inquiry->reference;

        $inquiry->reference = 'EZ-2000-ZZZZZZ';
        $inquiry->name = 'Renamed Visitor';
        $inquiry->status = InquiryStatus::UnderReview;
        $inquiry->save();

        $fresh = $inquiry->fresh();

        $this->assertSame($original, $fresh->reference);
        $this->assertSame('Renamed Visitor', $fresh->name);
        $this->assertSame(InquiryStatus::UnderReview, $fresh->status);
    }

    public function test_reference_cannot_be_set_through_mass_assignment(): void
    {
        $inquiry = Inquiry::create(array_merge($this->data(), ['reference' => 'EZ-2000-FORGED']));

        $this->assertNotSame('EZ-2000-FORGED', $inquiry->reference);
        $this->assertMatchesRegularExpression('/^EZ-\d{4}-[A-Z0-9]{6}$/', $inquiry->reference);
    }

    public function test_submission_token_is_unique(): void
    {
        $token = (string) Str::uuid();

        Inquiry::createFromSubmission($this->data(), $token);

        $this->expectException(UniqueConstraintViolationException::class);

        Inquiry::createFromSubmission($this->data(), $token);
    }

    public function test_whatsapp_url_rejects_numbers_with_a_leading_zero(): void
    {
        ContactSetting::current()->update(['whatsapp' => '0801 234 5678']);

        $this->assertNull(ContactSetting::current()->whatsappUrl('Hello'));

        ContactSetting::current()->update(['whatsapp' => '+234 801 234 5678']);

        $this->assertSame(
            'https://wa.me/2348012345678?text=Hello',
            ContactSetting::current()->whatsappUrl('Hello')
        );
    }

    public function test_status_lifecycle_has_the_four_agreed_values(): void
    {
        $this->assertSame(
            ['new', 'under_review', 'responded', 'closed'],
            array_map(fn (InquiryStatus $status) => $status->value, InquiryStatus::cases())
        );

        $this->assertSame('Under Review', InquiryStatus::UnderReview->label());
    }
}