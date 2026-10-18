<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('service_category_id')->nullable()->after('pillar')
                ->constrained('service_categories')->nullOnDelete();
            $table->unsignedInteger('sort_order')->default(0)->after('service_category_id');
            $table->boolean('is_visible')->default(true)->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_category_id');
            $table->dropColumn(['sort_order', 'is_visible']);
        });
    }
};