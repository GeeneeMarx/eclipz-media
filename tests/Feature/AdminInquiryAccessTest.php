<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminInquiryAccessTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role = 'admin', bool $active = true): User
    {
        return User::create([
            'name' => 'Test User',
            'email' => Str::random(8).'@example.test',
            'password' => 'not-a-real-password',
            'role' => $role,
            'is_active' => $active,
        ]);
    }

    public function test_guests_are_redirected_to_the_admin_login(): void
    {
        $this->get('/admin/inquiries')->assertRedirectContains('/admin/login');
    }

    public function test_non_admin_roles_cannot_open_the_inquiry_resource(): void
    {
        $this->actingAs($this->makeUser('editor'))
            ->get('/admin/inquiries')
            ->assertForbidden();
    }

    public function test_inactive_admins_cannot_open_the_inquiry_resource(): void
    {
        $this->actingAs($this->makeUser('admin', false))
            ->get('/admin/inquiries')
            ->assertForbidden();
    }

    public function test_active_admins_can_open_the_inquiry_resource(): void
    {
        $this->actingAs($this->makeUser())
            ->get('/admin/inquiries')
            ->assertOk();
    }

    public function test_the_admin_panel_has_no_registration_route(): void
    {
        $this->get('/admin/register')->assertNotFound();
    }

    public function test_public_pages_do_not_link_to_the_admin(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('/admin', false);
    }
}