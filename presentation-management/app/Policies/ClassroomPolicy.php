<?php

namespace App\Policies;

use App\Models\Classroom;
use App\Models\User;

class ClassroomPolicy
{
    /**
     * Admin có toàn quyền
     */
    private function isAdmin(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Xem danh sách lớp
     */
    public function viewAny(User $user): bool
    {
        // Admin, Teacher, Student đều có thể xem (nhưng filter khác nhau)
        return true;
    }

    /**
     * Xem chi tiết lớp
     */
    public function view(User $user, Classroom $classroom): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        // GV hoặc HS thuộc lớp này
        return $classroom->members()->where('users.id', $user->id)->exists();
    }

    /**
     * Tạo lớp mới
     */
    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Cập nhật lớp
     */
    public function update(User $user, Classroom $classroom): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Xóa lớp
     */
    public function delete(User $user, Classroom $classroom): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Quản lý thành viên lớp (thêm/xóa GV, HS)
     */
    public function manageMembers(User $user, Classroom $classroom): bool
    {
        return $this->isAdmin($user);
    }
}
