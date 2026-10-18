<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('client')->index();
            $table->string('logo')->nullable();
            $table->foreignId('industry_id')->nullable()->constrained('industries')->nullOnDelete();
            $table->string('website')->nullable();
            $table->boolean('is_visible')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients_partners');
    }
};
