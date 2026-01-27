<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Chi tiết khóa học') }}: {{ $course->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.courses.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                    ← Quay lại danh sách
                </a>
                <a href="{{ route('admin.courses.manage-students', $course) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    Quản lý học viên
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Course Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Thông tin chung</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="text-gray-500 block text-sm">Tên khóa học</span>
                                <span class="font-medium text-lg">{{ $course->name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-sm">Mã khóa học</span>
                                <span class="font-mono bg-gray-100 px-2 py-1 rounded text-sm">{{ $course->code }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-sm">Giảng viên</span>
                                <span class="font-medium">{{ $course->instructor }}</span>
                            </div>
                            <div class="flex space-x-6">
                                <div>
                                    <span class="text-gray-500 block text-sm">Thời lượng</span>
                                    <span class="font-medium">{{ $course->duration }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 block text-sm">Cấp độ</span>
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                                        {{ $course->level === 'beginner' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $course->level === 'intermediate' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $course->level === 'advanced' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($course->level) }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-sm">Giá học phí</span>
                                <span class="font-bold text-xl text-primary">{{ number_format($course->price) }}đ</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Mô tả</h3>
                        <p class="text-gray-600 leading-relaxed">{{ $course->description }}</p>

                        <div class="mt-8 border-t pt-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Thống kê sơ bộ</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <span class="text-blue-600 block text-sm font-semibold">Học viên</span>
                                    <span class="text-2xl font-bold text-blue-800">{{ $course->students->count() }}</span>
                                </div>
                                <div class="bg-purple-50 p-4 rounded-lg">
                                    <span class="text-purple-600 block text-sm font-semibold">Trạng thái</span>
                                    <span class="text-2xl font-bold text-purple-800">{{ ucfirst($course->status) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enrolled Students Preview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Danh sách học viên ({{ $course->students->count() }})</h3>
                    <a href="{{ route('admin.courses.manage-students', $course) }}" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                        Quản lý chi tiết &rarr;
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày ghi danh</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiến độ</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($course->students->take(5) as $student)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $student->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($student->pivot->enrolled_at)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 max-w-[100px]">
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $student->pivot->progress }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-500 mt-1 inline-block">{{ $student->pivot->progress }}%</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500 text-sm">
                                        Chưa có học viên nào.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
