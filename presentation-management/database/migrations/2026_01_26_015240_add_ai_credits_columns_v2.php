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
        // Add columns if they don't exist
        if (!Schema::hasColumn('users', 'ai_credits')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('ai_credits')->default(0)->after('balance');
            });
        }
        
        if (!Schema::hasColumn('courses', 'ai_credits')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->integer('ai_credits')->default(0)->after('price');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'ai_credits')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('ai_credits');
            });
        }
        
        if (Schema::hasColumn('courses', 'ai_credits')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('ai_credits');
            });
        }
    }
};
