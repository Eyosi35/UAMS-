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
    Schema::create('course_sections', function (Blueprint $table) {
        $table->uuid('id')->primary();

        $table->foreignUuid('course_offering_id')
            ->constrained('course_offerings')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();

        $table->string('section_name');

        $table->unsignedInteger('capacity')->default(35);

        $table->unique([
            'course_offering_id',
            'section_name'
        ]);

        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_sections');
    }
};
