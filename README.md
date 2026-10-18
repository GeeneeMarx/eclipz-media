# Eclipz Media & Marketing — Production Platform

Phase 1 (Foundation) deliverable: architecture, database, auth, admin
foundation, and locked design tokens. This is real code, written and
reviewed but **not executed** in the environment that produced it (no
PHP/Composer/MySQL available there) — see "First run" below for what to
verify once it's on your machine.

## Requirements
- PHP 8.2+, Composer 2.x
- MySQL 8.0+ (or MariaDB 10.3+)
- Node.js 18+, npm

## Setup

```bash
composer create-project laravel/laravel eclipz-app
cd eclipz-app
composer require filament/filament:"^3.2" -W
php artisan filament:install --panels
```

`filament:install` generates its own `app/Providers/Filament/AdminPanelProvider.php`
— **overwrite it** with the one in this delivery (it locks the panel to
`/admin`, applies the Electric Blue accent, and wires in `EnsureAdminIsActive`).

Then copy in, preserving paths:
- `app/Enums/*` → `app/Enums/`
- `app/Models/*` → `app/Models/` (overwrite the default `User.php`)
- `app/Http/Middleware/EnsureAdminIsActive.php`
- `app/Services/ActivityLogger.php`
- `app/Filament/*` → `app/Filament/`
- `database/migrations/*` (remove Laravel's default migrations first — ours
  replaces the default users/password_reset_tokens/sessions migration)
- `database/seeders/*`
- `resources/views/filament/*`, `resources/views/public/*`
- `resources/css/app.css`, `resources/js/app.js`, `tailwind.config.js`, `vite.config.js`
- `routes/web.php`
- `bootstrap/providers.php` (merge with anything Filament already added)
- `.env.example`
- `DESIGN_SYSTEM.md`

```bash
mysql -u root -p -e "CREATE DATABASE eclipz_media CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
cp .env.example .env
# edit .env: DB_DATABASE, DB_USERNAME, DB_PASSWORD
php artisan key:generate
php artisan storage:link

npm install
npm install -D tailwindcss postcss autoprefixer laravel-vite-plugin
```

**Before seeding:** open `database/seeders/AdminUserSeeder.php` and replace
the placeholder email and password with Richard's real email and a real
first password — do not run this seeder with the placeholder values in any
shared environment.

```bash
php artisan migrate --seed
npm run build   # or `npm run dev` while working locally
php artisan serve
```

## First run — what to verify
This code hasn't been executed anywhere yet, so treat the first run as a
real test, not a formality:
1. `/admin` loads a login page, with no link to it anywhere on `/`.
2. Richard's seeded credentials log in; a wrong password is rejected.
3. Dashboard shows the Filament default; Projects/Clients & Partners/
   Testimonials/Inquiries/Settings/Activity Log all appear in the nav.
4. Creating/editing/publishing a project writes a row to
   `admin_activity_logs` (check via `php artisan tinker` →
   `App\Models\AdminActivityLog::latest()->first()`).
5. Toggling `is_active` to `false` on Richard's user (via tinker) and
   refreshing any admin page logs him out immediately.
6. `/` renders the placeholder homepage in Jet Black with Bebas Neue +
   Archivo loading correctly (check Network tab for the Google Fonts request).

Report back anything that breaks here — I'd rather fix it against a real
error than guess at a fix from a description.

## What's NOT in Phase 1
Public site pages (Work/Services/About/Process/Clients/Contact), the
inquiry form, media library UI, and role/permission expansion beyond
`admin` are Phases 2–4, per the brief's phase plan.
