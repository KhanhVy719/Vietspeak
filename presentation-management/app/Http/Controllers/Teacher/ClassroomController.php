<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Classroom;

class ClassroomController extends Controller
{
    /**
     * Display a listing of classrooms (only teaching ones)
     */
    public function index()
    {
        $user = auth()->user();

        $classrooms = $user->teachingClassrooms()
            ->withCount(['students', 'assignments'])
            ->latest()
            ->paginate(20);

        return view('teacher.classrooms.index', compact('classrooms'));
    }

    /**
     * Display the specified classroom with students and grades
     */
    public function show(Classroom $classroom)
    {
        $this->authorize('view', $classroom);

        // Load students với các bài nộp và điểm
        $classroom->load([
            'students',
            'groups.users', // Load nhóm và thành viên
            'assignments.submissions.grade',
            'assignments.submissions.student'
        ]);

        // Tính điểm trung bình cho từng học sinh
        $studentGrades = [];
        foreach ($classroom->students as $student) {
            $submissions = $classroom->assignments->flatMap(function ($assignment) use ($student) {
                return $assignment->submissions->where('user_id', $student->id);
            });

            $gradedSubmissions = $submissions->filter(function ($submission) {
                return $submission->isGraded();
            });

            $averageScore = $gradedSubmissions->count() > 0
                ? $gradedSubmissions->avg('grade.score')
                : null;

            $studentGrades[$student->id] = [
                'total_submissions' => $submissions->count(),
                'graded_submissions' => $gradedSubmissions->count(),
                'average_score' => $averageScore,
            ];
        }

        return view('teacher.classrooms.show', compact('classroom', 'studentGrades'));
    }
}
