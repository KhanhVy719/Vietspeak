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
        // Try/Catch raw SQL allows us to attempt adding columns even if things are messy
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users ADD COLUMN ai_credits INT DEFAULT 0");
        } catch (\Exception $e) {
            // Log or ignore if column exists
        }

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE courses ADD COLUMN ai_credits INT DEFAULT 0");
        } catch (\Exception $e) {
            // Log or ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users DROP COLUMN ai_credits");
        } catch (\Exception $e) {}
        
        try {
             \Illuminate\Support\Facades\DB::statement("ALTER TABLE courses DROP COLUMN ai_credits");
        } catch (\Exception $e) {}
    }
};
