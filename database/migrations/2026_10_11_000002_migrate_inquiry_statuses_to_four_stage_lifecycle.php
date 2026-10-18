<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Maps the old six-status values onto the four-status lifecycle
     * (New, Under Review, Responded, Closed) so existing rows still load.
     * Any value left unmapped would make the status enum throw.
     */
    public function up(): void
    {
        $map = [
            'contacted' => 'responded',
            'consultation' => 'responded',
            'proposal' => 'responded',
            'won' => 'closed',
            'lost' => 'closed',
        ];

        foreach ($map as $old => $new) {
            DB::table('inquiries')->where('status', $old)->update(['status' => $new]);
        }
    }

    public function down(): void
    {
        // Intentionally empty: the old values cannot be restored exactly.
        // To roll back fully, restore the database backup taken before this stage.
    }
};