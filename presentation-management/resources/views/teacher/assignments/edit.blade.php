<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Chỉnh sửa bài tập: {{ $assignment->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('teacher.assignments.update', $assignment) }}">
                    @csrf
                    @method('PUT')

                    <!-- Classroom -->
                    <div class="mb-4">
                        <x-input-label for="classroom_id" :value="__('Lớp học')" />
                        <select id="classroom_id" name="classroom_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                            @foreach($classrooms as $classroom)
                                <option value="{{ $classroom->id }}" {{ old('classroom_id', $assignment->classroom_id) == $classroom->id ? 'selected' : '' }}>
                                    {{ $classroom->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('classroom_id')" class="mt-2" />
                    </div>

                    <!-- Title -->
                    <div class="mb-4">
                        <x-input-label for="title" :value="__('Tiêu đề bài tập')" />
                        <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $assignment->title)" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <x-input-label for="description" :value="__('Mô tả chi tiết')" />
                        <textarea id="description" name="description" rows="6" 
                                  class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $assignment->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <!-- Due Date -->
                    <div class="mb-4">
                        <x-input-label for="due_date" :value="__('Hạn nộp')" />
                        <input id="due_date" name="due_date" type="datetime-local" 
                               class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" 
                               value="{{ old('due_date', $assignment->due_date->format('Y-m-d\TH:i')) }}" required />
                        <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('teacher.assignments.show', $assignment) }}" class="mr-4 text-gray-600 hover:text-gray-900">
                            Hủy
                        </a>
                        <x-primary-button>
                            {{ __('Cập nhật') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
