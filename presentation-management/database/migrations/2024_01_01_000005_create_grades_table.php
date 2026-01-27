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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->onDelete('cascade');
            $table->decimal('score', 3, 1); // Điểm 0-10, 1 chữ số thập phân
            $table->text('comment')->nullable(); // Nhận xét của giáo viên
            $table->foreignId('graded_by')->constrained('users'); // Giáo viên chấm
            $table->dateTime('graded_at');
            $table->timestamps();

            // Một bài nộp chỉ có một lần chấm điểm
            $table->unique('submission_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
