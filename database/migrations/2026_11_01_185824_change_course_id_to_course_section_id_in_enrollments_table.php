<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {

            $table->dropUnique(['student_id', 'course_id']);
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');

            $table->foreignUuid('course_section_id')
                ->constrained('course_sections')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->unique(['student_id', 'course_section_id']);
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropUnique(['student_id', 'course_section_id']);
            $table->dropForeign(['course_section_id']);
            $table->dropColumn('course_section_id');

            $table->foreignUuid('course_id')
                ->constrained('courses')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->unique(['student_id', 'course_id']);
        });
    }

};
