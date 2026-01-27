<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 animate-fade-in">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Users Stat -->
                <div class="stat-card group">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-gray-600 text-sm font-medium mb-1">Tổng người dùng</div>
                            <div class="text-3xl font-bold gradient-primary bg-clip-text text-transparent">
                                {{ $stats['total_users'] }}
                            </div>
                        </div>
                        <div class="icon-primary group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Classrooms Stat -->
                <div class="stat-card group">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-gray-600 text-sm font-medium mb-1">Tổng lớp học</div>
                            <div class="text-3xl font-bold gradient-secondary bg-clip-text text-transparent">
                                {{ $stats['total_classrooms'] }}
                            </div>
                        </div>
                        <div class="icon-success group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Assignments Stat -->
                <div class="stat-card group">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-gray-600 text-sm font-medium mb-1">Tổng bài tập</div>
                            <div class="text-3xl font-bold gradient-accent bg-clip-text text-transparent">
                                {{ $stats['total_assignments'] }}
                            </div>
                        </div>
                        <div class="icon-warning group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Submissions Stat -->
                <div class="stat-card group">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-gray-600 text-sm font-medium mb-1">Tổng bài nộp</div>
                            <div class="text-3xl font-bold text-teal-600">
                                {{ $stats['total_submissions'] }}
                            </div>
                        </div>
                        <div class="icon-container bg-teal-100 text-teal-600 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 animate-slide-up">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Quản lý nhanh
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- User Management -->
                    <a href="{{ route('admin.users.index') }}" 
                       class="card-elevated p-5 hover:shadow-xl hover:scale-105 transition-all duration-300 group">
                        <div class="flex items-center">
                            <div class="icon-primary mr-4 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors">Quản lý người dùng</h4>
                                <p class="text-sm text-gray-600 mt-1">Quản lý giáo viên và học sinh</p>
                            </div>
                        </div>
                    </a>

                    <!-- Classroom Management -->
                    <a href="{{ route('admin.classrooms.index') }}" 
                       class="card-elevated p-5 hover:shadow-xl hover:scale-105 transition-all duration-300 group">
                        <div class="flex items-center">
                            <div class="icon-success mr-4 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 group-hover:text-green-600 transition-colors">Quản lý lớp học</h4>
                                <p class="text-sm text-gray-600 mt-1">Phân công thành viên</p>
                            </div>
                        </div>
                    </a>

                    <!-- Course Management -->
                    <a href="{{ route('admin.courses.index') }}" 
                       class="card-elevated p-5 hover:shadow-xl hover:scale-105 transition-all duration-300 group">
                        <div class="flex items-center">
                            <div class="icon-container bg-purple-100 text-purple-600 mr-4 group-hover:scale-110 transition-transform rounded-md p-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-gray-900 font-bold group-hover:text-purple-600">Quản lý khóa học</h4>
                                <p class="text-gray-500 text-sm">Quản lý khóa học</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Recent Submissions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Bài nộp gần đây</h3>
                    <div class="space-y-3">
                        @forelse($recent_submissions as $submission)
                            <div class="border-b pb-2">
                                <div class="font-medium">{{ $submission->student->name }}</div>
                                <div class="text-sm text-gray-600">{{ $submission->assignment->title }}</div>
                                <div class="text-xs text-gray-500">{{ $submission->submitted_at->diffForHumans() }}</div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">Chưa có bài nộp</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Assignments -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Bài tập mới</h3>
                    <div class="space-y-3">
                        @forelse($recent_assignments as $assignment)
                            <div class="border-b pb-2">
                                <div class="font-medium">{{ $assignment->title }}</div>
                                <div class="text-sm text-gray-600">{{ $assignment->classroom->name }}</div>
                                <div class="text-xs text-gray-500">{{ $assignment->created_at->diffForHumans() }}</div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">Chưa có bài tập</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
