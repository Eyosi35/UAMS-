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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignUuid('department_id')->nullable()
                ->constrained('departments')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->unsignedTinyInteger('year_of_study')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn(['department_id', 'year_of_study']);
        });
    }

};
