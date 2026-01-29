<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ai_analysis_history', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('updated_at');
            $table->index('expires_at'); // For efficient cleanup queries
        });

        // Set expires_at for existing records (30 days from created_at)
        DB::table('ai_analysis_history')
            ->whereNull('expires_at')
            ->update([
                'expires_at' => DB::raw("created_at + INTERVAL '30 days'")
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_analysis_history', function (Blueprint $table) {
            $table->dropIndex(['expires_at']);
            $table->dropColumn('expires_at');
        });
    }
};
