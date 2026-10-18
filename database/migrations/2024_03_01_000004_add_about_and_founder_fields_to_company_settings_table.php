<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Extends the existing company_settings singleton rather than creating
     * a parallel "about content" table - reuses the established settings
     * pattern (see company/contact/seo settings).
     */
    public function up(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->string('short_description')->nullable()->after('tagline');
            $table->text('story')->nullable()->after('short_description');
            $table->text('what_we_do')->nullable()->after('story');
            $table->text('differentiator')->nullable()->after('what_we_do');
            $table->text('client_type_text')->nullable()->after('differentiator');

            $table->string('founder_name')->nullable()->after('vision');
            $table->string('founder_title')->nullable()->after('founder_name');
            $table->text('founder_bio')->nullable()->after('founder_title');
            $table->string('founder_specializations')->nullable()->after('founder_bio');
            $table->string('founder_quote')->nullable()->after('founder_specializations');
        });
    }

    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn([
                'short_description', 'story', 'what_we_do', 'differentiator', 'client_type_text',
                'founder_name', 'founder_title', 'founder_bio', 'founder_specializations', 'founder_quote',
            ]);
        });
    }
};