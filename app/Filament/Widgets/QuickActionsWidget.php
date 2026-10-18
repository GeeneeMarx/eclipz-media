<?php

namespace App\Filament\Widgets;

use App\Enums\InquiryStatus;
use App\Filament\Pages\CompanySettingsPage;
use App\Filament\Pages\ContactSettingsPage;
use App\Filament\Resources\ClientPartnerResource;
use App\Filament\Resources\InquiryResource;
use App\Filament\Resources\ProjectResource;
use App\Support\DashboardMetrics;
use Filament\Widgets\Widget;

class QuickActionsWidget extends Widget
{
    protected static string $view = 'filament.widgets.quick-actions';

    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    /**
     * Shortcuts to existing admin pages. A shortcut is only returned when its
     * destination exists and the signed-in admin is allowed to open it.
     *
     * @return array<int, array{label: string, description: string, url: string}>
     */
    public static function actions(): array
    {
        $counts = DashboardMetrics::inquiryCounts();
        $canReview = InquiryResource::canViewAny();

        $candidates = [
            [
                'allowed' => $canReview,
                'label' => 'Review enquiries',
                'description' => $counts['total'].' in total',
                'url' => fn () => InquiryResource::getUrl('index'),
            ],
            [
                'allowed' => $canReview && $counts['new'] > 0,
                'label' => 'Review new enquiries',
                'description' => $counts['new'].' waiting for a first review',
                'url' => fn () => InquiryResource::getUrl('index', [
                    'tableFilters' => ['status' => ['value' => InquiryStatus::New->value]],
                ]),
            ],
            [
                'allowed' => $canReview && $counts['email_problems'] > 0,
                'label' => 'Fix email problems',
                'description' => $counts['email_problems'].' with a failed email, ready to resend',
                'url' => fn () => InquiryResource::getUrl('index', [
                    'tableFilters' => ['failed_emails' => ['isActive' => true]],
                ]),
            ],
            [
                'allowed' => ProjectResource::canCreate(),
                'label' => 'Create a project',
                'description' => 'Add work to the portfolio',
                'url' => fn () => ProjectResource::getUrl('create'),
            ],
            [
                'allowed' => ProjectResource::canViewAny(),
                'label' => 'Manage projects',
                'description' => 'Publish, feature or edit projects',
                'url' => fn () => ProjectResource::getUrl('index'),
            ],
            [
                'allowed' => ClientPartnerResource::canViewAny(),
                'label' => 'Clients and partners',
                'description' => 'Logos, visibility and order',
                'url' => fn () => ClientPartnerResource::getUrl('index'),
            ],
            [
                'allowed' => ContactSettingsPage::canAccess(),
                'label' => 'Contact information',
                'description' => 'Emails, phone and WhatsApp',
                'url' => fn () => ContactSettingsPage::getUrl(),
            ],
            [
                'allowed' => CompanySettingsPage::canAccess(),
                'label' => 'Company settings',
                'description' => 'About, founder, mission and vision',
                'url' => fn () => CompanySettingsPage::getUrl(),
            ],
        ];

        return collect($candidates)
            ->filter(fn (array $candidate) => $candidate['allowed'])
            ->map(fn (array $candidate) => [
                'label' => $candidate['label'],
                'description' => $candidate['description'],
                'url' => ($candidate['url'])(),
            ])
            ->values()
            ->all();
    }

    protected function getViewData(): array
    {
        return ['actions' => static::actions()];
    }
}