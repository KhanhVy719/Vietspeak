<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Chỉ admin mới có quyền quản lý user
     */
    private function isAdmin(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Xem danh sách users
     */
    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Xem chi tiết user
     */
    public function view(User $user, User $model): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Tạo user mới
     */
    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Cập nhật user
     */
    public function update(User $user, User $model): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Xóa user
     */
    public function delete(User $user, User $model): bool
    {
        // Admin có thể xóa, nhưng không được xóa chính mình
        return $this->isAdmin($user) && $user->id !== $model->id;
    }

    /**
     * Gán role cho user
     */
    public function assignRole(User $user): bool
    {
        return $this->isAdmin($user);
    }
}
