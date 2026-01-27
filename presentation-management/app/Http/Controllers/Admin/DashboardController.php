<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Classroom;
use App\Models\Assignment;
use App\Models\Submission;

class DashboardController extends Controller
{
    /**
     * Hiển thị dashboard cho admin
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $stats = [
            'total_users' => User::count(),
            'total_teachers' => User::role('teacher')->count(),
            'total_students' => User::role('student')->count(),
            'total_classrooms' => Classroom::count(),
            'total_assignments' => Assignment::count(),
            'total_submissions' => Submission::count(),
            'graded_submissions' => Submission::has('grade')->count(),
        ];

        // Lấy hoạt động gần đây
        $recent_submissions = Submission::with(['student', 'assignment.classroom'])
            ->latest('submitted_at')
            ->take(10)
            ->get();

        $recent_assignments = Assignment::with(['classroom', 'creator'])
            ->latest('created_at')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_submissions', 'recent_assignments'));
    }
}
