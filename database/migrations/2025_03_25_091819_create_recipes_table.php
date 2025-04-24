<?php

use App\Models\Category;
use App\Models\RecipeCategory;
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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('prep_time')->nullable();
            $table->integer('cook_time')->nullable();
            $table->integer('servings')->nullable();
            $table->string('difficulty')->nullable();
            $table->string('tags')->nullable();
            $table->string('image_url')->nullable();
            $table->string('video_url')->nullable();
            $table->integer('rating')->nullable();
            $table->integer('calories')->nullable();
            $table->integer('protein')->nullable();
            $table->integer('carbohydrates')->nullable();
            $table->integer('fats')->nullable();
            $table->boolean('is_vegan')->default(false);
            $table->boolean('is_vegetarian')->default(false);
            $table->boolean('is_gluten_free')->default(false);
            $table->string('season')->nullable();
            $table->string('occasion')->nullable();
            $table->json('gallery_images')->nullable();
            $table->json('steps_video_urls')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_featured')->default(false);
            $table->foreignId('region_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('recipe_categories')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
