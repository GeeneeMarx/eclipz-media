<?php

namespace Database\Seeders;

use App\Models\ProcessStep;
use Illuminate\Database\Seeder;

/** The verified 8-step Eclipz process - exact wording, no invented steps. */
class ProcessStepSeeder extends Seeder
{
    public function run(): void
    {
        $steps = [
            ['Discovery', 'Understanding goals and challenges.'],
            ['Research', 'Analysing market and audience.'],
            ['Strategy', 'Planning impactful creative solutions.'],
            ['Production', 'Creating compelling visual content.'],
            ['Editing', 'Refining content for excellence.'],
            ['Launch', 'Publishing across target platforms.'],
            ['Optimize', 'Improving performance through insights.'],
            ['Measure', 'Tracking results and growth.'],
        ];

        foreach ($steps as $i => [$title, $description]) {
            ProcessStep::updateOrCreate(
                ['title' => $title],
                ['description' => $description, 'sort_order' => $i, 'is_visible' => true]
            );
        }
    }
}