<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nộp lại bài: {{ $submission->assignment->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Warning about resubmission -->
            <div class="bg-orange-50 border-l-4 border-orange-400 p-4 mb-6">
                <p class="font-semibold text-orange-800">⚠️ Nộp lại bài tập</p>
                <p class="text-sm text-orange-700 mt-1">
                    Bài nộp cũ sẽ bị thay thế bằng bài nộp mới. File cũ sẽ bị xóa.
                </p>
            </div>

            <!-- Current Submission Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-3">Bài nộp hiện tại</h3>
                <div class="text-sm space-y-2">
                    <div>
                        <span class="text-gray-600">Nộp lúc:</span>
                        <span class="font-medium ml-2">{{ $submission->submitted_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">File:</span>
                        <span class="font-medium ml-2">{{ $submission->getFileName() }}</span>
                    </div>
                    @if($submission->note)
                        <div>
                            <span class="text-gray-600">Ghi chú:</span>
                            <p class="ml-2 bg-gray-50 p-2 rounded mt-1">{{ $submission->note }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Resubmission Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Nộp bài mới</h3>

                <form method="POST" action="{{ route('student.submissions.update', $submission) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="assignment_id" value="{{ $submission->assignment_id }}">

                    <!-- File Upload -->
                    <div class="mb-6">
                        <x-input-label for="file" :value="__('File bài nộp mới')" />
                        <input id="file" name="file" type="file" 
                               class="mt-1 block w-full text-sm text-gray-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-md file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-blue-50 file:text-blue-700
                                      hover:file:bg-blue-100"
                               required accept=".pdf,.pptx,.ppt,.mp4" />
                        <p class="text-xs text-gray-500 mt-2">
                            ✅ Định dạng: PDF, PPTX, MP4 | 📦 Tối đa: 200MB
                        </p>
                        <x-input-error :messages="$errors->get('file')" class="mt-2" />
                    </div>

                    <!-- Note -->
                    <div class="mb-6">
                        <x-input-label for="note" :value="__('Ghi chú mới')" />
                        <textarea id="note" name="note" rows="4" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('note', $submission->note) }}</textarea>
                        <x-input-error :messages="$errors->get('note')" class="mt-2" />
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('student.submissions.show', $submission) }}" 
                           class="mr-4 text-gray-600 hover:text-gray-900">
                            Hủy
                        </a>
                        <x-primary-button class="bg-orange-500 hover:bg-orange-700">
                            {{ __('🔄 Nộp lại') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
