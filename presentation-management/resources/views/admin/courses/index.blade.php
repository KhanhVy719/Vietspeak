<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Quản lý khóa học
            </h2>
            <a href="{{ route('admin.courses.create') }}" class="w-full sm:w-auto bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                + Tạo khóa học mới
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <!-- Mobile: Card view, Desktop: Table view -->
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mã</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên khóa học</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Giảng viên</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cấp độ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Học viên</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($courses as $course)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $course->code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $course->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $course->duration }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $course->instructor ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $course->level == 'beginner' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $course->level == 'intermediate' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $course->level == 'advanced' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ $course->level }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $course->students_count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $course->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $course->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.courses.show', $course) }}" class="text-blue-600 hover:text-blue-900 mr-3">Xem</a>
                                            <a href="{{ route('admin.courses.manage-students', $course) }}" class="text-green-600 hover:text-green-900 mr-3">Học viên</a>
                                            <a href="{{ route('admin.courses.edit', $course) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Sửa</a>
                                            <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" 
                                                        onclick="return confirm('Xóa khóa học này?')">Xóa</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                            Chưa có khóa học nào
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="sm:hidden space-y-4">
                        @forelse($courses as $course)
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $course->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $course->code }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs leading-5 font-semibold rounded-full 
                                        {{ $course->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $course->status }}
                                    </span>
                                </div>
                                
                                <div class="space-y-2 text-sm mb-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Thời lượng:</span>
                                        <span class="font-medium">{{ $course->duration }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Giảng viên:</span>
                                        <span class="font-medium">{{ $course->instructor ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Cấp độ:</span>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $course->level == 'beginner' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $course->level == 'intermediate' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $course->level == 'advanced' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ $course->level }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Học viên:</span>
                                        <span class="font-medium">{{ $course->students_count }} người</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2 pt-3 border-t">
                                    <a href="{{ route('admin.courses.show', $course) }}" class="text-center bg-blue-50 text-blue-600 hover:bg-blue-100 py-2 px-3 rounded text-sm font-medium">
                                        👁️ Xem
                                    </a>
                                    <a href="{{ route('admin.courses.manage-students', $course) }}" class="text-center bg-green-50 text-green-600 hover:bg-green-100 py-2 px-3 rounded text-sm font-medium">
                                        👥 Học viên
                                    </a>
                                    <a href="{{ route('admin.courses.edit', $course) }}" class="text-center bg-indigo-50 text-indigo-600 hover:bg-indigo-100 py-2 px-3 rounded text-sm font-medium">
                                        ✏️ Sửa
                                    </a>
                                    <form action="{{ route('admin.courses.destroy', $course) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full text-center bg-red-50 text-red-600 hover:bg-red-100 py-2 px-3 rounded text-sm font-medium" 
                                                onclick="return confirm('Xóa khóa học này?')">
                                            🗑️ Xóa
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                Chưa có khóa học nào
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        {{ $courses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
