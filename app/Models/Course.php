<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    /** @use HasFactory<\Database\Factories\CourseFactory> */
    use HasFactory, HasUuids;

    public $timestamps = false;

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

    public function lecturers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'course_lecturer',
            'course_id',
            'lecturer_id'
        )->withTimestamps();
    }

    public function offerings(): HasMany{
    return $this->hasMany(CourseOffering::class);
    }
}
