<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Danh sách bài tập
            </h2>
            <a href="{{ route('teacher.assignments.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Tạo bài tập mới
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @forelse($assignments as $assignment)
                        <div class="border-b pb-4 mb-4 last:border-b-0">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <a href="{{ route('teacher.assignments.show', $assignment) }}" class="text-xl font-semibold hover:text-blue-600">
                                        {{ $assignment->title }}
                                    </a>
                                    <p class="text-gray-600 mt-1">{{ Str::limit($assignment->description, 150) }}</p>
                                    <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                                        <span>📚 {{ $assignment->classroom->name }}</span>
                                        <span>📅 Hạn: {{ $assignment->due_date->format('d/m/Y H:i') }}</span>
                                        <span>📝 {{ $assignment->submissions_count }} bài nộp</span>
                                    </div>
                                </div>
                                <div class="ml-4 flex space-x-2">
                                    <a href="{{ route('teacher.assignments.edit', $assignment) }}" class="text-blue-600 hover:text-blue-900">
                                        Sửa
                                    </a>
                                    <form action="{{ route('teacher.assignments.destroy', $assignment) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Xóa bài tập này?')">
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">Chưa có bài tập nào</p>
                    @endforelse
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $assignments->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
