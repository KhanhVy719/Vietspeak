<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assignment;

class StudentApiController extends Controller
{
    /**
     * Get student profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar_url' => $user->avatar_url,
                'balance' => $user->balance, // Số dư ví
                'ai_credits' => $user->ai_credits ?? 0, // Lượt AI
                'created_at' => $user->created_at->format('d/m/Y'),
            ]
        ]);
    }

    /**
     * Get student's classes
     */
    public function classes(Request $request)
    {
        $user = $request->user();
        // Load groups của user và members của group đó
        $user->load('groups.users');
        
        $classes = $user->studyingClassrooms()->get();

        return response()->json([
            'success' => true,
            'data' => $classes->map(function($class) use ($user) {
                // Find group in this class
                $myGroup = $user->groups->where('classroom_id', $class->id)->first();
                
                $groupData = null;
                if ($myGroup) {
                    $groupData = [
                        'id' => $myGroup->id,
                        'name' => $myGroup->name,
                        'members' => $myGroup->users->map(function($u) {
                            // Calculate stats for other members
                            
                            // 1. Avg Score
                            $avgScore = \App\Models\Grade::whereHas('submission', function($q) use ($u) {
                                $q->where('user_id', $u->id);
                            })->avg('score');

                            // 2. Completed Assignments
                            $completedCount = \App\Models\Submission::where('user_id', $u->id)->count();

                            // 3. Courses
                            $coursesCount = $u->enrolledCourses()->where('courses.status', 'active')->count();

                            return [
                                'id' => $u->id,
                                'name' => $u->name,
                                'avatar_url' => $u->avatar_url,
                                'stats' => [
                                    'avg_score' => $avgScore ? round($avgScore, 1) : 0,
                                    'completed_assignments' => $completedCount,
                                    'courses_count' => $coursesCount
                                ]
                            ];
                        })
                    ];
                }

                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'description' => $class->description,
                    'my_group' => $groupData
                ];
            })
        ]);
    }

    /**
     * Get student's assignments
     */
    public function assignments(Request $request)
    {
        $user = $request->user();
        $classroomIds = $user->studyingClassrooms()->pluck('classrooms.id');
        
        $assignments = Assignment::whereIn('classroom_id', $classroomIds)
            ->with('classroom')
            ->orderBy('due_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $assignments->map(function($assignment) use ($user) {
                // Check if submitted
                $submission = \App\Models\Submission::where('assignment_id', $assignment->id)
                    ->where('user_id', $user->id)
                    ->with('grade')
                    ->first();

                // Determine status
                $status = 'pending'; // Chưa nộp
                if ($submission) {
                    $status = 'submitted'; // Đã nộp
                    if ($submission->grade) {
                        $status = 'graded'; // Đã chấm
                    }
                } elseif ($assignment->due_date < now()) {
                    $status = 'overdue'; // Quá hạn
                }

                return [
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'description' => $assignment->description,
                    'due_date' => $assignment->due_date->format('d/m/Y H:i'),
                    'classroom' => [
                        'id' => $assignment->classroom_id,
                        'name' => $assignment->classroom->name ?? 'N/A',
                    ],
                    'status' => $status,
                    'submitted_at' => $submission ? $submission->created_at->format('d/m/Y H:i') : null,
                    'grade' => $submission && $submission->grade ? [
                        'score' => $submission->grade->score,
                        'comment' => $submission->grade->comment
                    ] : null
                ];
            })
        ]);
    }

    /**
     * Get student's grades
     */
    public function grades(Request $request)
    {
        $user = $request->user();
        
        $grades = \App\Models\Grade::whereHas('submission', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['submission.assignment.classroom'])->orderBy('graded_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $grades->map(function($grade) {
                return [
                    'id' => $grade->id,
                    'score' => $grade->score,
                    'comment' => $grade->comment,
                    'graded_at' => $grade->graded_at->format('d/m/Y H:i'),
                    'assignment' => [
                        'title' => $grade->submission->assignment->title,
                        'classroom' => $grade->submission->assignment->classroom->name,
                    ]
                ];
            })
        ]);
    }

    /**
     * Get student learning progress
     */
    public function progress(Request $request)
    {
        $user = $request->user();
        
        // Get all classes
        $classroomIds = $user->studyingClassrooms()->pluck('classrooms.id');
        
        // Get assignments and submissions
        $totalAssignments = \App\Models\Assignment::whereIn('classroom_id', $classroomIds)->count();
        $completedAssignments = \App\Models\Submission::where('user_id', $user->id)->count();
        
        // Get average grade
        $grades = \App\Models\Grade::whereHas('submission', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->get();
        
        $averageScore = $grades->count() > 0 ? $grades->avg('score') : 0;
        
        // Get enrolled courses
        $totalCourses = $user->enrolledCourses()->where('courses.status', 'active')->count();
        
        return response()->json([
            'success' => true,
            'data' => [
                'total_classes' => $classroomIds->count(),
                'total_courses' => $totalCourses,
                'total_assignments' => $totalAssignments,
                'completed_assignments' => $completedAssignments,
                'pending_assignments' => $totalAssignments - $completedAssignments,
                'average_score' => round($averageScore, 1),
                'completion_rate' => $totalAssignments > 0 ? round(($completedAssignments / $totalAssignments) * 100, 1) : 0,
            ]
        ]);
    }

    /**
     * Get student's enrolled courses
     */
    public function courses(Request $request)
    {
        try {
            $user = $request->user();
            
            $courses = $user->enrolledCourses()
                // Explicitly select course columns to avoid ambiguity with pivot columns if any
                ->select('courses.*')
                ->where('courses.status', 'active')
                ->get()
                ->map(function($course) {
                    return [
                        'id' => $course->id,
                        'name' => $course->name,
                        'code' => $course->code,
                        'description' => $course->description,
                        'instructor' => $course->instructor,
                        'duration' => $course->duration,
                        'level' => $course->level,
                        'progress' => $course->pivot->progress ?? 0,
                        'enrolled_at' => $course->pivot->enrolled_at ? date('d/m/Y', strtotime($course->pivot->enrolled_at)) : null,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $courses
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
