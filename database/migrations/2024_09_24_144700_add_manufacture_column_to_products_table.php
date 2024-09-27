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
        Schema::table('products', function (Blueprint $table) {
            $table->longText('manufacturer')->nullable();
            $table->longText('allergens')->nullable();
            $table->longText('composition')->nullable();
            $table->longText('storing')->nullable();
            $table->longText('nutritional')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['manufacturer', 'allergens', 'composition', 'storing', 'nutritional']);
        });
    }
};
