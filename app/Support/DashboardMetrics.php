<?php

namespace App\Support;

use App\Enums\InquiryStatus;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

/**
 * Figures for the admin dashboard. Everything is counted from the database
 * when the page loads: nothing is stored, estimated or hardcoded.
 */
class DashboardMetrics
{
    /**
     * @return array{total: int, new: int, under_review: int, responded: int, closed: int, last_7_days: int, email_problems: int}
     */
    public static function inquiryCounts(): array
    {
        $byStatus = DB::table('inquiries')
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'total' => (int) $byStatus->sum(),
            'new' => (int) ($byStatus[InquiryStatus::New->value] ?? 0),
            'under_review' => (int) ($byStatus[InquiryStatus::UnderReview->value] ?? 0),
            'responded' => (int) ($byStatus[InquiryStatus::Responded->value] ?? 0),
            'closed' => (int) ($byStatus[InquiryStatus::Closed->value] ?? 0),
            'last_7_days' => (int) DB::table('inquiries')
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
            // Enquiries with at least one email that has a recorded failure.
            'email_problems' => (int) DB::table('inquiries')
                ->where(function ($query) {
                    $query->whereNotNull('admin_notification_error')
                        ->orWhereNotNull('visitor_confirmation_error')
                        ->orWhereNotNull('status_notification_error');
                })
                ->count(),
        ];
    }

    /**
     * @return array{published: int, draft: int}
     */
    public static function projectCounts(): array
    {
        return [
            'published' => Project::query()->where('is_published', true)->count(),
            'draft' => Project::query()->where('is_published', false)->count(),
        ];
    }
}