<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Bài nộp của bạn
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Assignment Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-xl font-semibold mb-3">{{ $submission->assignment->title }}</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">Lớp học</p>
                        <p class="font-semibold">{{ $submission->assignment->classroom->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Hạn nộp</p>
                        <p>{{ $submission->assignment->due_date->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Submission Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Thông tin bài nộp</h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-gray-600 text-sm">Nộp lúc</p>
                        <p class="font-semibold">{{ $submission->submitted_at->format('d/m/Y H:i') }}</p>
                        <p class="text-xs text-gray-500">{{ $submission->submitted_at->diffForHumans() }}</p>
                    </div>

                    <div>
                        <p class="text-gray-600 text-sm mb-2">File đã nộp</p>
                        <div class="bg-gray-50 p-4 rounded flex items-center justify-between">
                            <div>
                                <p class="font-medium">📄 {{ $submission->getFileName() }}</p>
                                <p class="text-xs text-gray-500">{{ strtoupper($submission->getFileExtension()) }}</p>
                            </div>
                            <a href="{{ route('downloads.submission', $submission) }}" 
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                📥 Tải xuống
                            </a>
                        </div>
                    </div>

                    @if($submission->note)
                        <div>
                            <p class="text-gray-600 text-sm mb-1">Ghi chú của bạn</p>
                            <p class="bg-gray-50 p-3 rounded">{{ $submission->note }}</p>
                        </div>
                    @endif

                    <!-- Resubmit option -->
                    @if(!$submission->isGraded())
                        <div class="pt-4 border-t">
                            <a href="{{ route('student.submissions.edit', $submission) }}" 
                               class="inline-block bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">
                                🔄 Nộp lại (chưa chấm điểm)
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Grade (if exists) -->
            @if($submission->isGraded())
                <div class="bg-gradient-to-r from-green-50 to-blue-50 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Kết quả chấm điểm</h3>
                    
                    <div class="grid grid-cols-2 gap-6 mb-4">
                        <div class="text-center">
                            <p class="text-gray-600 text-sm mb-2">Điểm số</p>
                            <p class="text-6xl font-bold text-green-600">{{ $submission->grade->score }}</p>
                            <p class="text-sm text-gray-600 mt-2">{{ $submission->grade->getScoreCategory() }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Giáo viên chấm</p>
                            <p class="font-semibold">{{ $submission->grade->grader->name }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $submission->grade->graded_at->format('d/m/Y H:i') }}</p>
                            <p class="text-xs text-gray-500">{{ $submission->grade->graded_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    @if($submission->grade->comment)
                        <div>
                            <p class="text-gray-600 text-sm mb-2">Nhận xét của giáo viên</p>
                            <div class="bg-white p-4 rounded shadow-sm">
                                <p class="whitespace-pre-wrap">{{ $submission->grade->comment }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <p class="font-semibold text-yellow-800">Chưa có điểm</p>
                    <p class="text-sm text-yellow-700 mt-1">
                        Giáo viên chưa chấm điểm bài nộp của bạn. Vui lòng đợi.
                    </p>
                </div>
            @endif

            <!-- Back Button -->
            <div>
                <a href="{{ route('student.assignments.show', $submission->assignment) }}" 
                   class="text-blue-600 hover:text-blue-900">
                    ← Quay lại bài tập
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
