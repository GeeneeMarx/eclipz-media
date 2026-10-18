<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** 1:1 with projects where project_type = case_study. */
    public function up(): void
    {
        Schema::create('case_study_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->unique()->constrained('projects')->cascadeOnDelete();
            $table->text('challenge')->nullable();
            $table->text('strategy')->nullable();
            $table->text('execution')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_study_details');
    }
};
