<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nộp bài: {{ $assignment->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Assignment Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="font-semibold text-lg mb-2">{{ $assignment->title }}</h3>
                <p class="text-gray-600 mb-2">{{ $assignment->description }}</p>
                <div class="flex items-center space-x-4 text-sm">
                    <span class="text-gray-500">📚 {{ $assignment->classroom->name }}</span>
                    <span class="text-gray-500">
                        📅 Hạn nộp: {{ $assignment->due_date->format('d/m/Y H:i') }}
                        @if($assignment->isOverdue())
                            <span class="text-red-600 font-semibold">(Đã quá hạn)</span>
                        @endif
                    </span>
                </div>
            </div>

            <!-- Submission Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Thông tin bài nộp</h3>

                <form method="POST" action="{{ route('student.submissions.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">

                    <!-- File Upload -->
                    <div class="mb-6">
                        <x-input-label for="file" :value="__('File bài nộp')" />
                        <input id="file" name="file" type="file" 
                               class="mt-1 block w-full text-sm text-gray-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-md file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-blue-50 file:text-blue-700
                                      hover:file:bg-blue-100"
                               required accept=".pdf,.pptx,.ppt,.mp4" />
                        <p class="text-xs text-gray-500 mt-2">
                            ✅ Định dạng hỗ trợ: PDF, PPTX, MP4<br>
                            📦 Kích thước tối đa: 200MB
                        </p>
                        <x-input-error :messages="$errors->get('file')" class="mt-2" />
                    </div>

                    <!-- Note -->
                    <div class="mb-6">
                        <x-input-label for="note" :value="__('Ghi chú (không bắt buộc)')" />
                        <textarea id="note" name="note" rows="4" 
                                  placeholder="Ghi chú về bài nộp của bạn..."
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('note') }}</textarea>
                        <x-input-error :messages="$errors->get('note')" class="mt-2" />
                    </div>

                    <!-- Warning -->
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <p class="text-sm text-yellow-700">
                            <strong>⚠️ Lưu ý:</strong> Bạn chỉ có thể nộp bài một lần cho mỗi bài tập. Hãy kiểm tra kỹ trước khi nộp.
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('student.assignments.show', $assignment) }}" 
                           class="mr-4 text-gray-600 hover:text-gray-900">
                            Hủy
                        </a>
                        <x-primary-button class="text-lg py-3 px-6">
                            {{ __('📤 Nộp bài') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
