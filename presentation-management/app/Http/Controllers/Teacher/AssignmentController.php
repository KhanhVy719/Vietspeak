<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssignmentRequest;
use App\Models\Assignment;
use App\Models\Classroom;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments (only created by teacher)
     */
    public function index()
    {
        $user = auth()->user();

        $assignments = Assignment::where('created_by', $user->id)
            ->with(['classroom', 'submissions'])
            ->withCount('submissions')
            ->latest()
            ->paginate(20);

        return view('teacher.assignments.index', compact('assignments'));
    }

    /**
     * Show the form for creating a new assignment
     */
    public function create()
    {
        $this->authorize('create', Assignment::class);

        $user = auth()->user();
        $classrooms = $user->teachingClassrooms;

        return view('teacher.assignments.create', compact('classrooms'));
    }

    /**
     * Store a newly created assignment
     */
    public function store(StoreAssignmentRequest $request)
    {
        $assignment = Assignment::create([
            'classroom_id' => $request->classroom_id,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('teacher.assignments.show', $assignment)
            ->with('success', 'Tạo bài tập thành công!');
    }

    /**
     * Display the specified assignment with submissions
     */
    public function show(Assignment $assignment)
    {
        $this->authorize('view', $assignment);

        $assignment->load([
            'classroom.students',
            'submissions.student',
            'submissions.grade'
        ]);

        return view('teacher.assignments.show', compact('assignment'));
    }

    /**
     * Show the form for editing the specified assignment
     */
    public function edit(Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        $user = auth()->user();
        $classrooms = $user->teachingClassrooms;

        return view('teacher.assignments.edit', compact('assignment', 'classrooms'));
    }

    /**
     * Update the specified assignment
     */
    public function update(StoreAssignmentRequest $request, Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        $assignment->update($request->validated());

        return redirect()
            ->route('teacher.assignments.show', $assignment)
            ->with('success', 'Cập nhật bài tập thành công!');
    }

    /**
     * Remove the specified assignment
     */
    public function destroy(Assignment $assignment)
    {
        $this->authorize('delete', $assignment);

        $assignment->delete();

        return redirect()
            ->route('teacher.assignments.index')
            ->with('success', 'Xóa bài tập thành công!');
    }
}
