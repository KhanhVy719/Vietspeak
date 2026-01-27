<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        
        // Chỉ student hoặc admin
        return $user->hasRole('admin') || $user->hasRole('student');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'assignment_id' => ['required', 'exists:assignments,id'],
            'file' => [
                'required',
                'file',
                'max:2100000', // 2GB in KB
                'mimes:jpeg,png,jpg,gif,svg,mp4,mov,avi,wmv,doc,docx,pdf,xls,xlsx,ppt,pptx,zip,rar',
            ],
            'note' => ['nullable', 'string', 'max:1000'],
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
            'assignment_id' => 'bài tập',
            'file' => 'file bài nộp',
            'note' => 'ghi chú',
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
            'assignment_id.required' => 'Bài tập là bắt buộc.',
            'assignment_id.exists' => 'Bài tập không tồn tại.',
            'file.required' => 'File bài nộp là bắt buộc.',
            'file.file' => 'File không hợp lệ.',
            'file.max' => 'File không được vượt quá 2GB.',
            'file.mimes' => 'File phải có định dạng: Ảnh, Video, PDF, Word, Excel, PowerPoint hoặc nén (zip, rar).',
            'note.max' => 'Ghi chú không được vượt quá :max ký tự.',
        ];
    }
}
