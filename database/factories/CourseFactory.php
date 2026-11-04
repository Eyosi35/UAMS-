<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'course_title' => fake()->words(3, true),
            'course_code' => fake()->unique()->bothify('CS###'),
            'credit_hour' => 3,
            'prerequisit' => 'None',
        ];
    }
}