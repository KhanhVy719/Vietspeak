<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900">
            Dashboard Giáo viên
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 animate-fade-in">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="stat-card group">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-gray-600 text-sm font-medium mb-1">Lớp phụ trách</div>
                            <div class="text-3xl font-bold gradient-primary bg-clip-text text-transparent">
                                {{ $stats['total_classrooms'] }}
                            </div>
                        </div>
                        <div class="icon-primary group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="stat-card group">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-gray-600 text-sm font-medium mb-1">Tổng học sinh</div>
                            <div class="text-3xl font-bold gradient-secondary bg-clip-text text-transparent">
                                {{ $stats['total_students'] }}
                            </div>
                        </div>
                        <div class="icon-success group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="stat-card group">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-gray-600 text-sm font-medium mb-1">Bài tập đã tạo</div>
                            <div class="text-3xl font-bold text-purple-600">
                                {{ $stats['total_assignments'] }}
                            </div>
                        </div>
                        <div class="icon-container bg-purple-100 text-purple-600 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="stat-card group">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-gray-600 text-sm font-medium mb-1">Chờ chấm điểm</div>
                            <div class="text-3xl font-bold text-red-600">
                                {{ $stats['pending_grades'] }}
                            </div>
                        </div>
                        <div class="icon-danger group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Classrooms List -->
            <div class="card-elevated p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Các lớp phụ trách
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($classrooms as $classroom)
                        <a href="{{ route('teacher.classrooms.show', $classroom) }}" 
                           class="card-elevated p-5 hover:shadow-xl transition-all duration-300 group block">
                            <h4 class="font-bold text-lg text-gray-900 group-hover:text-blue-600 transition-colors mb-3">
                                {{ $classroom->name }}
                            </h4>
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div class="flex items-center text-sm text-gray-600">
                                    <span class="badge-success mr-2">👥</span>
                                    {{ $classroom->students_count }} học sinh
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <span class="badge-primary mr-2">📝</span>
                                    {{ $classroom->assignments_count }} bài tập
                                </div>
                            </div>
                            
                            @if($classroom->assignments->count() > 0)
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <p class="text-xs font-semibold text-gray-500 mb-2">📚 Bài tập gần đây:</p>
                                    <div class="space-y-1">
                                        @foreach($classroom->assignments as $assignment)
                                            <p class="text-sm text-gray-700 truncate flex items-center">
                                                <span class="text-blue-500 mr-2">•</span>
                                                {{ $assignment->title }}
                                            </p>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </a>
                    @empty
                        <div class="col-span-2 text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <p class="text-gray-500 font-medium">Bạn chưa được phân công lớp nào</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
