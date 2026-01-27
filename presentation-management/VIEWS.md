# Hướng Dẫn Tạo Views

Tài liệu này hướng dẫn tạo các Blade views còn lại cho hệ thống.

## Cấu Trúc Thư Mục Views

```
resources/views/
├── layouts/
│   ├── app.blade.php ✓ (Đã tạo)
│   ├── navigation.blade.php ✓ (Đã tạo)
│   └── guest.blade.php (Cần tạo - từ Breeze)
├── components/
│   └── ... (Breeze components - tự động generate)
├── admin/
│   ├── dashboard.blade.php
│   ├── users/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   └── classrooms/
│       ├── index.blade.php
│       ├── create.blade.php
│       ├── edit.blade.php
│       ├── show.blade.php
│       └── manage-members.blade.php
├── teacher/
│   ├── dashboard.blade.php
│   ├── classrooms/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── assignments/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── show.blade.php
│   └── submissions/
│       └── show.blade.php
└── student/
    ├── dashboard.blade.php
    ├── assignments/
    │   ├── index.blade.php
    │   └── show.blade.php
    └── submissions/
        ├── create.blade.php
        ├── show.blade.php
        └── edit.blade.php
```

## Các View Được Tạo Tự Động bởi Breeze

Khi chạy `php artisan breeze:install blade`, các file sau sẽ được tạo tự động:

### Auth Views (resources/views/auth/)

- login.blade.php
- register.blade.php
- forgot-password.blade.php
- reset-password.blade.php
- verify-email.blade.php
- confirm-password.blade.php

### Profile Views (resources/views/profile/)

- edit.blade.php
- partials/delete-user-form.blade.php
- partials/update-password-form.blade.php
- partials/update-profile-information-form.blade.php

### Components (resources/views/components/)

- application-logo.blade.php
- auth-session-status.blade.php
- danger-button.blade.php
- dropdown.blade.php
- dropdown-link.blade.php
- input-error.blade.php
- input-label.blade.php
- modal.blade.php
- nav-link.blade.php
- primary-button.blade.php
- responsive-nav-link.blade.php
- secondary-button.blade.php
- text-input.blade.php
- ... và nhiều components khác

## Template Cơ Bản Cho Các Views

### Admin Dashboard (resources/views/admin/dashboard.blade.php)

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg: px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-900 text-2xl font-bold">{{ $stats['total_users'] }}</div>
                    <div class="text-gray-600 text-sm">Tổng người dùng</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-900 text-2xl font-bold">{{ $stats['total_classrooms'] }}</div>
                    <div class="text-gray-600 text-sm">Tổng lớp học</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-900 text-2xl font-bold">{{ $stats['total_assignments'] }}</div>
                    <div class="text-gray-600 text-sm">Tổng bài tập</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-900 text-2xl font-bold">{{ $stats['total_submissions'] }}</div>
                    <div class="text-gray-600 text-sm">Tổng bài nộp</div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Recent Submissions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Bài nộp gần đây</h3>
                    <div class="space-y-3">
                        @foreach($recent_submissions as $submission)
                            <div class="border-b pb-2">
                                <div class="font-medium">{{ $submission->student->name }}</div>
                                <div class="text-sm text-gray-600">{{ $submission->assignment->title }}</div>
                                <div class="text-xs text-gray-500">{{ $submission->submitted_at->diffForHumans() }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Assignments -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Bài tập mới</h3>
                    <div class="space-y-3">
                        @foreach($recent_assignments as $assignment)
                            <div class="border-b pb-2">
                                <div class="font-medium">{{ $assignment->title }}</div>
                                <div class="text-sm text-gray-600">{{ $assignment->classroom->name }}</div>
                                <div class="text-xs text-gray-500">{{ $assignment->created_at->diffForHumans() }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

### Admin Users Index (resources/views/admin/users/index.blade.php)

```blade
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Quản lý người dùng
            </h2>
            <a href="{{ route('admin.users.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Tạo người dùng mới
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search and Filter -->
            <div class="mb-6 bg-white p-4 rounded-lg shadow">
                <form method="GET" action="{{ route('admin.users.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <input type="text" name="search" placeholder="Tìm kiếm..." value="{{ request('search') }}"
                               class="rounded-md border-gray-300">
                        <select name="role" class="rounded-md border-gray-300">
                            <option value="">Tất cả vai trò</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="teacher" {{ request('role') == 'teacher' ? 'selected' : '' }}>Giáo viên</option>
                            <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Học sinh</option>
                        </select>
                        <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Tìm kiếm
                        </button>
                    </div>
                </form>
            </div>

            <!-- Users Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vai trò</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $user->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                          @if($user->hasRole('admin')) bg-purple-100 text-purple-800
                                          @elseif($user->hasRole('teacher')) bg-blue-100 text-blue-800
                                          @else bg-green-100 text-green-800 @endif">
                                        {{ ucfirst($user->roles->first()->name ?? 'N/A') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Sửa</a>
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('Bạn có chắc muốn xóa người dùng này?')">
                                                Xóa
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
```

### Student Submission Create (resources/views/student/submissions/create.blade.php)

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nộp bài: {{ $assignment->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Assignment Info -->
                <div class="mb-6 p-4 bg-gray-50 rounded">
                    <h3 class="font-semibold text-lg mb-2">{{ $assignment->title }}</h3>
                    <p class="text-gray-600 mb-2">{{ $assignment->description }}</p>
                    <p class="text-sm text-gray-500">
                        <strong>Hạn nộp:</strong> {{ $assignment->due_date->format('d/m/Y H:i') }}
                        @if($assignment->isOverdue())
                            <span class="text-red-600 font-semibold">(Đã quá hạn)</span>
                        @endif
                    </p>
                </div>

                <!-- Submission Form -->
                <form method="POST" action="{{ route('student.submissions.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">

                    <!-- File Upload -->
                    <div class="mb-4">
                        <x-input-label for="file" :value="__('File bài nộp')" />
                        <input id="file" name="file" type="file" class="mt-1 block w-full" required
                               accept=".pdf,.pptx,.ppt,.mp4" />
                        <p class="text-xs text-gray-500 mt-1">
                            Định dạng: PDF, PPTX, MP4. Tối đa: 200MB
                        </p>
                        <x-input-error :messages="$errors->get('file')" class="mt-2" />
                    </div>

                    <!-- Note -->
                    <div class="mb-4">
                        <x-input-label for="note" :value="__('Ghi chú')" />
                        <textarea id="note" name="note" rows="4"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('note') }}</textarea>
                        <x-input-error :messages="$errors->get('note')" class="mt-2" />
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('student.assignments.show', $assignment) }}"
                           class="mr-4 text-gray-600 hover:text-gray-900">
                            Hủy
                        </a>
                        <x-primary-button>
                            {{ __('Nộp bài') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
```

## Hướng Dẫn Tạo Views Còn Lại

### Pattern Chung

Tất cả views đều sử dụng `<x-app-layout>` component và có cấu trúc:

```blade
<x-app-layout>
    <x-slot name="header">
        <!-- Page title -->
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Page content -->
        </div>
    </div>
</x-app-layout>
```

### Components Breeze Có Sẵn

Sử dụng các component sau:

- `<x-input-label>` - Label cho input
- `<x-text-input>` - Text input
- `<x-input-error>` - Hiển thị lỗi validation
- `<x-primary-button>` - Nút chính
- `<x-secondary-button>` - Nút phụ
- `<x-danger-button>` - Nút nguy hiểm
- `<x-dropdown>` - Dropdown menu
- `<x-modal>` - Modal dialog

### Tips

1. **Forms:** Luôn thêm `@csrf` trong forms
2. **Delete:** Dùng method `@method('DELETE')` cho delete forms
3. **Validation Errors:** Hiển thị lỗi với `<x-input-error>`
4. **Flash Messages:** Đã được xử lý trong layout app.blade.php
5. **Pagination:** Dùng `{{ $items->links() }}`

## Các View Quan Trọng Cần Tạo Tiếp

1. **Admin Views:** users/create.blade.php, users/edit.blade.php, classrooms/\* (6 files)
2. **Teacher Views:** dashboard.blade.php, classrooms/_, assignments/_, submissions/show.blade.php (9 files)
3. **Student Views:** dashboard.blade.php, assignments/_, submissions/_ (6 files)

**Tổng cộng:** ~21 view files cần tạo tiếp

Mỗi view có thể tham khảo template ở trên và điều chỉnh cho phù hợp với từng tính năng cụ thể.

---

Sau khi tạo xong tất cả views, hệ thống sẽ hoàn chỉnh và sẵn sàng sử dụng!
