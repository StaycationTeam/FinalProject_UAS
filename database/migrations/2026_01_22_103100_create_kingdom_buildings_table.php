<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kingdom_buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kingdom_id')->constrained('kingdoms')->onDelete('cascade');
            $table->foreignId('building_id')->constrained('buildings')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->integer('level')->default(1);
            $table->timestamp('last_production_at')->nullable();
            $table->timestamps();

            // Composite index for faster queries
            $table->index(['kingdom_id', 'building_id']);
            $table->unique(['kingdom_id', 'building_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kingdom_buildings');
    }
};
