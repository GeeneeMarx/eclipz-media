<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * project_type distinguishes the three content levels: "visual" (simple
     * showcase), "case_study" (deeper story, joins case_study_details),
     * "digital" (web/app development work).
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('industry_id')->nullable()->constrained('industries')->nullOnDelete();
            $table->string('project_type')->default('visual')->index();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('video_url')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_published')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
