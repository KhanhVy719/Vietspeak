<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Submission;

class DashboardController extends Controller
{
    /**
     * Hiển thị dashboard cho học sinh
     */
    public function index()
    {
        $user = auth()->user();

        // Lấy các lớp học sinh đang tham gia
        $classrooms = $user->studyingClassrooms()
            ->withCount('assignments')
            ->get();

        // Lấy các bài tập chưa nộp
        $pendingAssignments = Assignment::whereHas('classroom.students', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })
        ->whereDoesntHave('submissions', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with('classroom')
        ->where('due_date', '>', now())
        ->orderBy('due_date')
        ->take(10)
        ->get();

        // Thống kê
        $totalSubmissions = $user->submissions()->count();
        $gradedSubmissions = $user->submissions()->whereHas('grade')->count();
        
        // Tính điểm trung bình
        $averageScore = $user->submissions()
            ->whereHas('grade')
            ->with('grade')
            ->get()
            ->pluck('grade.score')
            ->avg();

        $stats = [
            'total_classrooms' => $classrooms->count(),
            'total_submissions' => $totalSubmissions,
            'graded_submissions' => $gradedSubmissions,
            'pending_assignments' => $pendingAssignments->count(),
            'average_score' => $averageScore ? round($averageScore, 2) : null,
        ];

        return view('student.dashboard', compact('classrooms', 'pendingAssignments', 'stats'));
    }
}
