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
        
    Schema::create('course_lecturer', function (Blueprint $table) {
        $table->foreignUuid('course_id')
            ->constrained('courses')
            ->cascadeOnDelete();

        $table->foreignUuid('lecturer_id')
            ->constrained('users')
            ->cascadeOnDelete();

        $table->primary(['course_id', 'lecturer_id']);

        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_lecturer');
    }
};
