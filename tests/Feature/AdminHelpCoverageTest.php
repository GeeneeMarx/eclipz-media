<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\ProcessStep;
use App\Models\User;
use App\Support\AdminHelp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminHelpCoverageTest extends TestCase
{
    use RefreshDatabase;

    private function signIn(): void
    {
        $this->actingAs(User::create([
            'name' => 'Help Admin',
            'email' => 'help-coverage@example.test',
            'password' => 'not-a-real-password',
            'role' => 'admin',
            'is_active' => true,
        ]));
    }

    /** The leading run of plain letters, safe to look for inside an HTML attribute. */
    private function probe(string $text): string
    {
        $probe = explode('|', preg_replace('/[^A-Za-z0-9 ]/', '|', $text))[0];

        $this->assertGreaterThan(12, strlen($probe), 'Start this help text with 13 or more plain characters: '.$text);

        return $probe;
    }

    public function test_help_wording_is_complete_and_readable(): void
    {
        $count = 0;

        foreach (AdminHelp::section('fields') as $scope => $fields) {
            foreach ($fields as $name => $help) {
                $count++;

                $this->assertNotEmpty($help['short'] ?? null, "{$scope}.{$name} needs a short line");
                $this->assertNotEmpty($help['full'] ?? null, "{$scope}.{$name} needs the full text");
                $this->assertLessThanOrEqual(80, strlen($help['short']), "{$scope}.{$name} short line is too long");
                $this->assertLessThanOrEqual(420, strlen($help['full']), "{$scope}.{$name} full text is too long");
            }
        }

        $this->assertGreaterThan(80, $count, 'The help wording looks incomplete');

        foreach (['actions', 'columns'] as $group) {
            foreach (AdminHelp::section($group) as $name => $text) {
                $this->assertNotEmpty($text, "{$group}.{$name} is empty");
            }
        }
    }

    public function test_the_other_forms_show_their_field_help(): void
    {
        $this->signIn();

        foreach ([
            ['filament.admin.resources.projects.create', 'ProjectResource', ['name', 'slug', 'is_published', 'is_featured']],
            ['filament.admin.resources.testimonials.create', 'TestimonialResource', ['client_name', 'quote', 'is_published']],
            ['filament.admin.resources.services.create', 'ServiceResource', ['name', 'service_category_id', 'is_visible']],
            ['filament.admin.resources.service-categories.create', 'ServiceCategoryResource', ['name', 'is_visible']],
            ['filament.admin.resources.media-library-items.create', 'MediaLibraryItemResource', ['path', 'label']],
            ['filament.admin.pages.company-settings-page', 'CompanySettingsPage', ['company_name', 'tagline', 'mission', 'founder_name']],
        ] as [$route, $scope, $names]) {
            $html = $this->get(route($route))->assertOk()->getContent();
            $fields = AdminHelp::section('fields')[$scope];

            foreach ($names as $name) {
                $this->assertStringContainsString($this->probe($fields[$name]['full']), $html, "{$scope}.{$name} shows no info-icon text");
            }
        }
    }

    public function test_the_edit_pages_of_steps_and_enquiries_show_their_field_help(): void
    {
        $this->signIn();

        $step = ProcessStep::create(['title' => 'Discovery', 'description' => 'We learn about your brand.', 'sort_order' => 1, 'is_visible' => true]);
        $inquiry = Inquiry::create([
            'name' => 'Ada Visitor',
            'email' => 'ada@example.test',
            'service_requested' => 'Social Media',
            'message' => 'We would like help planning our launch content.',
        ]);

        foreach ([
            [route('filament.admin.resources.process-steps.edit', $step), 'ProcessStepResource', ['title', 'description', 'is_visible']],
            [route('filament.admin.resources.inquiries.edit', $inquiry), 'InquiryResource', ['email', 'status']],
        ] as [$url, $scope, $names]) {
            $html = $this->get($url)->assertOk()->getContent();
            $fields = AdminHelp::section('fields')[$scope];

            foreach ($names as $name) {
                $this->assertStringContainsString($this->probe($fields[$name]['full']), $html, "{$scope}.{$name} shows no info-icon text");
            }
        }
    }
}