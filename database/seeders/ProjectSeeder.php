<?php

namespace Database\Seeders;

use App\Models\Industry;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Every project below comes directly from the brief (§8). No client names,
 * services, locations, metrics, or narrative details are invented — where
 * the brief didn't supply something (e.g. a challenge/strategy narrative),
 * it is simply left out rather than guessed at.
 *
 * All projects seed as is_published = false, project_type = "visual".
 * There is no real cover image yet for any of them, so nothing goes live
 * on the public site until Eclipz supplies photography and an admin
 * publishes it deliberately. Afari Travels and Ambrosial Cafe & Bakery get
 * their real, brief-supplied metrics attached; no other project gets
 * fabricated numbers.
 */
class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'name' => 'Purple Lavender Court',
                'industry' => 'Hospitality & Shortlets',
                'location' => 'Osapa London, Lagos; Osborne Phase 2, Lagos; Ikoyi, Lagos',
                'services' => ['Social Media Management', 'Content Production', 'Photography', 'Videography', 'Reels', 'Paid Advertising', 'Brand Positioning', 'Campaign Execution'],
                'metrics' => [],
            ],
            [
                'name' => 'Purple Lavender Hotel',
                'industry' => 'Hospitality & Shortlets',
                'location' => null,
                'services' => ['Social Media Management', 'Content Production', 'Photography/Videography', 'Promotional Campaigns', 'Advertising', 'Creative Direction'],
                'metrics' => [],
            ],
            [
                'name' => 'Afari Travels',
                'industry' => 'Travel & Tourism',
                'location' => null,
                'services' => ['Social Media Management', 'Travel Content', 'Content Strategy', 'Creative Production', 'Paid Advertising', 'Campaign Execution', 'Lead Generation'],
                'metrics' => [
                    ['label' => 'Reach', 'value' => '119K+'],
                    ['label' => 'Engagements', 'value' => '2,310'],
                    ['label' => 'Followers Gained', 'value' => '389'],
                    ['label' => 'Qualified Leads', 'value' => '38'],
                ],
            ],
            [
                'name' => 'Jobrole Consulting — Next Frontier Conference',
                'industry' => 'Professional Services',
                'location' => null,
                'services' => ['Event Marketing', 'Promotional Video', 'Creative Direction', 'Campaign Content', 'Conference Media', 'Social Media Marketing'],
                'metrics' => [],
            ],
            [
                'name' => 'Plus TV Africa',
                'industry' => 'Media & Lifestyle',
                'location' => null,
                'services' => ['Media Production', 'Video/Content Production', 'Creative Support'],
                'metrics' => [],
            ],
            [
                'name' => 'The Other Side Podcast',
                'industry' => 'Podcasts',
                'location' => null,
                'services' => ['Podcast Production', 'Creative Direction', 'Video Editing', 'Social Media Clips', 'Thumbnail Design', 'Content Production'],
                'metrics' => [],
            ],
            [
                'name' => 'Veesioo Tech',
                'industry' => 'Corporate & Technology',
                'location' => null,
                'services' => ['Digital Marketing', 'Social Media', 'Content Creation', 'Creative Services'],
                'metrics' => [],
            ],
            [
                'name' => 'Gemini Universal Bizlink Limited',
                'industry' => null,
                'location' => null,
                'services' => ['Social Media Strategy', 'Content Creation', 'Digital Marketing', 'Brand Communication', 'Campaign Strategy'],
                'metrics' => [],
            ],
            [
                'name' => 'Ridewell Global Services',
                'industry' => 'Mobility',
                'location' => null,
                'services' => ['Brand Strategy', 'Marketing Strategy', 'Content Strategy', 'Digital Marketing', 'Partnership/Referral Campaign Strategy'],
                'metrics' => [],
            ],
            [
                'name' => 'Nandis Healing Spa',
                'industry' => 'Wellness',
                'location' => null,
                'services' => ['Social Media Content', 'Promotional Campaigns', 'Creative Strategy', 'Service Promotion', 'Digital Marketing'],
                'metrics' => [],
            ],
            [
                'name' => 'Ambrosial Cafe & Bakery',
                'industry' => 'Food & Hospitality',
                'location' => null,
                'services' => ['Google Ads', 'Google Business Optimization', 'Digital Marketing', 'Local Search Marketing', 'Performance Tracking'],
                'metrics' => [
                    ['label' => 'Calls', 'value' => '56'],
                    ['label' => 'Direction Requests', 'value' => '525'],
                    ['label' => 'Website Clicks', 'value' => '139'],
                    ['label' => 'Profile Views', 'value' => '3,460'],
                    ['label' => 'Searches', 'value' => '1,243'],
                    ['label' => 'Total Interactions', 'value' => '720'],
                ],
            ],
            [
                'name' => 'Links & Tours Ltd',
                'industry' => 'Travel & Tourism',
                'location' => null,
                'services' => ['Content Strategy', 'Social Media Content', 'Travel Marketing', 'SEO/Blog Content', 'Digital Marketing'],
                'metrics' => [],
            ],
        ];

        foreach ($projects as $i => $p) {
            $industryId = $p['industry']
                ? Industry::where('slug', Str::slug($p['industry']))->value('id')
                : null;

            $project = Project::updateOrCreate(
                ['slug' => Str::slug($p['name'])],
                [
                    'name' => $p['name'],
                    'industry_id' => $industryId,
                    'project_type' => 'visual',
                    'location' => $p['location'],
                    'cover_image' => null, // no stock imagery — stays empty until real photography is supplied
                    'is_featured' => false,
                    'is_published' => false, // publish deliberately once real assets exist
                    'sort_order' => $i,
                ]
            );

            $serviceIds = collect($p['services'])->map(
                fn ($name) => Service::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    ['name' => $name, 'pillar' => null]
                )->id
            );
            $project->services()->sync($serviceIds);

            $project->metrics()->delete();
            foreach ($p['metrics'] as $j => $metric) {
                $project->metrics()->create([
                    'label' => $metric['label'],
                    'value' => $metric['value'],
                    'sort_order' => $j,
                ]);
            }
        }
    }
}
