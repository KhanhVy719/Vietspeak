<?php

Route::get('/fix-cache', function() {
    try {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        return "✅ Cache Cleared! Attempting to refresh autoload... (Please Restart Laragon if this doesn't work)";
    } catch (\Exception $e) {
        return "❌ Error: " . $e->getMessage();
    }
});



Route::get('/create-admin', function() {
    try {
        if (!\App\Models\User::where('email', 'admin@example.com')->exists()) {
            \App\Models\User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ]);
            return "✅ Admin Created Successfully!<br>Email: admin@example.com<br>Password: password";
        }
        return "ℹ️ Admin already exists!<br>Email: admin@example.com<br>Password: password";
    } catch (\Exception $e) {
        return "❌ Error: " . $e->getMessage();
    }
});





use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ClassroomController as AdminClassroomController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\ClassroomController as TeacherClassroomController;
use App\Http\Controllers\Teacher\AssignmentController as TeacherAssignmentController;
use App\Http\Controllers\Teacher\SubmissionController as TeacherSubmissionController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\AssignmentController as StudentAssignmentController;
use App\Http\Controllers\Student\SubmissionController as StudentSubmissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard redirect based on role
Route::get('/dashboard', function () {
    if (auth()->user()->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    } elseif (auth()->user()->hasRole('teacher')) {
        return redirect()->route('teacher.dashboard');
    } elseif (auth()->user()->hasRole('student')) {
        return redirect()->route('student.dashboard');
    }
    
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// File download route
Route::get('/downloads/submissions/{submission}', [DownloadController::class, 'download'])
    ->middleware('auth')
    ->name('downloads.submission');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // User management
    Route::resource('users', UserController::class);
    
    // Classroom management
    Route::resource('classrooms', AdminClassroomController::class);
    Route::get('classrooms/{classroom}/manage-members', [AdminClassroomController::class, 'manageMembers'])->name('classrooms.manage-members');
    Route::post('classrooms/{classroom}/add-teacher', [AdminClassroomController::class, 'addTeacher'])->name('classrooms.add-teacher');
    Route::delete('classrooms/{classroom}/teachers/{user}', [AdminClassroomController::class, 'removeTeacher'])->name('classrooms.remove-teacher');
    Route::post('classrooms/{classroom}/add-student', [AdminClassroomController::class, 'addStudent'])->name('classrooms.add-student');
    Route::delete('classrooms/{classroom}/students/{user}', [AdminClassroomController::class, 'removeStudent'])->name('classrooms.remove-student');
    
    // Course management
    Route::resource('courses', AdminCourseController::class);
    Route::get('courses/{course}/manage-students', [AdminCourseController::class, 'manageStudents'])->name('courses.manage-students');
    Route::post('courses/{course}/add-student', [AdminCourseController::class, 'addStudent'])->name('courses.add-student');
    Route::delete('courses/{course}/students/{user}', [AdminCourseController::class, 'removeStudent'])->name('courses.remove-student');

    // Settings
    Route::get('settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/test', [App\Http\Controllers\Admin\SettingController::class, 'testConnection'])->name('settings.test');
    
    // AI Configuration
    Route::get('settings/ai', [App\Http\Controllers\Admin\SettingController::class, 'aiConfig'])->name('settings.ai-config');
    Route::post('settings/ai', [App\Http\Controllers\Admin\SettingController::class, 'updateAiConfig'])->name('settings.update-ai-config');
    Route::post('settings/ai/test', [App\Http\Controllers\Admin\SettingController::class, 'testAiConnection'])->name('settings.test-ai-connection');
});

// Emergency route to force Gemini
Route::get('/force-gemini', function() {
    \App\Models\Setting::updateOrCreate(['key' => 'ai_provider'], ['value' => 'gemini']);
    return "Đã chuyển sang Gemini thành công! Giờ bạn hãy quay lại trang ai.html để test.";
});

// Teacher routes
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
    
    // Classroom views
    Route::get('/classrooms', [TeacherClassroomController::class, 'index'])->name('classrooms.index');
    Route::get('/classrooms/{classroom}', [TeacherClassroomController::class, 'show'])->name('classrooms.show');
    
    // Assignment management
    Route::resource('assignments', TeacherAssignmentController::class);
    
    // Submission grading
    // Submission grading
    Route::get('/submissions/{submission}', [TeacherSubmissionController::class, 'show'])->name('submissions.show');
    Route::post('/submissions/{submission}/grade', [TeacherSubmissionController::class, 'grade'])->name('submissions.grade');

    // Group management (Reusing Admin Controller for simplicity, or create Teacher specific one)
    // Assuming Teacher has permission to manage groups in their classrooms
    Route::post('classrooms/{classroom}/groups', [App\Http\Controllers\Admin\GroupController::class, 'store'])->name('groups.store');
    Route::delete('groups/{group}', [App\Http\Controllers\Admin\GroupController::class, 'destroy'])->name('groups.destroy');
    Route::post('groups/{group}/add-member', [App\Http\Controllers\Admin\GroupController::class, 'addMember'])->name('groups.add-member');
    Route::delete('groups/{group}/members/{user}', [App\Http\Controllers\Admin\GroupController::class, 'removeMember'])->name('groups.remove-member');
});

// Student routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    
    // View assignments
    Route::get('/assignments', [StudentAssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/{assignment}', [StudentAssignmentController::class, 'show'])->name('assignments.show');
    
    // Submit assignments
    Route::get('/assignments/{assignment}/submit', [StudentSubmissionController::class, 'create'])->name('submissions.create');
    Route::post('/submissions', [StudentSubmissionController::class, 'store'])->name('submissions.store');
    Route::get('/submissions/{submission}', [StudentSubmissionController::class, 'show'])->name('submissions.show');
    Route::get('/submissions/{submission}/edit', [StudentSubmissionController::class, 'edit'])->name('submissions.edit');
    Route::put('/submissions/{submission}', [StudentSubmissionController::class, 'update'])->name('submissions.update');
});

require __DIR__.'/auth.php';


