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
        // Change enum to string
        // DB::statement works best for avoiding doctrine dependencies for enum changes
        // Fix for cross-database compatibility (MySQL vs PgSQL)
        $driver = \Illuminate\Support\Facades\DB::getDriverName();

        if ($driver === 'pgsql') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE courses DROP CONSTRAINT IF EXISTS courses_level_check");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE courses ALTER COLUMN level DROP DEFAULT");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE courses ALTER COLUMN level TYPE VARCHAR(255)");
        } else {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE courses MODIFY COLUMN level VARCHAR(255) DEFAULT 'beginner'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = \Illuminate\Support\Facades\DB::getDriverName();

        if ($driver === 'pgsql') {
             // Postgres doesn't allow easy revert to enum without custom types, fallback to string
             \Illuminate\Support\Facades\DB::statement("ALTER TABLE courses ALTER COLUMN level DROP DEFAULT");
             \Illuminate\Support\Facades\DB::statement("ALTER TABLE courses ALTER COLUMN level TYPE VARCHAR(255)");
        } else {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE courses MODIFY COLUMN level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner'");
        }
    }
};
