<?php

namespace Tests\Feature;

use App\Filament\Widgets\QuickActionsWidget;
use App\Models\Inquiry;
use App\Models\Project;
use App\Models\User;
use App\Support\DashboardMetrics;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function admin(string $role = 'admin'): User
    {
        return User::create([
            'name' => 'Test Admin',
            'email' => $role.'@example.test',
            'password' => 'not-a-real-password',
            'role' => $role,
            'is_active' => true,
        ]);
    }

    private function makeInquiry(array $overrides = []): Inquiry
    {
        return Inquiry::create(array_merge([
            'name' => 'Ada Visitor',
            'email' => 'ada@example.test',
            'service_requested' => 'Social Media',
            'message' => 'We would like help planning our launch content.',
        ], $overrides));
    }

    public function test_inquiry_counts_come_from_the_database(): void
    {
        $this->makeInquiry();
        $this->makeInquiry(['email' => 'b@example.test']);
        $this->makeInquiry(['email' => 'c@example.test', 'status' => 'under_review']);
        $this->makeInquiry(['email' => 'd@example.test', 'status' => 'responded']);
        $this->makeInquiry(['email' => 'e@example.test', 'status' => 'closed']);

        $old = $this->makeInquiry(['email' => 'old@example.test']);
        $old->forceFill(['created_at' => now()->subDays(10)])->saveQuietly();

        $problem = $this->makeInquiry(['email' => 'f@example.test', 'status' => 'closed']);
        $problem->forceFill(['visitor_confirmation_error' => 'TransportException, timed out'])->saveQuietly();

        $this->assertEquals([
            'total' => 7,
            'new' => 3,
            'under_review' => 1,
            'responded' => 1,
            'closed' => 2,
            'last_7_days' => 6,
            'email_problems' => 1,
        ], DashboardMetrics::inquiryCounts());
    }

    public function test_counts_are_zero_on_an_empty_database(): void
    {
        $this->assertEquals([
            'total' => 0,
            'new' => 0,
            'under_review' => 0,
            'responded' => 0,
            'closed' => 0,
            'last_7_days' => 0,
            'email_problems' => 0,
        ], DashboardMetrics::inquiryCounts());

        $this->assertEquals(['published' => 0, 'draft' => 0], DashboardMetrics::projectCounts());
    }

    public function test_project_counts_come_from_the_database(): void
    {
        Project::create(['name' => 'Live Project', 'slug' => 'live-project', 'is_published' => true]);
        Project::create(['name' => 'Draft One', 'slug' => 'draft-one', 'is_published' => false]);
        Project::create(['name' => 'Draft Two', 'slug' => 'draft-two', 'is_published' => false]);

        $this->assertEquals(['published' => 1, 'draft' => 2], DashboardMetrics::projectCounts());
    }

    public function test_dashboard_renders_with_intentional_empty_states(): void
    {
        $this->actingAs($this->admin());

        $this->get('/admin')
            ->assertOk()
            ->assertSee('Quick actions')
            ->assertSee('Total enquiries')
            ->assertSee('Published projects')
            ->assertSee('No enquiries yet');
    }

    public function test_dashboard_lists_recent_enquiries(): void
    {
        $inquiry = $this->makeInquiry();
        $this->actingAs($this->admin());

        $this->get('/admin')
            ->assertOk()
            ->assertSee($inquiry->reference)
            ->assertSee('Ada Visitor');
    }

    public function test_quick_actions_link_to_valid_admin_pages(): void
    {
        $this->actingAs($this->admin());
        $this->get('/admin')->assertOk();

        $actions = QuickActionsWidget::actions();

        $this->assertGreaterThanOrEqual(5, count($actions));

        foreach ($actions as $action) {
            $this->assertStringStartsWith('/admin', (string) parse_url($action['url'], PHP_URL_PATH));
            $this->get($action['url'])->assertOk();
        }
    }

    public function test_quick_actions_offer_attention_items_only_when_there_are_some(): void
    {
        $this->actingAs($this->admin());
        $this->get('/admin')->assertOk();

        $labels = array_column(QuickActionsWidget::actions(), 'label');
        $this->assertNotContains('Review new enquiries', $labels);
        $this->assertNotContains('Fix email problems', $labels);

        $this->makeInquiry();
        $problem = $this->makeInquiry(['email' => 'b@example.test']);
        $problem->forceFill(['visitor_confirmation_error' => 'TransportException, timed out'])->saveQuietly();

        $actions = QuickActionsWidget::actions();
        $labels = array_column($actions, 'label');
        $this->assertContains('Review new enquiries', $labels);
        $this->assertContains('Fix email problems', $labels);

        foreach ($actions as $action) {
            $this->get($action['url'])->assertOk();
        }
    }

    public function test_dashboard_is_not_available_to_non_admins(): void
    {
        $this->actingAs($this->admin('editor'));

        $this->get('/admin')->assertForbidden();
    }
}