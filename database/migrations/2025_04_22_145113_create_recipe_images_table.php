<?php

use Illuminate\Support\Collection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recipe_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('recipes');
            $table->string('path', 255);
            $table->string('url', 255);
            $table->string('mime', 55)->nullable();
            $table->integer('size')->nullable();
            $table->integer('position')->nullable();
            $table->timestamps();
        });

        DB::table('recipes')
            ->chunkById(100, function (Collection $recipes) {
                $recipes = $recipes->map(function ($r) {
                    return [
                        'recipe_id' => $r->id,
                        'path' => '',
                        'url' => $r->image_url,
                        'position' => 1,
                        'created_at' => \Carbon\Carbon::now(),
                        'updated_at' => \Carbon\Carbon::now()
                    ];
                });

                DB::table('recipe_images')->insert($recipes->all());

            });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('recipes')
            ->select('id')
            ->chunkById(100, function (Collection $recipes) {
                foreach ($recipes as $recipe) {
                    $image = DB::table('recipe_images')
                        ->select(['recipe_id', 'url', 'mime', 'size'])
                        ->where('recipe_id', $recipe->id)
                        ->first();
                    if ($image) {
                        DB::table('recipes')
                            ->where('id', $image->recipe_id)
                            ->update([
                                'image' => $image->url,
                                'image_mime' => $image->mime,
                                'image_size' => $image->size
                            ]);
                    }
                }
            });

        Schema::dropIfExists('recipe_images');
    }
};
