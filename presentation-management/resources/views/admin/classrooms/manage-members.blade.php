<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Quản lý thành viên: {{ $classroom->name }}
            </h2>
            <a href="{{ route('admin.classrooms.show', $classroom) }}" class="text-gray-600 hover:text-gray-900">
                ← Quay lại
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Teachers Management -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Giáo viên ({{ $classroom->teachers->count() }})</h3>
                    
                    <!-- Add Teacher Form -->
                    <form method="POST" action="{{ route('admin.classrooms.add-teacher', $classroom) }}" class="mb-4">
                        @csrf
                        <div class="flex gap-2">
                            <select name="user_id" class="flex-1 rounded-md border-gray-300" required>
                                <option value="">-- Chọn giáo viên --</option>
                                @foreach($availableTeachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->email }})</option>
                                @endforeach
                            </select>
                            <x-primary-button>Thêm</x-primary-button>
                        </div>
                    </form>

                    <!-- Current Teachers -->
                    <div class="space-y-2 mt-4">
                        @forelse($classroom->teachers as $teacher)
                            <div class="flex justify-between items-center border-b pb-2">
                                <div>
                                    <p class="font-medium">{{ $teacher->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $teacher->email }}</p>
                                </div>
                                <form action="{{ route('admin.classrooms.remove-teacher', [$classroom, $teacher]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm"
                                            onclick="return confirm('Xóa giáo viên này khỏi lớp?')">
                                        Xóa
                                    </button>
                                </form>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">Chưa có giáo viên nào</p>
                        @endforelse
                    </div>
                </div>

                <!-- Students Management -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Học sinh ({{ $classroom->students->count() }})</h3>
                    
                    <!-- Add Student Form -->
                    <form method="POST" action="{{ route('admin.classrooms.add-student', $classroom) }}" class="mb-4">
                        @csrf
                        <div class="flex gap-2">
                            <select name="user_id" class="flex-1 rounded-md border-gray-300" required>
                                <option value="">-- Chọn học sinh --</option>
                                @foreach($availableStudents as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                                @endforeach
                            </select>
                            <x-primary-button>Thêm</x-primary-button>
                        </div>
                    </form>

                    <!-- Current Students -->
                    <div class="space-y-2 mt-4 max-h-96 overflow-y-auto">
                        @forelse($classroom->students as $student)
                            <div class="flex justify-between items-center border-b pb-2">
                                <div>
                                    <p class="font-medium">{{ $student->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $student->email }}</p>
                                </div>
                                <form action="{{ route('admin.classrooms.remove-student', [$classroom, $student]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm"
                                            onclick="return confirm('Xóa học sinh này khỏi lớp?')">
                                        Xóa
                                    </button>
                                </form>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">Chưa có học sinh nào</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
