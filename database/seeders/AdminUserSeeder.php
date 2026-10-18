<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Creates the initial administrator ONLY when no account with that email exists.
 * It never changes an existing account, so re-running the seeders can never reset a
 * password that Richard has already changed. The initial password comes from .env
 * (ADMIN_INITIAL_PASSWORD), never from source code.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = (string) config('admin.initial_email');

        if (User::where('email', $email)->exists()) {
            return;
        }

        $password = (string) config('admin.initial_password');

        if (strlen($password) < 12) {
            $this->command?->warn('Set ADMIN_INITIAL_PASSWORD in .env (at least 12 characters) to create the first administrator. No account was created.');

            return;
        }

        User::create([
            'name' => (string) config('admin.initial_name'),
            'email' => $email,
            'password' => $password,
            'role' => Role::Admin->value,
            'is_active' => true,
        ]);
    }
}