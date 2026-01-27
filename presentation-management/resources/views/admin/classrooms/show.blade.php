<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Chi tiết lớp: {{ $classroom->name }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('admin.classrooms.manage-members', $classroom) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Quản lý thành viên
                </a>
                <a href="{{ route('admin.classrooms.edit', $classroom) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Sửa thông tin
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Classroom Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Thông tin lớp học</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">Tên lớp:</p>
                        <p class="font-semibold">{{ $classroom->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Mô tả:</p>
                        <p>{{ $classroom->description ?: 'Không có mô tả' }}</p>
                    </div>
                </div>
            </div>

            <!-- Teachers List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Giáo viên ({{ $classroom->teachers->count() }})</h3>
                <div class="space-y-2">
                    @forelse($classroom->teachers as $teacher)
                        <div class="flex justify-between items-center border-b pb-2">
                            <div>
                                <p class="font-medium">{{ $teacher->name }}</p>
                                <p class="text-sm text-gray-600">{{ $teacher->email }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">Chưa có giáo viên nào được gán</p>
                    @endforelse
                </div>
            </div>

            <!-- Students List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Học sinh ({{ $classroom->students->count() }})</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($classroom->students as $student)
                        <div class="border-b pb-2">
                            <p class="font-medium">{{ $student->name }}</p>
                            <p class="text-sm text-gray-600">{{ $student->email }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500">Chưa có học sinh nào</p>
                    @endforelse
                </div>
            </div>

            <!-- Assignments List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Bài tập ({{ $classroom->assignments->count() }})</h3>
                <div class="space-y-3">
                    @forelse($classroom->assignments as $assignment)
                        <div class="border-b pb-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium">{{ $assignment->title }}</p>
                                    <p class="text-sm text-gray-600">{{ $assignment->description }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Hạn nộp: {{ $assignment->due_date->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                <div class="text-sm">
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                        {{ $assignment->submissions->count() }} bài nộp
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">Chưa có bài tập nào</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
