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
        Schema::table('recipe_images', function (Blueprint $table) {
            // Drop the existing foreign key
            $table->dropForeign(['recipe_id']);

            // Add the new foreign key with cascade on delete
            $table->foreign('recipe_id')
                ->references('id')
                ->on('recipes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipe_images', function (Blueprint $table) {
            // Drop the new foreign key
            $table->dropForeign(['recipe_id']);

            // Add the original foreign key without cascade
            $table->foreign('recipe_id')
                ->references('id')
                ->on('recipes');
        });
    }
};
