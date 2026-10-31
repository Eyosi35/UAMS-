<?php

use App\Models\Course;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->uuid()->primary();

            $table->foreignUuid('student_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            
             $table->foreignUuid('course_id')
                ->constrained('courses')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->unique(['student_id', 'course_id']);
            $table->timestamps();
        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
