<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('course_title');
            $table->string('course_code')->unique();
            $table->unsignedInteger('capacity')->default(35);

            $table->unsignedTinyInteger('credit_hour');    
            
            $table->string('prerequisit');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
