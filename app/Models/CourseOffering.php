<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CourseOffering extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'course_id',
        'semester_id',
        'capacity',
        'registration_start',
        'registration_end',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'registration_start' => 'date',
            'registration_end' => 'date',
        ];
    }

    public function course(): BelongsTo{
        return $this->belongsTo(Course::class);
    }

    public function semester(): BelongsTo{
        return $this->belongsTo(Semester::class);
    }

    public function lecturers(): BelongsToMany{
        return $this->belongsToMany(
            User::class,
            'course_offering_lecturer',
            'course_offering_id',
            'lecturer_id'
        )->withTimestamps();
    }

    public function enrollments(): HasMany{
        return $this->hasMany(Enrollment::class);
    }
}
