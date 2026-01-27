<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of courses
     */
    public function index()
    {
        $courses = Course::withCount('students')->latest()->paginate(20);
        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course
     */
    public function create()
    {
        return view('admin.courses.create');
    }

    /**
     * Store a newly created course
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses',
            'description' => 'nullable|string',
            'instructor' => 'nullable|string',
            'duration' => 'nullable|string',
            'level' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'ai_credits' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        Course::create($validated);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Tạo khóa học thành công!');
    }

    /**
     * Display the specified course
     */
    public function show(Course $course)
    {
        $course->load('students');
        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the course
     */
    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    /**
     * Update the specified course
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses,code,' . $course->id,
            'description' => 'nullable|string',
            'instructor' => 'nullable|string',
            'duration' => 'nullable|string',
            'level' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'ai_credits' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $course->update($validated);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Cập nhật khóa học thành công!');
    }

    /**
     * Remove the specified course
     */
    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Xóa khóa học thành công!');
    }

    /**
     * Show form to manage course students
     */
    public function manageStudents(Course $course)
    {
        $course->load('students');
        
        $availableStudents = User::role('student')
            ->whereNotIn('id', $course->students->pluck('id'))
            ->get();

        return view('admin.courses.manage-students', compact('course', 'availableStudents'));
    }

    /**
     * Add a student to course
     */
    public function addStudent(Request $request, Course $course)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $student = User::findOrFail($request->user_id);

        if (!$student->hasRole('student')) {
            return back()->with('error', 'Người dùng này không phải học sinh!');
        }

        $course->students()->attach($student->id, [
            'enrolled_at' => now(),
            'status' => 'active',
            'progress' => 0,
        ]);

        // Grant AI Credits if any
        if ($course->ai_credits > 0) {
            $student->ai_credits += $course->ai_credits;
            $student->save();
        }

        return back()->with('success', 'Ghi danh học sinh vào khóa học thành công!');
    }

    /**
     * Remove a student from course
     */
    public function removeStudent(Course $course, User $student)
    {
        $course->students()->detach($student->id);

        return back()->with('success', 'Xóa học sinh khỏi khóa học thành công!');
    }
}
