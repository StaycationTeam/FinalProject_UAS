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
        Schema::create('tribe_appearance_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tribe_id')->constrained('tribes')->onDelete('cascade');
            $table->enum('part_type', ['head', 'body', 'legs', 'arms']);
            $table->string('name');
            $table->string('image_url');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index(['tribe_id', 'part_type']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tribe_appearance_parts');
    }
};
