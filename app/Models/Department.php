<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'department_name',
        'department_code',
    ];

    public function students(): HasMany{
        return $this->hasMany(User::class, 'department_id')
            ->where('role', 'student');
    }

    public function lecturers(): HasMany{
        return $this->hasMany(User::class, 'department_id')
            ->where('role', 'lecturer');
    }

    public function sections(): HasMany{
        return $this->hasMany(CourseSection::class);
    }
}
