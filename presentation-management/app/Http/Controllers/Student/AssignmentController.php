<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments for enrolled classrooms
     */
    public function index()
    {
        $user = auth()->user();

        // Lấy các bài tập từ các lớp học sinh tham gia
        $assignments = Assignment::whereHas('classroom.students', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })
        ->with(['classroom', 'submissions' => function ($query) use ($user) {
            $query->where('user_id', $user->id)->with('grade');
        }])
        ->latest('due_date')
        ->paginate(20);

        return view('student.assignments.index', compact('assignments'));
    }

    /**
     * Display the specified assignment
     */
    public function show(Assignment $assignment)
    {
        $this->authorize('view', $assignment);

        $user = auth()->user();

        // Lấy bài nộp của học sinh (nếu có)
        $submission = $assignment->submissions()
            ->where('user_id', $user->id)
            ->with('grade')
            ->first();

        $assignment->load('classroom');

        return view('student.assignments.show', compact('assignment', 'submission'));
    }
}
