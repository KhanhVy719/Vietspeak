<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $assignment->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Assignment Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-2xl font-bold mb-4">{{ $assignment->title }}</h3>
                
                <div class="grid grid-cols-2 gap-4 mb-4 pb-4 border-b">
                    <div>
                        <p class="text-gray-600 text-sm">Lớp học</p>
                        <p class="font-semibold">{{ $assignment->classroom->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Hạn nộp</p>
                        <p class="font-semibold">{{ $assignment->due_date->format('d/m/Y H:i') }}</p>
                        @if($assignment->isOverdue())
                            <span class="text-red-600 text-sm font-bold">(Đã quá hạn)</span>
                        @else
                            <span class="text-green-600 text-sm">(Còn {{ $assignment->due_date->diffForHumans() }})</span>
                        @endif
                    </div>
                </div>

                <div>
                    <p class="text-gray-600 text-sm mb-2">Mô tả bài tập</p>
                    <div class="bg-gray-50 p-4 rounded whitespace-pre-wrap">{{ $assignment->description ?: 'Không có mô tả' }}</div>
                </div>
            </div>

            <!-- Submission Status -->
            @if($submission)
                <!-- Already submitted -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Bài nộp của bạn</h3>
                    
                    <div class="bg-blue-50 p-4 rounded mb-4">
                        <p class="text-sm text-gray-600">Đã nộp lúc</p>
                        <p class="font-semibold">{{ $submission->submitted_at->format('d/m/Y H:i') }}</p>
                    </div>

                    @if($submission->note)
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-1">Ghi chú của bạn</p>
                            <p class="bg-gray-50 p-3 rounded">{{ $submission->note }}</p>
                        </div>
                    @endif

                    <a href="{{ route('student.submissions.show', $submission) }}" 
                       class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Xem chi tiết bài nộp →
                    </a>
                </div>
            @else
                <!-- Not submitted yet -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    @if($assignment->isOverdue())
                        <div class="bg-red-50 border-l-4 border-red-500 p-4">
                            <p class="font-semibold text-red-800">Bài tập đã quá hạn nộp</p>
                            <p class="text-sm text-red-600 mt-1">Bạn không thể nộp bài tập này nữa</p>
                        </div>
                    @else
                        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4">
                            <p class="font-semibold text-green-800">Bạn chưa nộp bài tập này</p>
                            <p class="text-sm text-green-600 mt-1">Hãy nộp bài trước khi hết hạn</p>
                        </div>
                        <a href="{{ route('student.submissions.create', $assignment) }}" 
                           class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded text-lg">
                            📤 Nộp bài ngay
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
