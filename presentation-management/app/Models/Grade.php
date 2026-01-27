<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'score',
        'comment',
        'graded_by',
        'graded_at',
    ];

    protected $casts = [
        'score' => 'decimal:1',
        'graded_at' => 'datetime',
    ];

    /**
     * Bài nộp được chấm
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    /**
     * Giáo viên chấm bài
     */
    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    /**
     * Lấy phân loại điểm
     */
    public function getScoreCategory(): string
    {
        if ($this->score >= 9.0) {
            return 'Xuất sắc';
        } elseif ($this->score >= 8.0) {
            return 'Giỏi';
        } elseif ($this->score >= 6.5) {
            return 'Khá';
        } elseif ($this->score >= 5.0) {
            return 'Trung bình';
        } else {
            return 'Yếu';
        }
    }
}
