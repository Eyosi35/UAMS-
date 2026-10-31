<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    /** @use HasFactory<\Database\Factories\CourseFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'course_title',
        'course_code',
        'credit_hour',
        'prerequisit'
    ];

    public function enrollments() :HasMany
    {
        return $this->hasMany(Enrollment::class);
    }
}
