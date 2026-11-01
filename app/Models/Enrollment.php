<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'student_id',
        'course_section_id',
    ];

    public function student(): BelongsTo{
        return $this->belongsTo(User::class, 'student_id');
    }

    public function courseSection(): BelongsTo{
        return $this->belongsTo(CourseSection::class);
    }
}
