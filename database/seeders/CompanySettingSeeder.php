<?php

namespace Database\Seeders;

use App\Models\CompanySetting;
use Illuminate\Database\Seeder;

/** Verified company/about/founder content from the Phase 5 source material. */
class CompanySettingSeeder extends Seeder
{
    public function run(): void
    {
        CompanySetting::current()->update([
            'company_name' => 'Eclipz Media & Marketing Limited',
            'tagline' => 'Crafting Timeless Visual Stories',
            'short_description' => 'Eclipz Media & Marketing Limited is a Lagos-based creative media and digital marketing agency that helps modern brands build visibility, connect with their audiences, and drive growth through strategic content, digital marketing, advertising, and visual storytelling.',
            'story' => 'Eclipz began as a creative and media operation focused on content and digital media and has grown into a full-service creative media and marketing agency serving businesses across different industries. The business has evolved from individual creative work into an agency with a growing team, interns and multiple client accounts.',
            'what_we_do' => "Eclipz helps brands communicate and grow through the combination of Strategy, Content, Advertising and Storytelling. We don't simply create content. We develop creative assets around a brand's marketing objectives, audience and business goals.",
            'differentiator' => 'Eclipz bridges the gap between creativity and marketing performance, combining visual storytelling with digital marketing strategy so content production, social media and advertising strategy work together rather than operating as disconnected services.',
            'client_type_text' => 'Businesses and organizations that need professional content production, consistent social media management, digital advertising, brand positioning, campaign execution, event and corporate media, and strategic creative support.',
            'mission' => 'To empower businesses to tell better stories, construct enduring brands, and achieve scalable growth through innovative media solutions.',
            'vision' => "To emerge as one of Africa's foremost creative media and digital marketing agencies, universally celebrated for innovation, excellence, and tangible results.",
            'founder_name' => 'Richard Ese',
            'founder_title' => 'Founder & Creative Director',
            'founder_bio' => 'Richard Ese leads Eclipz Media & Marketing Limited with a clear objective: driving client growth through high-impact media production and precision digital advertising.',
            'founder_specializations' => 'Hospitality, Real estate, Travel, Corporate branding, Creative direction, Video production, Photography, Performance advertising',
            'founder_quote' => 'Combining creative storytelling with data-driven strategy to build unforgettable brands.',
        ]);
    }
}