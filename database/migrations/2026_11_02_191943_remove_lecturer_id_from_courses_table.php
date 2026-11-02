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
    Schema::table('courses', function (Blueprint $table) {
        $table->dropForeign(['lecturer_id']);
        $table->dropColumn('lecturer_id');
    });
}

public function down(): void
{
    Schema::table('courses', function (Blueprint $table) {
        $table->foreignUuid('lecturer_id')
            ->constrained('users');
    });
}
};
