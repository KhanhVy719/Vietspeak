<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'classroom_id',
        'title',
        'description',
        'due_date',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    /**
     * Lớp học mà bài tập thuộc về
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Giáo viên tạo bài tập
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Các bài nộp cho bài tập này
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Các điểm của bài tập (thông qua submissions)
     */
    public function grades(): HasManyThrough
    {
        return $this->hasManyThrough(Grade::class, Submission::class);
    }

    /**
     * Kiểm tra bài tập đã quá hạn chưa
     */
    public function isOverdue(): bool
    {
        return $this->due_date->isPast();
    }

    /**
     * Lấy bài nộp của một học sinh cụ thể
     */
    public function getSubmissionByStudent(User $student): ?Submission
    {
        return $this->submissions()->where('user_id', $student->id)->first();
    }
}
