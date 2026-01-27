<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Lớp: {{ $classroom->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Classroom Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-2">{{ $classroom->name }}</h3>
                <p class="text-gray-600">{{ $classroom->description }}</p>
            </div>

            <!-- Students with Grades Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Học sinh & Điểm trung bình ({{ $classroom->students->count() }})</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Học sinh</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tổng bài nộp</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Đã chấm</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Điểm TB</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($classroom->students as $student)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $student->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $student->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $studentGrades[$student->id]['total_submissions'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $studentGrades[$student->id]['graded_submissions'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($studentGrades[$student->id]['average_score'])
                                            <span class="font-semibold text-lg">
                                                {{ number_format($studentGrades[$student->id]['average_score'], 1) }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Chưa có học sinh</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Group Management -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Quản lý Tổ/Nhóm</h3>
                    <!-- Form tạo nhóm -->
                    <form action="{{ route('teacher.groups.store', $classroom) }}" method="POST" class="flex flex-wrap gap-2 items-center">
                        @csrf
                        <input type="text" name="name" placeholder="Tên nhóm mới..." class="border rounded px-3 py-1 text-sm" required>
                        <input type="text" name="description" placeholder="Mô tả (tùy chọn)" class="border rounded px-3 py-1 text-sm hidden sm:block">
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm whitespace-nowrap">
                            + Tạo Nhóm
                        </button>
                    </form>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                     @forelse($classroom->groups as $group)
                        <div class="border rounded-lg p-4 shadow-sm relative bg-gray-50">
                            <div class="flex justify-between items-center mb-3 border-b pb-2">
                                <div>
                                    <h4 class="font-bold text-gray-800">{{ $group->name }}</h4>
                                    @if($group->description) <p class="text-xs text-gray-500">{{ $group->description }}</p> @endif
                                </div>
                                <form action="{{ route('teacher.groups.destroy', $group) }}" method="POST" onsubmit="return confirm('Xóa nhóm này?');">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700 text-xs bg-white border px-2 py-1 rounded">Xóa</button>
                                </form>
                            </div>
                            
                            <ul class="space-y-2 mb-3 max-h-40 overflow-y-auto pr-1">
                                @forelse($group->users as $member)
                                    <li class="flex justify-between items-center text-sm bg-white p-2 rounded shadow-sm border border-gray-100">
                                        <div class="flex items-center space-x-2">
                                            <!-- Simple avatar placeholder if needed -->
                                            <span>{{ $member->name }}</span>
                                        </div>
                                        <form action="{{ route('teacher.groups.remove-member', [$group, $member]) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button class="text-gray-400 hover:text-red-600 ml-2" title="Xóa khỏi nhóm">×</button>
                                        </form>
                                    </li>
                                @empty
                                    <li class="text-gray-400 text-sm italic py-2 text-center">Trống</li>
                                @endforelse
                            </ul>

                            <!-- Add Member Form -->
                            <form action="{{ route('teacher.groups.add-member', $group) }}" method="POST" class="mt-2 text-sm border-t pt-2">
                                @csrf
                                <div class="flex gap-1">
                                    <select name="user_id" class="flex-1 text-xs border rounded p-1 w-full" required>
                                        <option value="">+ Thêm thành viên</option>
                                        @foreach($classroom->students as $student)
                                            {{-- Filter: Only show students NOT in any group of this classroom OR check specific logic --}}
                                            {{-- Current logic: Check if in THIS group --}}
                                            @if(!$group->users->contains($student->id))
                                                <option value="{{ $student->id }}">{{ $student->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-3 rounded text-xs font-bold">Thêm</button>
                                </div>
                            </form>
                        </div>
                     @empty
                        <div class="col-span-full text-center py-8 text-gray-500 bg-gray-50 rounded border border-dashed">
                             Chưa có nhóm nào. Hãy tạo nhóm mới!
                        </div>
                     @endforelse
                </div>
            </div>

            <!-- Assignments -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Bài tập ({{ $classroom->assignments->count() }})</h3>
                    <a href="{{ route('teacher.assignments.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                        Tạo bài tập mới
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($classroom->assignments as $assignment)
                        <div class="border rounded p-4">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <a href="{{ route('teacher.assignments.show', $assignment) }}" class="font-semibold text-lg hover:text-blue-600">
                                        {{ $assignment->title }}
                                    </a>
                                    <p class="text-sm text-gray-600 mt-1">{{ Str::limit($assignment->description, 100) }}</p>
                                    <p class="text-xs text-gray-500 mt-2">
                                        Hạn nộp: {{ $assignment->due_date->format('d/m/Y H:i') }}
                                        @if($assignment->isOverdue())
                                            <span class="text-red-600">(Đã quá hạn)</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="ml-4">
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded text-sm">
                                        {{ $assignment->submissions->count() }} bài nộp
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">Chưa có bài tập nào</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
