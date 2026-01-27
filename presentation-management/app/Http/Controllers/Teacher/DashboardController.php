<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Classroom;

class DashboardController extends Controller
{
    /**
     * Hiển thị dashboard cho giáo viên
     */
    public function index()
    {
        $user = auth()->user();

        // Lấy các lớp mà GV đang phụ trách
        $classrooms = $user->teachingClassrooms()
            ->withCount(['students', 'assignments'])
            ->with(['assignments' => function ($query) {
                $query->latest()->take(5);
            }])
            ->get();

        // Thống kê
        $stats = [
            'total_classrooms' => $classrooms->count(),
            'total_students' => $classrooms->sum('students_count'),
            'total_assignments' => $user->createdAssignments()->count(),
            'pending_grades' => $user->createdAssignments()
                ->with('submissions')
                ->get()
                ->sum(function ($assignment) {
                    return $assignment->submissions->filter(function ($submission) {
                        return !$submission->isGraded();
                    })->count();
                }),
        ];

        return view('teacher.dashboard', compact('classrooms', 'stats'));
    }
}
