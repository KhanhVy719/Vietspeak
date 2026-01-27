<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Danh sách bài tập
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @forelse($assignments as $assignment)
                    @php
                        $submission = $assignment->submissions->first();
                    @endphp
                    <div class="border-b pb-4 mb-4 last:border-b-0">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <a href="{{ route('student.assignments.show', $assignment) }}" class="text-xl font-semibold hover:text-blue-600">
                                    {{ $assignment->title }}
                                </a>
                                <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                                    <span>📚 {{ $assignment->classroom->name }}</span>
                                    <span>📅 Hạn: {{ $assignment->due_date->format('d/m/Y H:i') }}</span>
                                </div>
                                
                                <!-- Submission Status -->
                                <div class="mt-3">
                                    @if($submission)
                                        <!-- Has submitted -->
                                        @if($submission->isGraded())
                                            <div class="flex items-center space-x-3">
                                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                                    Đã chấm điểm: {{ $submission->grade->score }}
                                                </span>
                                                <a href="{{ route('student.submissions.show', $submission) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                                                    Xem chi tiết →
                                                </a>
                                            </div>
                                        @else
                                            <div class="flex items-center space-x-3">
                                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                                                    Đã nộp, chờ chấm điểm
                                                </span>
                                                <a href="{{ route('student.submissions.show', $submission) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                                                    Xem bài nộp →
                                                </a>
                                            </div>
                                        @endif
                                    @else
                                        <!-- Not submitted yet -->
                                        @if($assignment->isOverdue())
                                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                                                Đã quá hạn nộp
                                            </span>
                                        @else
                                            <a href="{{ route('student.submissions.create', $assignment) }}" 
                                               class="inline-block px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded">
                                                Nộp bài ngay
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-8">Chưa có bài tập nào</p>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $assignments->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
