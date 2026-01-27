<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Các lớp của tôi
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($classrooms as $classroom)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="font-semibold text-xl mb-3">{{ $classroom->name }}</h3>
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Học sinh:</span>
                                <span class="font-semibold">{{ $classroom->students_count }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Bài tập:</span>
                                <span class="font-semibold">{{ $classroom->assignments_count }}</span>
                            </div>
                        </div>
                        <a href="{{ route('teacher.classrooms.show', $classroom) }}" 
                           class="block w-full text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Xem chi tiết
                        </a>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12">
                        <p class="text-gray-500 text-lg">Bạn chưa được phân công lớp nào</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $classrooms->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
