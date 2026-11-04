<?php

namespace Database\Factories;

use App\Models\CourseSection;
use App\Models\CourseOffering;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseSectionFactory extends Factory
{
    protected $model = CourseSection::class;

    public function definition(): array
    {
        return [
            'course_offering_id' => CourseOffering::factory(),
            'section_name' => 'A',
            'capacity' => 35,
            'department_id' => Department::factory(),
            'year_of_study' => 2,
        ];
    }
}