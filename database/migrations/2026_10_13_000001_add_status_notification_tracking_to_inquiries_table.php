<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Additive only. Records which status the visitor was last emailed about,
     * so one real change produces one email and repeats produce none.
     */
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->string('last_notified_status', 30)->nullable()->after('visitor_confirmation_error');
            $table->timestamp('status_notified_at')->nullable()->after('last_notified_status');
            $table->string('status_notification_error', 255)->nullable()->after('status_notified_at');
        });

        // Enquiries that already have a status were never emailed about it.
        // Treat that status as the baseline so nothing is emailed retroactively;
        // only later forward moves notify the visitor.
        DB::table('inquiries')
            ->where('status', '!=', 'new')
            ->update(['last_notified_status' => DB::raw('status')]);
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn(['last_notified_status', 'status_notified_at', 'status_notification_error']);
        });
    }
};