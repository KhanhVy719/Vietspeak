<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    /**
     * Admin có toàn quyền
     */
    private function isAdmin(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Xem danh sách bài nộp
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Xem chi tiết bài nộp
     */
    public function view(User $user, Submission $submission): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        // HS chủ bài
        if ($submission->user_id === $user->id) {
            return true;
        }

        // GV của lớp
        $classroom = $submission->assignment->classroom;
        return $classroom->hasTeacher($user);
    }

    /**
     * Tạo bài nộp
     */
    public function create(User $user): bool
    {
        // Chỉ student hoặc admin
        return $this->isAdmin($user) || $user->hasRole('student');
    }

    /**
     * Cập nhật bài nộp (nộp lại)
     */
    public function update(User $user, Submission $submission): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        // Không cho phép nộp lại nếu đã được chấm điểm
        if ($submission->isGraded()) {
            return false;
        }

        // Chỉ HS chủ bài được nộp lại
        return $submission->user_id === $user->id;
    }

    /**
     * Xóa bài nộp
     */
    public function delete(User $user, Submission $submission): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        // HS có thể xóa bài nộp của mình (trước khi GV chấm)
        return $submission->user_id === $user->id && !$submission->isGraded();
    }

    /**
     * Chấm điểm bài nộp
     */
    public function grade(User $user, Submission $submission): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        // GV của lớp
        $classroom = $submission->assignment->classroom;
        return $classroom->hasTeacher($user);
    }

    /**
     * Tải file bài nộp
     */
    public function download(User $user, Submission $submission): bool
    {
        // Giống như view
        return $this->view($user, $submission);
    }
}
