<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassroomRequest;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    /**
     * Display a listing of classrooms
     */
    public function index()
    {
        $this->authorize('viewAny', Classroom::class);

        $classrooms = Classroom::withCount(['students', 'teachers', 'assignments'])
            ->latest()
            ->paginate(20);

        return view('admin.classrooms.index', compact('classrooms'));
    }

    /**
     * Show the form for creating a new classroom
     */
    public function create()
    {
        $this->authorize('create', Classroom::class);

        return view('admin.classrooms.create');
    }

    /**
     * Store a newly created classroom
     */
    public function store(StoreClassroomRequest $request)
    {
        $classroom = Classroom::create($request->validated());

        return redirect()
            ->route('admin.classrooms.index')
            ->with('success', 'Tạo lớp học thành công!');
    }

    /**
     * Display the specified classroom
     */
    public function show(Classroom $classroom)
    {
        $this->authorize('view', $classroom);

        $classroom->load(['students', 'teachers', 'assignments.submissions.grade']);

        return view('admin.classrooms.show', compact('classroom'));
    }

    /**
     * Show the form for editing classroom
     */
    public function edit(Classroom $classroom)
    {
        $this->authorize('update', $classroom);

        return view('admin.classrooms.edit', compact('classroom'));
    }

    /**
     * Update the specified classroom
     */
    public function update(StoreClassroomRequest $request, Classroom $classroom)
    {
        $this->authorize('update', $classroom);

        $classroom->update($request->validated());

        return redirect()
            ->route('admin.classrooms.index')
            ->with('success', 'Cập nhật lớp học thành công!');
    }

    /**
     * Remove the specified classroom
     */
    public function destroy(Classroom $classroom)
    {
        $this->authorize('delete', $classroom);

        $classroom->delete();

        return redirect()
            ->route('admin.classrooms.index')
            ->with('success', 'Xóa lớp học thành công!');
    }

    /**
     * Show form to manage classroom members (students and teachers)
     */
    public function manageMembers(Classroom $classroom)
    {
        $this->authorize('manageMembers', $classroom);

        $classroom->load(['students', 'teachers']);

        // Lấy danh sách students chưa có trong lớp
        $availableStudents = User::role('student')
            ->whereNotIn('id', $classroom->students->pluck('id'))
            ->get();

        // Lấy danh sách teachers chưa có trong lớp
        $availableTeachers = User::role('teacher')
            ->whereNotIn('id', $classroom->teachers->pluck('id'))
            ->get();

        return view('admin.classrooms.manage-members', compact(
            'classroom',
            'availableStudents',
            'availableTeachers'
        ));
    }

    /**
     * Add a student to classroom
     */
    public function addStudent(Request $request, Classroom $classroom)
    {
        $this->authorize('manageMembers', $classroom);

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $student = User::findOrFail($request->user_id);

        if (!$student->hasRole('student')) {
            return back()->with('error', 'Người dùng này không phải học sinh!');
        }

        $classroom->students()->attach($student->id, ['type' => 'student']);

        return back()->with('success', 'Thêm học sinh vào lớp thành công!');
    }

    /**
     * Remove a student from classroom
     */
    public function removeStudent(Classroom $classroom, User $student)
    {
        $this->authorize('manageMembers', $classroom);

        $classroom->students()->detach($student->id);

        return back()->with('success', 'Xóa học sinh khỏi lớp thành công!');
    }

    /**
     * Add a teacher to classroom
     */
    public function addTeacher(Request $request, Classroom $classroom)
    {
        $this->authorize('manageMembers', $classroom);

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $teacher = User::findOrFail($request->user_id);

        if (!$teacher->hasRole('teacher')) {
            return back()->with('error', 'Người dùng này không phải giáo viên!');
        }

        $classroom->teachers()->attach($teacher->id, ['type' => 'teacher']);

        return back()->with('success', 'Gán giáo viên vào lớp thành công!');
    }

    /**
     * Remove a teacher from classroom
     */
    public function removeTeacher(Classroom $classroom, User $teacher)
    {
        $this->authorize('manageMembers', $classroom);

        $classroom->teachers()->detach($teacher->id);

        return back()->with('success', 'Xóa giáo viên khỏi lớp thành công!');
    }
}
