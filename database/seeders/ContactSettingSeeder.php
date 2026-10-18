<?php

namespace Database\Seeders;

use App\Models\ContactSetting;
use Illuminate\Database\Seeder;

/**
 * Sets the initial private inquiry-notification address, only if none is
 * saved yet. It never overwrites a value an admin has already chosen.
 */
class ContactSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = ContactSetting::current();

        if (blank($settings->inquiry_notification_email)) {
            $settings->update(['inquiry_notification_email' => 'eclipzmedia247@gmail.com']);
        }
    }
}