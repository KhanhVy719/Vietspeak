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
        Schema::create('ai_analysis_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['audio', 'image', 'video']); // Analysis type
            $table->string('file_path')->nullable(); // Optional: store file path
            $table->integer('cost')->default(5000); // Cost in VND
            $table->text('prompt')->nullable(); // AI prompt sent
            $table->text('result')->nullable(); // AI response
            $table->string('model')->default('gemini-2.0-flash-exp'); // AI model used
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_analysis_history');
    }
};
