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
        Schema::table('recipe_categories', function (Blueprint $table) {
            // Drop the incorrect foreign key
            $table->dropForeign(['parent_id']);

            // Add the correct self-referencing foreign key
            $table->foreign('parent_id')
                ->references('id')
                ->on('recipe_categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipe_categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);

            // Optional: revert to the old (incorrect) one if you want
            $table->foreign('parent_id')
                ->references('id')
                ->on('categories') // ← original incorrect table
                ->onDelete('cascade');
        });
    }
};
