<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Học sinh trong lớp
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_user')
            ->wherePivot('type', 'student')
            ->withTimestamps();
    }

    /**
     * Giáo viên phụ trách lớp
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_user')
            ->wherePivot('type', 'teacher')
            ->withTimestamps();
    }

    /**
     * Tất cả thành viên (cả GV và HS)
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_user')
            ->withPivot('type')
            ->withTimestamps();
    }

    /**
     * Bài tập của lớp
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Các nhóm/tổ trong lớp
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    /**
     * Kiểm tra user có phải là giáo viên của lớp này không
     */
    public function hasTeacher(User $user): bool
    {
        return $this->teachers()->where('users.id', $user->id)->exists();
    }

    /**
     * Kiểm tra user có phải là học sinh của lớp này không
     */
    public function hasStudent(User $user): bool
    {
        return $this->students()->where('users.id', $user->id)->exists();
    }
}
