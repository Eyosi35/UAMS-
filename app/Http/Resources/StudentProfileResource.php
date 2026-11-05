<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,

            'department' => [
                'id' => $this->department?->id,
                'name' => $this->department?->department_name,
                'code' => $this->department?->department_code,
            ],

            'year_of_study' => $this->year_of_study,

            'enrolled_sections' => $this->courseSections->map(
                fn ($section) => [
                    'id' => $section->id,
                    'section_name' => $section->section_name,

                    'course' => [
                        'id' => $section->courseOffering->course->id,
                        'title' => $section->courseOffering->course->course_title,
                        'code' => $section->courseOffering->course->course_code,
                        'credit_hour' => $section->courseOffering->course->credit_hour,
                    ],

                    'semester' => [
                        'id' => $section->courseOffering->semester->id,
                        'name' => $section->courseOffering->semester->name,
                        'academic_year' => $section->courseOffering->semester->academic_year,
                    ],

                    'lecturers' => $section->lecturers->map(fn ($lecturer) => [
                        'id' => $lecturer->id,
                        'name' => $lecturer->name,
                        'email' => $lecturer->email,
                    ]),
                ]
            ),
        ];
    }
}
