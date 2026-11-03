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
        Schema::table('course_sections', function (Blueprint $table) {
            $table->foreignUuid('department_id')
                ->constrained('departments')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->unsignedTinyInteger('year_of_study');

           
            $table->dropUnique(['course_offering_id','section_name']);

            $table->unique([
                'course_offering_id',
                'department_id',
                'year_of_study',
                'section_name'
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('course_sections', function (Blueprint $table) {
            $table->dropUnique([
                'course_offering_id',
                'department_id',
                'year_of_study',
                'section_name'
            ]);

            $table->unique([
                'course_offering_id',
                'section_name'
            ]);

            $table->dropForeign(['department_id']);
            $table->dropColumn(['department_id', 'year_of_study']);
        });
    }

};
