<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSection extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'course_offering_id',
        'section_name',
        'capacity',
        'department_id',
        'year_of_study',
    ];

    public function courseOffering(): BelongsTo{
        return $this->belongsTo(CourseOffering::class);
    }

    public function lecturers(): BelongsToMany{
        return $this->belongsToMany(
            User::class,
            'course_section_lecturer',
            'course_section_id',
            'lecturer_id'
        )->withTimestamps();
    }

    public function enrollments(): HasMany{
        return $this->hasMany(Enrollment::class);
    }
}
