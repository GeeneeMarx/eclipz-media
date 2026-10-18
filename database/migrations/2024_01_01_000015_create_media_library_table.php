<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Standalone uploaded assets not tied to a specific project (logos, brand assets, etc.). */
    public function up(): void
    {
        Schema::create('media_library_items', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->string('label')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_library_items');
    }
};
