<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGradeRequest;
use App\Models\Grade;
use App\Models\Submission;

class SubmissionController extends Controller
{
    /**
     * Display the specified submission for grading
     */
    public function show(Submission $submission)
    {
        $this->authorize('view', $submission);

        $submission->load(['student', 'assignment.classroom', 'grade']);

        return view('teacher.submissions.show', compact('submission'));
    }

    /**
     * Store or update a grade for submission
     */
    public function grade(StoreGradeRequest $request, Submission $submission)
    {
        $this->authorize('grade', $submission);

        // Cập nhật hoặc tạo mới grade
        Grade::updateOrCreate(
            ['submission_id' => $submission->id],
            [
                'score' => $request->score,
                'comment' => $request->comment,
                'graded_by' => auth()->id(),
                'graded_at' => now(),
            ]
        );

        return back()->with('success', 'Chấm điểm thành công!');
    }
}
