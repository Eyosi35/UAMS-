<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'phone_number'
    ];
    
    protected $guarded = ['role', 'password', 'remember_token'];
   
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(){
        return $this->role == 'admin';
    }

    public function isLecturer(){
        $this->role == 'lecturer';
    }

    public function isStudent(){
        $this->role == 'student';
    }

    public function enrollments():HasMany{
        return $this->hasMany(Enrollment::class);
    }

    public function courses() :BelongsToMany
    {
        return $this->belongsToMany(
            Course::class, 
            'course_lecturer',
            'lecturer_id',
            'course_id'
        )->withTimestamps();
    }
    
    public function teachingSections(): BelongsToMany{
        return $this->belongsToMany(
            CourseSection::class,
            'course_section_lecturer',
            'lecturer_id',
            'course_section_id'
        )->withTimestamps();
    }
}
