<?php

namespace Tests\Feature;

use App\Enums\InquiryStatus;
use App\Models\ContactSetting;
use App\Models\Inquiry;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Tests\TestCase;

class InquirySubmissionTest extends TestCase
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
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Ada Visitor',
            'company' => 'Example Co',
            'email' => 'ada@example.test',
            'phone' => '+234 801 234 5678',
            'service_requested' => 'Social Media',
            'budget' => 'under_500k',
            'message' => 'We would like help planning our launch content.',
            'form_token' => (string) Str::uuid(),
        ], $overrides);
    }

    private function submit(array $overrides = [])
    {
        return $this->from(route('contact'))->post(route('contact.store'), $this->payload($overrides));
    }

    public function test_valid_inquiry_is_saved_and_visitor_is_sent_to_the_confirmation_page(): void
    {
        $response = $this->submit();

        $inquiry = Inquiry::first();

        $this->assertNotNull($inquiry);
        $this->assertSame('Ada Visitor', $inquiry->name);
        $this->assertSame('ada@example.test', $inquiry->email);
        $this->assertSame('Social Media', $inquiry->service_requested);
        $this->assertSame('under_500k', $inquiry->budget);
        $this->assertSame(InquiryStatus::New, $inquiry->status);

        $response->assertRedirect(route('contact.received'));
        $response->assertSessionHas('inquiry_reference', $inquiry->reference);
    }

    public function test_budget_is_optional(): void
    {
        $payload = $this->payload();
        unset($payload['budget']);

        $this->from(route('contact'))->post(route('contact.store'), $payload)
            ->assertRedirect(route('contact.received'));

        $this->assertDatabaseCount('inquiries', 1);
        $this->assertNull(Inquiry::first()->budget);
    }

    public function test_phone_is_optional(): void
    {
        $this->submit(['phone' => ''])->assertRedirect(route('contact.received'));

        $this->assertDatabaseCount('inquiries', 1);
    }

    public function test_required_fields_are_validated(): void
    {
        foreach (['name', 'email', 'service_requested', 'message'] as $field) {
            $this->submit([$field => ''])->assertSessionHasErrors($field);
        }

        $this->assertDatabaseCount('inquiries', 0);
    }

    public function test_invalid_email_service_and_budget_are_rejected(): void
    {
        $this->submit(['email' => 'not-an-email'])->assertSessionHasErrors('email');
        $this->submit(['service_requested' => 'Underwater Basket Weaving'])->assertSessionHasErrors('service_requested');
        $this->submit(['budget' => 'a-million-dollars'])->assertSessionHasErrors('budget');

        $this->assertDatabaseCount('inquiries', 0);
    }

    public function test_short_message_and_bad_phone_are_rejected(): void
    {
        $this->submit(['message' => 'Too short'])->assertSessionHasErrors('message');
        $this->submit(['phone' => 'abc'])->assertSessionHasErrors('phone');

        $this->assertDatabaseCount('inquiries', 0);
    }

    public function test_attachments_are_rejected(): void
    {
        $data = $this->payload() + [
            'attachment' => UploadedFile::fake()->create('brief.pdf', 100, 'application/pdf'),
        ];

        $this->from(route('contact'))->post(route('contact.store'), $data)
            ->assertSessionHasErrors('form');

        $this->assertDatabaseCount('inquiries', 0);
    }

    public function test_honeypot_submissions_are_silently_dropped(): void
    {
        $this->submit(['hp_check' => 'http://spam.example'])
            ->assertRedirect(route('contact'));

        $this->assertDatabaseCount('inquiries', 0);
    }

    public function test_double_submission_with_the_same_token_creates_one_record(): void
    {
        $token = (string) Str::uuid();

        $this->submit(['form_token' => $token])->assertRedirect(route('contact.received'));
        $this->submit(['form_token' => $token])->assertRedirect(route('contact.received'));

        $this->assertDatabaseCount('inquiries', 1);
    }

    public function test_rate_limiting_blocks_the_sixth_attempt_in_a_minute(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->submit(['email' => 'not-an-email'])->assertSessionHasErrors('email');
        }

        $this->submit()->assertSessionHasErrors('form');

        $this->assertDatabaseCount('inquiries', 0);
    }

    public function test_confirmation_page_shows_the_saved_reference_and_copy_button(): void
    {
        $this->submit();
        $inquiry = Inquiry::firstOrFail();

        $this->withSession(['inquiry_reference' => $inquiry->reference])
            ->get(route('contact.received'))
            ->assertOk()
            ->assertSee($inquiry->reference)
            ->assertSee('Copy ID');
    }

    public function test_confirmation_page_redirects_without_a_session(): void
    {
        $this->get(route('contact.received'))->assertRedirect(route('contact'));
    }

    public function test_whatsapp_link_contains_the_reference_and_is_url_encoded(): void
    {
        ContactSetting::current()->update(['whatsapp' => '+234 801 234 5678']);

        $this->submit();
        $inquiry = Inquiry::firstOrFail();

        $expected = 'https://wa.me/2348012345678?text='.rawurlencode(Inquiry::followUpMessage($inquiry->reference));

        $this->withSession(['inquiry_reference' => $inquiry->reference])
            ->get(route('contact.received'))
            ->assertOk()
            ->assertSee($expected, false);
    }

    public function test_whatsapp_button_is_hidden_when_no_number_is_configured(): void
    {
        $this->withSession(['inquiry_reference' => 'EZ-2026-ABC234'])
            ->get(route('contact.received'))
            ->assertOk()
            ->assertDontSee('Follow up on WhatsApp');
    }

    public function test_contact_form_has_csrf_token_service_options_and_no_file_input(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('name="_token"', false)
            ->assertSee('Social Media')
            ->assertSee('Not sure yet / Other')
            ->assertDontSee('type="file"', false);
    }
}