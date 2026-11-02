<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'course_section_id' => [
                'required',
                'uuid',
                'exists:course_sections,id',
            ],

            'student_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'student_ids.*' => [
                'required',
                'uuid',
                'distinct',
                'exists:users,id',
            ],
        ];
    }
}
