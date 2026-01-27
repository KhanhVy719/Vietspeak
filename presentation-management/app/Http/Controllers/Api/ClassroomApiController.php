<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClassroomApiController extends Controller
{
    /**
     * Get classroom details
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        // Get classroom with relationships
        $classroom = $user->studyingClassrooms()
            ->with(['teachers', 'assignments.submissions' => function($q) use ($user) {
                $q->where('user_id', $user->id);
            }])
            ->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $classroom->id,
                'name' => $classroom->name,
                'description' => $classroom->description,
                'teachers' => $classroom->teachers->map(fn($t) => [
                    'id' => $t->id,
                    'name' => $t->name,
                ]),
                'students_count' => $classroom->students()->count(),
                'assignments' => $classroom->assignments->map(function($assignment) use ($user) {
                    $submission = $assignment->submissions->first();
                    return [
                        'id' => $assignment->id,
                        'title' => $assignment->title,
                        'description' => $assignment->description,
                        'due_date' => $assignment->due_date->format('d/m/Y H:i'),
                        'status' => $submission ? 'submitted' : 'pending',
                        'submitted_at' => $submission ? $submission->submitted_at->format('d/m/Y H:i') : null,
                        'grade' => $submission && $submission->grade ? [
                            'score' => $submission->grade->score,
                            'comment' => $submission->grade->comment,
                        ] : null,
                    ];
                }),
            ]
        ]);
    }
}
