<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Additive only.
     * - contact_settings.inquiry_notification_email: private address that
     *   receives new-enquiry notifications (never shown publicly)
     * - inquiries.*: whether each email was accepted by the mail server, or
     *   a short non-sensitive reason it failed. "Accepted" is not the same
     *   as "received in the inbox".
     */
    public function up(): void
    {
        Schema::table('contact_settings', function (Blueprint $table) {
            $table->string('inquiry_notification_email')->nullable()->after('email');
        });

        Schema::table('inquiries', function (Blueprint $table) {
            $table->timestamp('admin_notified_at')->nullable()->after('submission_token');
            $table->string('admin_notification_error', 255)->nullable()->after('admin_notified_at');
            $table->timestamp('visitor_confirmed_at')->nullable()->after('admin_notification_error');
            $table->string('visitor_confirmation_error', 255)->nullable()->after('visitor_confirmed_at');
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn([
                'admin_notified_at',
                'admin_notification_error',
                'visitor_confirmed_at',
                'visitor_confirmation_error',
            ]);
        });

        Schema::table('contact_settings', function (Blueprint $table) {
            $table->dropColumn('inquiry_notification_email');
        });
    }
};