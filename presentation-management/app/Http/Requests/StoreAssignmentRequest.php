<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        
        // Admin luôn được phép
        if ($user->hasRole('admin')) {
            return true;
        }

        // Teacher phải là GV của lớp
        if ($user->hasRole('teacher') && $this->classroom_id) {
            return $user->teachingClassrooms()
                ->where('classrooms.id', $this->classroom_id)
                ->exists();
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'due_date' => ['required', 'date', 'after:now'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'classroom_id' => 'lớp học',
            'title' => 'tiêu đề',
            'description' => 'mô tả',
            'due_date' => 'hạn nộp',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'classroom_id.required' => 'Lớp học là bắt buộc.',
            'classroom_id.exists' => 'Lớp học không tồn tại.',
            'title.required' => 'Tiêu đề là bắt buộc.',
            'title.max' => 'Tiêu đề không được vượt quá :max ký tự.',
            'due_date.required' => 'Hạn nộp là bắt buộc.',
            'due_date.date' => 'Hạn nộp không hợp lệ.',
            'due_date.after' => 'Hạn nộp phải sau thời điểm hiện tại.',
        ];
    }
}
