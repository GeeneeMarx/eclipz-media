<?php

namespace Database\Seeders;

use App\Models\Industry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/** Industries as listed in the Eclipz brief (§11) — nothing added beyond it. */
class IndustrySeeder extends Seeder
{
    public function run(): void
    {
        $industries = [
            'Hospitality & Shortlets',
            'Real Estate & Property',
            'Travel & Tourism',
            'Corporate & Technology',
            'Media & Lifestyle',
            'Podcasts',
            'Wellness',
            'Food & Hospitality',
            'Mobility',
            'Events & Conferences',
            'Professional Services',
        ];

        foreach ($industries as $name) {
            Industry::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}
