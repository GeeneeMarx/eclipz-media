<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds the fields Phase 4 needs without touching the original projects
     * migration, which has already run against live seeded data locally.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('client_partner_id')->nullable()->after('industry_id')
                ->constrained('clients_partners')->nullOnDelete();
            $table->string('short_description')->nullable()->after('description');
            $table->string('project_url')->nullable()->after('video_url');
            $table->timestamp('published_at')->nullable()->after('is_published');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('client_partner_id');
            $table->dropColumn(['short_description', 'project_url', 'published_at']);
        });
    }
};