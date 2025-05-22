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
        Schema::table('recipes', function (Blueprint $table) {
            $table->string('prep_time')->change();
            $table->string('cook_time')->change();
            $table->string('servings')->change();
            $table->string('protein')->change();
            $table->string('carbohydrates')->change();
            $table->string('fats')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->integer('prep_time')->change();
            $table->integer('cook_time')->change();
            $table->integer('servings')->change();
            $table->integer('protein')->change();
            $table->integer('carbohydrates')->change();
            $table->integer('fats')->change();
        });
    }
};
