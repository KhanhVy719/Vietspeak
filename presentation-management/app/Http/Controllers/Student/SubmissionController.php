<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubmissionRequest;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    /**
     * Show the form for creating a submission
     */
    public function create(Assignment $assignment)
    {
        $this->authorize('view', $assignment);

        $user = auth()->user();

        // Kiểm tra đã nộp chưa
        $existingSubmission = $assignment->submissions()
            ->where('user_id', $user->id)
            ->first();

        if ($existingSubmission) {
            return redirect()
                ->route('student.submissions.show', $existingSubmission)
                ->with('info', 'Bạn đã nộp bài tập này rồi!');
        }

        return view('student.submissions.create', compact('assignment'));
    }

    /**
     * Store a newly created submission
     */
    public function store(StoreSubmissionRequest $request)
    {
        $user = auth()->user();
        $assignment = Assignment::findOrFail($request->assignment_id);

        $this->authorize('view', $assignment);

        // Kiểm tra đã nộp chưa
        $existingSubmission = $assignment->submissions()
            ->where('user_id', $user->id)
            ->first();

        if ($existingSubmission) {
            return redirect()
                ->route('student.assignments.show', $assignment)
                ->with('error', 'Bạn đã nộp bài tập này rồi!');
        }

        // Upload file vào private storage
        $file = $request->file('file');
        $fileName = time() . '_' . $user->id . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('submissions', $fileName, 'private');

        // Tạo submission
        $submission = Submission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $user->id,
            'file_path' => $filePath,
            'note' => $request->note,
            'submitted_at' => now(),
        ]);

        return redirect()
            ->route('student.submissions.show', $submission)
            ->with('success', 'Nộp bài thành công!');
    }

    /**
     * Display the specified submission
     */
    public function show(Submission $submission)
    {
        $this->authorize('view', $submission);

        $submission->load(['assignment.classroom', 'grade.grader']);

        return view('student.submissions.show', compact('submission'));
    }

    /**
     * Show the form for editing submission (resubmit)
     */
    public function edit(Submission $submission)
    {
        $this->authorize('update', $submission);

        // Không cho phép nộp lại nếu đã được chấm điểm
        if ($submission->isGraded()) {
            return redirect()
                ->route('student.submissions.show', $submission)
                ->with('error', 'Bài nộp đã được chấm điểm, không thể nộp lại!');
        }

        $submission->load('assignment');

        return view('student.submissions.edit', compact('submission'));
    }

    /**
     * Update the submission (resubmit)
     */
    public function update(StoreSubmissionRequest $request, Submission $submission)
    {
        $this->authorize('update', $submission);

        // Không cho phép nộp lại nếu đã được chấm điểm
        if ($submission->isGraded()) {
            return redirect()
                ->route('student.submissions.show', $submission)
                ->with('error', 'Bài nộp đã được chấm điểm, không thể nộp lại!');
        }

        // Xóa file cũ
        if (Storage::disk('private')->exists($submission->file_path)) {
            Storage::disk('private')->delete($submission->file_path);
        }

        // Upload file mới
        $file = $request->file('file');
        $fileName = time() . '_' . auth()->id() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('submissions', $fileName, 'private');

        // Cập nhật submission
        $submission->update([
            'file_path' => $filePath,
            'note' => $request->note,
            'submitted_at' => now(),
        ]);

        return redirect()
            ->route('student.submissions.show', $submission)
            ->with('success', 'Nộp lại bài thành công!');
    }
}
