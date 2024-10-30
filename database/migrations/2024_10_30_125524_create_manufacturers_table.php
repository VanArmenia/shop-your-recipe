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
        Schema::create('manufacturers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable(); // This will allow for images in the description
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('manufacturer'); // Remove the old column
            $table->foreignId('manufacturer_id')
                ->nullable()
                ->constrained('manufacturers')
                ->onDelete('set null'); // Adds a foreign key constraint to manufacturers table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturers');

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['manufacturer_id']); // Remove foreign key
            $table->dropColumn('manufacturer_id'); // Remove the new column
            $table->longText('manufacturer')->nullable(); // Restore the old column if needed
        });
    }
};
