<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentApiController extends Controller
{
    /**
     * Submit assignment
     */
    public function submit(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
            'file' => 'nullable|file|max:10240', // Max 10MB
        ]);

        $assignment = Assignment::findOrFail($id);
        $user = $request->user();
        
        // Check if user is in the class
        if (!$user->studyingClassrooms()->where('classrooms.id', $assignment->classroom_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền nộp bài này'
            ], 403);
        }
        
        // Check if already submitted
        $existingSubmission = $assignment->submissions()->where('user_id', $user->id)->first();
        
        if ($existingSubmission) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã nộp bài này rồi'
            ], 400);
        }
        
        // Save file if exists
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('submissions', 'public');
        }
        
        // Create submission
        $submission = $assignment->submissions()->create([
            'user_id' => $user->id,
            'content' => $request->content,
            'file_path' => $filePath,
            'submitted_at' => now(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Nộp bài thành công',
            'submission' => [
                'id' => $submission->id,
                'submitted_at' => $submission->submitted_at->format('d/m/Y H:i'),
            ]
        ]);
    }
    
    /**
     * Get assignment details
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $assignment = Assignment::with(['classroom', 'submissions' => function($q) use ($user) {
            $q->where('user_id', $user->id)->with('grade');
        }])->findOrFail($id);
        
        // Check if user is in the class
        if (!$user->studyingClassrooms()->where('classrooms.id', $assignment->classroom_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xem bài này'
            ], 403);
        }
        
        $submission = $assignment->submissions->first();
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'description' => $assignment->description,
                'due_date' => $assignment->due_date->format('d/m/Y H:i'),
                'classroom' => [
                    'id' => $assignment->classroom->id,
                    'name' => $assignment->classroom->name,
                ],
                'submission' => $submission ? [
                    'id' => $submission->id,
                    'content' => $submission->content,
                    'file_path' => $submission->file_path,
                    'submitted_at' => $submission->submitted_at->format('d/m/Y H:i'),
                    'grade' => $submission->grade ? [
                        'score' => $submission->grade->score,
                        'comment' => $submission->grade->comment,
                        'graded_at' => $submission->grade->graded_at->format('d/m/Y H:i'),
                    ] : null,
                ] : null,
            ]
        ]);
    }
}
