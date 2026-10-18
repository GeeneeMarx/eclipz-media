<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            CompanySettingSeeder::class,
            ContactSettingSeeder::class,
            IndustrySeeder::class,
            ServiceSeeder::class,
            ProcessStepSeeder::class,
            ClientPartnerSeeder::class,
            ProjectSeeder::class,
        ]);
    }
}