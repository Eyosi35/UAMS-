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
        Schema::create('course_section_lecturer', function (Blueprint $table) {
            $table->foreignUuid('course_section_id')
                ->constrained('course_sections')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignUuid('lecturer_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->primary(['course_section_id', 'lecturer_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_section_lecturer');
    }

};
