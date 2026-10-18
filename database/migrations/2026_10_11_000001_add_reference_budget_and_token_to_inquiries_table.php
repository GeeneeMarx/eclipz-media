<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    /**
     * Additive only: nothing is dropped or rewritten.
     * - reference: public Inquiry ID (EZ-YYYY-XXXXXX), unique at database level
     * - budget: optional budget band key (labels live in config/inquiry.php)
     * - submission_token: one-time token from the form, unique, so a double
     *   submit can never create two records
     */
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->string('reference', 20)->nullable()->unique()->after('id');
            $table->string('budget', 50)->nullable()->after('service_requested');
            $table->string('submission_token', 64)->nullable()->unique()->after('status');
        });

        // Give every enquiry that already exists its own reference.
        $used = [];

        foreach (DB::table('inquiries')->whereNull('reference')->orderBy('id')->get() as $row) {
            $createdAt = $row->created_at ? Carbon::parse($row->created_at, 'UTC') : Carbon::now('UTC');
            $year = $createdAt->setTimezone('Africa/Lagos')->format('Y');

            do {
                $suffix = '';
                for ($i = 0; $i < 6; $i++) {
                    $suffix .= self::ALPHABET[random_int(0, strlen(self::ALPHABET) - 1)];
                }
                $reference = "EZ-{$year}-{$suffix}";
            } while (isset($used[$reference]) || DB::table('inquiries')->where('reference', $reference)->exists());

            $used[$reference] = true;

            DB::table('inquiries')->where('id', $row->id)->update(['reference' => $reference]);
        }
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropUnique(['reference']);
            $table->dropUnique(['submission_token']);
            $table->dropColumn(['reference', 'budget', 'submission_token']);
        });
    }
};