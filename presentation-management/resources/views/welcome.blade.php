<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hệ thống Quản lý Bài tập</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <!-- Hero Section -->
    <div class="relative min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 via-purple-500 to-teal-500 overflow-hidden">
        <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center animate-fade-in">
            <!-- Logo/Icon -->
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-white rounded-full shadow-2xl mb-6 animate-scale-in">
                    <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h1 class="text-5xl md:text-7xl font-bold text-white mb-4 animate-slide-up">
                    Quản lý Bài tập
                </h1>
                <p class="text-xl md:text-2xl text-white text-opacity-90 mb-8 animate-slide-up" style="animation-delay: 0.1s;">
                    Hệ thống quản lý bài tập và chấm điểm chuyên nghiệp
                </p>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12 animate-slide-up" style="animation-delay: 0.2s;">
                <div class="glass p-6 rounded-xl backdrop-blur-lg bg-white bg-opacity-20 hover:bg-opacity-30 transition-all duration-300 group">
                    <div class="w-12 h-12 bg-white bg-opacity-30 rounded-lg flex items-center justify-center mb-4 mx-auto group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-white font-bold text-lg mb-2">Quản lý Người dùng</h3>
                    <p class="text-white text-opacity-80 text-sm">Quản lý giáo viên và học sinh hiệu quả</p>
                </div>

                <div class="glass p-6 rounded-xl backdrop-blur-lg bg-white bg-opacity-20 hover:bg-opacity-30 transition-all duration-300 group">
                    <div class="w-12 h-12 bg-white bg-opacity-30 rounded-lg flex items-center justify-center mb-4 mx-auto group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-white font-bold text-lg mb-2">Nộp & Chấm Bài</h3>
                    <p class="text-white text-opacity-80 text-sm">Nộp bài trực tuyến và chấm điểm nhanh chóng</p>
                </div>

                <div class="glass p-6 rounded-xl backdrop-blur-lg bg-white bg-opacity-20 hover:bg-opacity-30 transition-all duration-300 group">
                    <div class="w-12 h-12 bg-white bg-opacity-30 rounded-lg flex items-center justify-center mb-4 mx-auto group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-white font-bold text-lg mb-2">Thống kê & Báo cáo</h3>
                    <p class="text-white text-opacity-80 text-sm">Theo dõi tiến độ và kết quả học tập</p>
                </div>
            </div>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center animate-slide-up" style="animation-delay: 0.3s;">
                @auth
                    <a href="{{ url('/dashboard') }}" 
                       class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-bold rounded-xl shadow-2xl hover:shadow-glow-blue hover:scale-105 transition-all duration-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Vào Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-bold rounded-xl shadow-2xl hover:shadow-glow-blue hover:scale-105 transition-all duration-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        Đăng nhập
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" 
                           class="inline-flex items-center px-8 py-4 bg-white bg-opacity-20 backdrop-blur-lg text-white font-bold rounded-xl border-2 border-white hover:bg-white hover:text-blue-600 hover:scale-105 transition-all duration-300">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            Đăng ký
                        </a>
                    @endif
                @endauth
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <svg class="w-6 h-6 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </div>
    </div>
</body>
</html>
