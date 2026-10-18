<?php

namespace Tests\Feature;

use App\Filament\Resources\ClientPartnerResource\Pages\CreateClientPartner;
use App\Filament\Resources\MediaLibraryItemResource\Pages\CreateMediaLibraryItem;
use App\Models\AdminActivityLog;
use App\Models\User;
use App\Services\ActivityLogger;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Livewire\Livewire;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    // A valid 1x1 PNG, so the upload tests need no image library.
    private const PNG_BASE64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';

    private function admin(): User
    {
        return User::create([
            'name' => 'Test Admin',
            'email' => 'hardening-admin@example.test',
            'password' => 'not-a-real-password',
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_public_pages_send_security_headers(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeaderMissing('X-Robots-Tag')
            ->assertHeaderMissing('Strict-Transport-Security');
    }

    public function test_admin_pages_send_security_headers_and_are_not_indexable(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_hsts_is_sent_only_when_enabled_and_on_https(): void
    {
        config(['security.hsts' => true]);

        $this->get('https://localhost/')->assertHeader('Strict-Transport-Security', 'max-age=31536000');
        $this->get('http://localhost/')->assertHeaderMissing('Strict-Transport-Security');

        config(['security.hsts' => false]);

        $this->get('https://localhost/')->assertHeaderMissing('Strict-Transport-Security');
    }

    public function test_admin_seeder_creates_nothing_without_a_password_in_the_environment(): void
    {
        config(['admin.initial_email' => 'seed-admin@example.test', 'admin.initial_password' => null]);

        $this->seed(AdminUserSeeder::class);

        $this->assertDatabaseMissing('users', ['email' => 'seed-admin@example.test']);
    }

    public function test_admin_seeder_refuses_short_passwords(): void
    {
        config(['admin.initial_email' => 'seed-admin@example.test', 'admin.initial_password' => 'short']);

        $this->seed(AdminUserSeeder::class);

        $this->assertDatabaseMissing('users', ['email' => 'seed-admin@example.test']);
    }

    public function test_admin_seeder_creates_an_active_admin_with_a_hashed_password(): void
    {
        config(['admin.initial_email' => 'seed-admin@example.test', 'admin.initial_password' => 'Initial-Pass-2026!']);

        $this->seed(AdminUserSeeder::class);

        $user = User::where('email', 'seed-admin@example.test')->firstOrFail();

        $this->assertSame('admin', $user->role);
        $this->assertTrue($user->is_active);
        $this->assertNotSame('Initial-Pass-2026!', $user->password);
        $this->assertTrue(Hash::check('Initial-Pass-2026!', $user->password));
    }

    public function test_admin_seeder_never_overwrites_an_existing_account(): void
    {
        config(['admin.initial_email' => 'seed-admin@example.test', 'admin.initial_password' => 'Initial-Pass-2026!']);
        $this->seed(AdminUserSeeder::class);

        $user = User::where('email', 'seed-admin@example.test')->firstOrFail();
        $user->update(['password' => 'Changed-By-Richard-1!']);

        config(['admin.initial_password' => 'Another-Seed-Pass-9!']);
        $this->seed(AdminUserSeeder::class);

        $this->assertTrue(Hash::check('Changed-By-Richard-1!', $user->fresh()->password));
    }

    public function test_admin_can_open_the_profile_page_to_change_their_password(): void
    {
        $this->actingAs($this->admin());

        $this->get('/admin/profile')->assertOk();
    }

    public function test_password_policy_requires_length_case_and_a_number(): void
    {
        $fails = fn (string $password) => Validator::make(['p' => $password], ['p' => Password::default()])->fails();

        $this->assertTrue($fails('Short1'));
        $this->assertTrue($fails('alllowercase1234'));
        $this->assertTrue($fails('NoNumbersInThisOne'));
        $this->assertFalse($fails('Long-Enough-Pass-1'));
    }

    public function test_activity_log_truncates_long_text_instead_of_failing(): void
    {
        request()->headers->set('User-Agent', str_repeat('u', 400));

        ActivityLogger::log('updated', 'Project', 1, str_repeat('d', 400));

        $entry = AdminActivityLog::firstOrFail();

        $this->assertLessThanOrEqual(255, strlen($entry->description));
        $this->assertLessThanOrEqual(255, strlen((string) $entry->user_agent));
    }

    public function test_svg_uploads_are_rejected_for_client_logos(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());

        $svg = UploadedFile::fake()->createWithContent(
            'logo.svg',
            '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"><script>alert(1)</script></svg>'
        );

        Livewire::test(CreateClientPartner::class)
            ->fillForm(['name' => 'Svg Client', 'type' => 'client', 'logo' => $svg])
            ->call('create')
            ->assertHasFormErrors(['logo']);

        $this->assertDatabaseCount('clients_partners', 0);
    }

    public function test_png_uploads_are_accepted_for_client_logos(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());

        $png = UploadedFile::fake()->createWithContent('logo.png', base64_decode(self::PNG_BASE64));

        Livewire::test(CreateClientPartner::class)
            ->fillForm(['name' => 'Png Client', 'type' => 'client', 'logo' => $png])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseCount('clients_partners', 1);
    }

    public function test_script_files_are_rejected_by_the_media_library(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());

        $php = UploadedFile::fake()->createWithContent('shell.php', '<?php echo "x";');

        Livewire::test(CreateMediaLibraryItem::class)
            ->fillForm(['path' => $php, 'label' => 'Not allowed'])
            ->call('create')
            ->assertHasFormErrors(['path']);

        $this->assertDatabaseCount('media_library_items', 0);
    }
}