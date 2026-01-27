<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Quản lý học viên: {{ $course->name }}
            </h2>
            <a href="{{ route('admin.courses.index') }}" class="text-gray-600 hover:text-gray-900">
                ← Quay lại
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Học viên ({{ $course->students->count() }})</h3>
                
                <!-- Add Student Form -->
                <form method="POST" action="{{ route('admin.courses.add-student', $course) }}" class="mb-4">
                    @csrf
                    <div class="flex gap-2">
                        <select name="user_id" class="flex-1 rounded-md border-gray-300" required>
                            <option value="">-- Chọn học sinh --</option>
                            @foreach($availableStudents as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                            @endforeach
                        </select>
                        <x-primary-button>Ghi danh</x-primary-button>
                    </div>
                </form>

                <!-- Current Students -->
                <div class="space-y-2 mt-4 max-h-96 overflow-y-auto">
                    @forelse($course->students as $student)
                        <div class="flex justify-between items-center border-b pb-2">
                            <div>
                                <p class="font-medium">{{ $student->name }}</p>
                                <p class="text-sm text-gray-600">{{ $student->email }}</p>
                                <p class="text-xs text-gray-500">
                                    Ghi danh: {{ \Carbon\Carbon::parse($student->pivot->enrolled_at)->format('d/m/Y') }} • 
                                    Tiến độ: {{ $student->pivot->progress }}%
                                </p>
                            </div>
                            <form action="{{ route('admin.courses.remove-student', [$course, $student]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm"
                                        onclick="return confirm('Xóa học sinh này khỏi khóa học?')">
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
</x-app-layout>
