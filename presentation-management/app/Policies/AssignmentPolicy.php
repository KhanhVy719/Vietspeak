<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;

class AssignmentPolicy
{
    /**
     * Admin có toàn quyền
     */
    private function isAdmin(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Xem danh sách bài tập
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Xem chi tiết bài tập
     */
    public function view(User $user, Assignment $assignment): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        $classroom = $assignment->classroom;

        // GV của lớp hoặc HS trong lớp
        return $classroom->members()->where('users.id', $user->id)->exists();
    }

    /**
     * Tạo bài tập mới
     */
    public function create(User $user): bool
    {
        // Admin hoặc Teacher
        return $this->isAdmin($user) || $user->hasRole('teacher');
    }

    /**
     * Kiểm tra có thể tạo bài cho lớp cụ thể
     */
    public function createForClassroom(User $user, $classroomId): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        // Teacher phải là GV của lớp đó
        return $user->teachingClassrooms()->where('classrooms.id', $classroomId)->exists();
    }

    /**
     * Cập nhật bài tập
     */
    public function update(User $user, Assignment $assignment): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        // Chỉ GV tạo bài mới được sửa
        return $assignment->created_by === $user->id;
    }

    /**
     * Xóa bài tập
     */
    public function delete(User $user, Assignment $assignment): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        // Chỉ GV tạo bài mới được xóa
        return $assignment->created_by === $user->id;
    }
}
