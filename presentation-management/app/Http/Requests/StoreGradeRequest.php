<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGradeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        
        // Admin hoặc Teacher
        return $user->hasRole('admin') || $user->hasRole('teacher');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'submission_id' => ['required', 'exists:submissions,id'],
            'score' => ['required', 'numeric', 'min:0', 'max:10'],
            'comment' => ['nullable', 'string', 'max:2000'],
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
            'submission_id' => 'bài nộp',
            'score' => 'điểm',
            'comment' => 'nhận xét',
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
            'submission_id.required' => 'Bài nộp là bắt buộc.',
            'submission_id.exists' => 'Bài nộp không tồn tại.',
            'score.required' => 'Điểm là bắt buộc.',
            'score.numeric' => 'Điểm phải là số.',
            'score.min' => 'Điểm phải từ 0 đến 10.',
            'score.max' => 'Điểm phải từ 0 đến 10.',
            'comment.max' => 'Nhận xét không được vượt quá :max ký tự.',
        ];
    }
}
