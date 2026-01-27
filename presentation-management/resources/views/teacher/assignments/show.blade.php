<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $assignment->title }}
            </h2>
            <a href="{{ route('teacher.assignments.edit', $assignment) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Sửa bài tập
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Assignment Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-gray-600 text-sm">Lớp học</p>
                        <p class="font-semibold">{{ $assignment->classroom->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Hạn nộp</p>
                        <p class="font-semibold">{{ $assignment->due_date->format('d/m/Y H:i') }}</p>
                        @if($assignment->isOverdue())
                            <span class="text-red-600 text-sm">(Đã quá hạn)</span>
                        @endif
                    </div>
                </div>
                <div>
                    <p class="text-gray-600 text-sm mb-1">Mô tả</p>
                    <p class="whitespace-pre-wrap">{{ $assignment->description }}</p>
                </div>
            </div>

            <!-- Submissions List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">
                    Bài nộp ({{ $assignment->submissions->count() }}/{{ $assignment->classroom->students->count() }})
                </h3>
                
                @if($assignment->submissions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Học sinh</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nộp lúc</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Điểm</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hành động</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($assignment->submissions as $submission)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $submission->student->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $submission->submitted_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($submission->isGraded())
                                                <span class="text-lg font-bold">{{ $submission->grade->score }}</span>
                                            @else
                                                <span class="text-gray-400">Chưa chấm</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($submission->isGraded())
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Đã chấm
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Chờ chấm
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('teacher.submissions.show', $submission) }}" class="text-blue-600 hover:text-blue-900">
                                                Xem & Chấm điểm
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">Chưa có bài nộp nào</p>
                @endif
            </div>

            <!-- Students who haven't submitted -->
            @php
                $submittedStudentIds = $assignment->submissions->pluck('user_id');
                $notSubmitted = $assignment->classroom->students->whereNotIn('id', $submittedStudentIds);
            @endphp

            @if($notSubmitted->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4 text-red-600">Chưa nộp ({{ $notSubmitted->count() }})</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($notSubmitted as $student)
                            <div class="text-sm">
                                {{ $student->name }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
