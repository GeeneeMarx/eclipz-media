<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Presentation-only setting: adjusts a logo's visual weight on the
     * public Clients section without touching the uploaded file. Default
     * 1.00 = no adjustment. Not used for cropping/resizing the source image.
     */
    public function up(): void
    {
        Schema::table('clients_partners', function (Blueprint $table) {
            $table->decimal('display_scale', 3, 2)->default(1.00)->after('website');
        });
    }

    public function down(): void
    {
        Schema::table('clients_partners', function (Blueprint $table) {
            $table->dropColumn('display_scale');
        });
    }
};