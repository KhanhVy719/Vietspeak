<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900">
            Dashboard Học sinh
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 animate-fade-in">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Classrooms Card -->
                <div class="stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-gray-600 text-sm font-medium mb-1">Lớp đang học</div>
                            <div class="text-3xl font-bold gradient-primary bg-clip-text text-transparent">
                                {{ $stats['total_classrooms'] }}
                            </div>
                        </div>
                        <div class="icon-primary">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- Submissions Card -->
                <a href="{{ route('student.assignments.index') }}" 
                   class="stat-card-interactive">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-gray-600 text-sm font-medium mb-1">Bài đã nộp</div>
                            <div class="text-3xl font-bold gradient-secondary bg-clip-text text-transparent">
                                {{ $stats['total_submissions'] }}
                            </div>
                        </div>
                        <div class="icon-success">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-blue-500 text-xs mt-2">→ Xem tất cả bài tập</div>
                </a>
                
                <!-- Graded Card -->
                <a href="{{ route('student.assignments.index') }}" 
                   class="stat-card-interactive">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-gray-600 text-sm font-medium mb-1">Đã có điểm</div>
                            <div class="text-3xl font-bold text-teal-600">
                                {{ $stats['graded_submissions'] }}
                            </div>
                        </div>
                        <div class="icon-container bg-teal-100 text-teal-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-teal-500 text-xs mt-2">→ Xem điểm các bài</div>
                </a>
                
                <!-- Average Score Card -->
                <a href="{{ route('student.assignments.index') }}" 
                   class="stat-card-interactive">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-gray-600 text-sm font-medium mb-1">Điểm trung bình</div>
                            <div class="text-3xl font-bold gradient-accent bg-clip-text text-transparent">
                                {{ $stats['average_score'] ? number_format($stats['average_score'], 1) : '-' }}
                            </div>
                        </div>
                        <div class="icon-container bg-purple-100 text-purple-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-purple-500 text-xs mt-2">→ Xem chi tiết</div>
                </a>
            </div>

            <!-- Pending Assignments -->
            <div class="card-elevated p-6 animate-slide-up">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Bài tập chưa nộp
                    </h3>
                    <span class="badge-danger">{{ $stats['pending_assignments'] }} bài</span>
                </div>
                @if($pendingAssignments->count() > 0)
                    <div class="space-y-3">
                        @foreach($pendingAssignments as $assignment)
                            <div class="card-elevated p-4 border-l-4 border-red-500 hover:shadow-lg transition-shadow">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <a href="{{ route('student.assignments.show', $assignment) }}" class="font-bold text-gray-900 hover:text-blue-600 text-lg">
                                            {{ $assignment->title }}
                                        </a>
                                        <p class="text-sm text-gray-600 mt-1 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            {{ $assignment->classroom->name }}
                                        </p>
                                        <p class="text-sm font-semibold mt-2 flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-red-600">Hạn nộp: {{ $assignment->due_date->format('d/m/Y H:i') }}</span>
                                            <span class="text-orange-600 ml-2">({{ $assignment->due_date->diffForHumans() }})</span>
                                        </p>
                                    </div>
                                    <a href="{{ route('student.submissions.create', $assignment) }}" 
                                       class="btn-primary ml-4">
                                        📤 Nộp bài
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-16 w-16 text-green-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-green-600 font-semibold text-lg">Bạn đã nộp hết bài tập đúng hạn! 🎉</p>
                    </div>
                @endif
            </div>

            <!-- My Classes -->
            <div class="card-elevated p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Các lớp đang học
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($classrooms as $classroom)
                        <div class="card-elevated p-5 hover:shadow-xl transition-all duration-300 group">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="font-bold text-lg text-gray-900 group-hover:text-blue-600 transition-colors">
                                        {{ $classroom->name }}
                                    </h4>
                                    <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ $classroom->description }}</p>
                                </div>
                                <span class="badge-primary ml-2">
                                    {{ $classroom->assignments_count }} bài tập
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 col-span-2 text-center py-8">Bạn chưa được thêm vào lớp nào</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
