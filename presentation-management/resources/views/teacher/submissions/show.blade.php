<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Chấm điểm bài nộp
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Submission Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Thông tin bài nộp</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600 text-sm">Học sinh</p>
                        <p class="font-semibold">{{ $submission->student->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Bài tập</p>
                        <p class="font-semibold">{{ $submission->assignment->title }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Lớp</p>
                        <p>{{ $submission->assignment->classroom->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Nộp lúc</p>
                        <p>{{ $submission->submitted_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                @if($submission->note)
                    <div class="mt-4">
                        <p class="text-gray-600 text-sm mb-1">Ghi chú từ học sinh</p>
                        <p class="bg-gray-50 p-3 rounded">{{ $submission->note }}</p>
                    </div>
                @endif

                <!-- Download File -->
                <div class="mt-4">
                    <a href="{{ route('downloads.submission', $submission) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        📥 Tải xuống file bài nộp
                    </a>
                    <p class="text-xs text-gray-500 mt-1">File: {{ $submission->getFileName() }}</p>
                </div>
            </div>

            <!-- Grading Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">
                    @if($submission->isGraded())
                        Cập nhật điểm
                    @else
                        Chấm điểm
                    @endif
                </h3>

                <form method="POST" action="{{ route('teacher.submissions.grade', $submission) }}">
                    @csrf
                    <input type="hidden" name="submission_id" value="{{ $submission->id }}">

                    <!-- Score -->
                    <div class="mb-4">
                        <x-input-label for="score" :value="__('Điểm (0-10)')" />
                        <x-text-input id="score" class="block mt-1 w-full" type="number" name="score" 
                                     min="0" max="10" step="0.1"
                                     :value="old('score', $submission->grade->score ?? '')" required />
                        <x-input-error :messages="$errors->get('score')" class="mt-2" />
                    </div>

                    <!-- Comment -->
                    <div class="mb-4">
                        <x-input-label for="comment" :value="__('Nhận xét')" />
                        <textarea id="comment" name="comment" rows="6" 
                                  class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('comment', $submission->grade->comment ?? '') }}</textarea>
                        <x-input-error :messages="$errors->get('comment')" class="mt-2" />
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('teacher.assignments.show', $submission->assignment) }}" class="mr-4 text-gray-600 hover:text-gray-900">
                            Quay lại
                        </a>
                        <x-primary-button>
                            {{ __('Lưu điểm') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Current Grade (if exists) -->
            @if($submission->isGraded())
                <div class="bg-green-50 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-3">Đã chấm</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600 text-sm">Điểm</p>
                            <p class="text-3xl font-bold text-green-600">{{ $submission->grade->score }}</p>
                            <p class="text-sm text-gray-500">{{ $submission->grade->getScoreCategory() }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Chấm bởi</p>
                            <p>{{ $submission->grade->grader->name }}</p>
                            <p class="text-sm text-gray-500">{{ $submission->grade->graded_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @if($submission->grade->comment)
                        <div class="mt-3">
                            <p class="text-gray-600 text-sm mb-1">Nhận xét</p>
                            <p class="bg-white p-3 rounded">{{ $submission->grade->comment }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
