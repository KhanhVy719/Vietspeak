<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Hoàng Việt Anh
            $table->string('initials', 2); // HA
            $table->string('title'); // CO-FOUNDER & BỘ PHẬN KỸ THUẬT AI
            $table->text('description');
            $table->string('avatar_color', 7)->default('#1a3a5f'); // Hex color
            $table->string('avatar')->nullable(); // Path to uploaded image
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
