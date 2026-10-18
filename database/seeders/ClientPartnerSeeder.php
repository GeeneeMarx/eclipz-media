<?php

namespace Database\Seeders;

use App\Models\ClientPartner;
use App\Models\Industry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Classification per Phase 5 brief Section 5. Partners vs Clients is a
 * real distinction supplied by the source material, not interchangeable.
 */
class ClientPartnerSeeder extends Seeder
{
    public function run(): void
    {
        $partners = [
            ['name' => 'Purple Lavender Court', 'industry' => 'Hospitality & Shortlets'],
            ['name' => 'Purple Lavender Hotel', 'industry' => 'Hospitality & Shortlets'],
            ['name' => 'Afari Travels', 'industry' => 'Travel & Tourism'],
            ['name' => 'Jobrole Consulting', 'industry' => 'Professional Services'],
        ];

        $clients = [
            ['name' => 'Plus TV Africa', 'industry' => 'Media & Lifestyle'],
            ['name' => 'The Other Side Podcast', 'industry' => 'Podcasts'],
            ['name' => 'Veesioo Tech', 'industry' => 'Corporate & Technology'],
            ['name' => 'Gemini Universal Bizlink Limited', 'industry' => null],
            ['name' => 'Ridewell Global Services', 'industry' => 'Mobility'],
            ['name' => 'Nandis Healing Spa', 'industry' => 'Wellness'],
            ['name' => 'Ambrosial Cafe & Bakery', 'industry' => 'Food & Hospitality'],
            ['name' => 'Links & Tours Ltd', 'industry' => 'Travel & Tourism'],
            ['name' => 'LawrenceVille Apartment', 'industry' => 'Real Estate & Property'],
            ['name' => 'BLISS CleanTech Services', 'industry' => null],
        ];

        $this->seedGroup($partners, 'partner');
        $this->seedGroup($clients, 'client');
    }

    private function seedGroup(array $records, string $type): void
    {
        foreach ($records as $i => $c) {
            $industryId = $c['industry']
                ? Industry::where('slug', Str::slug($c['industry']))->value('id')
                : null;

            ClientPartner::updateOrCreate(
                ['name' => $c['name']],
                [
                    'type' => $type,
                    'industry_id' => $industryId,
                    'sort_order' => $i,
                    // logo, website, is_visible intentionally NOT reset here -
                    // preserves any real logos/visibility already set via /admin.
                ]
            );
        }
    }
}