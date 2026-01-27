<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens, \App\Traits\HasSnowflakeId;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'balance',
        'ai_credits',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be appended to arrays.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'avatar_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'integer',
            'ai_credits' => 'integer',
        ];
    }

    /**
     * Các lớp học mà user tham gia (cả GV và HS)
     */
    public function classrooms(): BelongsToMany
    {
        return $this->belongsToMany(Classroom::class, 'class_user')
            ->withPivot('type')
            ->withTimestamps();
    }

    /**
     * Các lớp mà user làm giáo viên
     */
    public function teachingClassrooms(): BelongsToMany
    {
        return $this->belongsToMany(Classroom::class, 'class_user')
            ->wherePivot('type', 'teacher')
            ->withTimestamps();
    }

    /**
     * Các lớp mà user là học sinh
     */
    public function studyingClassrooms(): BelongsToMany
    {
        return $this->belongsToMany(Classroom::class, 'class_user')
            ->wherePivot('type', 'student')
            ->withTimestamps();
    }

    /**
     * Các khóa học mà user đang tham gia
     */
    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_user')
            ->withPivot('enrolled_at', 'status', 'progress')
            ->withTimestamps();
    }

    /**
     * Bài tập do user tạo (giáo viên)
     */
    public function createdAssignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'created_by');
    }

    /**
     * Bài nộp của user (học sinh)
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Điểm do user chấm (giáo viên)
     */
    /**
     * Điểm do user chấm (giáo viên)
     */
    public function gradedSubmissions(): HasMany
    {
        return $this->hasMany(Grade::class, 'graded_by');
    }

    /**
     * Các nhóm/tổ mà user tham gia
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Kiểm tra user có phải admin không
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Kiểm tra user có phải giáo viên không
     */
    public function isTeacher(): bool
    {
        return $this->hasRole('teacher');
    }

    /**
     * Kiểm tra user có phải học sinh không
     */
    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute()
    {
        return $this->avatar 
            ? asset('storage/' . $this->avatar)
            : null;
    }
}
