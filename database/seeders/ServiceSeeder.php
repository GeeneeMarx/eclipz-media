<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * The canonical Phase 5 service taxonomy.
 *
 * services.name is unique (and MySQL compares it case-insensitively), so
 * existing rows are matched by NAME and moved into their category instead
 * of being inserted again. This preserves every project_service link from
 * earlier phases. Older services that are not part of this taxonomy are
 * left alone: they simply have no category and do not appear on /services.
 */
class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Social Media' => [
                'Social media strategy', 'Social media management', 'Content planning',
                'Community management', 'Social media optimization', 'Platform management',
            ],
            'Content Production' => [
                'Photography', 'Videography', 'Reels and short-form video', 'Corporate videos',
                'Promotional videos', 'Event coverage', 'Behind-the-scenes content',
                'Product and service content', 'Content editing',
            ],
            'Digital Marketing & Advertising' => [
                'Meta Ads', 'Google Ads', 'TikTok Ads', 'Paid social campaigns',
                'Lead generation campaigns', 'Campaign optimization', 'Performance tracking',
                'Digital marketing strategy',
            ],
            'Brand & Creative' => [
                'Brand positioning', 'Campaign strategy', 'Creative direction', 'Graphic design',
                'Motion graphics', 'Visual storytelling', 'Marketing collateral',
            ],
            'Corporate & Event Media' => [
                'Event photography', 'Event videography', 'Corporate event coverage',
                'Conference coverage', 'Promotional event content', 'Same-day and fast-turnaround social content',
            ],
            'Other Creative Services' => [
                'Podcast production', 'Podcast creative direction', 'Video editing',
                'Social media clips', 'Thumbnail design', 'Marketing consultancy',
            ],
            'Digital Experiences' => [
                'Website development', 'Business websites', 'Corporate websites', 'Landing pages',
                'E-commerce', 'Web applications', 'Custom business systems', 'Mobile applications',
                'Website maintenance',
            ],
        ];

        $categoryIndex = 0;
        foreach ($categories as $categoryName => $services) {
            $category = ServiceCategory::updateOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName, 'sort_order' => $categoryIndex, 'is_visible' => true]
            );

            foreach ($services as $serviceIndex => $serviceName) {
                $service = Service::where('name', $serviceName)->first();

                if (! $service) {
                    $slug = Str::slug($serviceName);

                    if (Service::where('slug', $slug)->exists()) {
                        $slug = Str::slug($categoryName.'-'.$serviceName);
                    }

                    $service = new Service(['slug' => $slug]);
                }

                $service->name = $serviceName;
                $service->service_category_id = $category->id;
                $service->sort_order = $serviceIndex;
                $service->is_visible = true;
                $service->save();
            }

            $categoryIndex++;
        }
    }
}