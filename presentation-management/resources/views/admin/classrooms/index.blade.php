<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Quản lý lớp học
            </h2>
            <a href="{{ route('admin.classrooms.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Tạo lớp mới
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Classrooms Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên lớp</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mô tả</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Học sinh</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Giáo viên</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bài tập</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($classrooms as $classroom)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $classroom->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $classroom->name }}</td>
                                <td class="px-6 py-4">{{ Str::limit($classroom->description, 50) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $classroom->students_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $classroom->teachers_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $classroom->assignments_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.classrooms.show', $classroom) }}" class="text-blue-600 hover:text-blue-900 mr-2">Xem</a>
                                    <a href="{{ route('admin.classrooms.edit', $classroom) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Sửa</a>
                                    <a href="{{ route('admin.classrooms.manage-members', $classroom) }}" class="text-green-600 hover:text-green-900 mr-2">Thành viên</a>
                                    <form action="{{ route('admin.classrooms.destroy', $classroom) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" 
                                                onclick="return confirm('Bạn có chắc muốn xóa lớp này?')">
                                            Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">Chưa có lớp học nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $classrooms->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
