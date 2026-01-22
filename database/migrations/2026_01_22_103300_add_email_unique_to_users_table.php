<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make email unique if not already
        Schema::table('users', function (Blueprint $table) {
            // Drop existing index if exists
            try {
                $table->dropIndex(['email']);
            } catch (\Exception $e) {
                // Index might not exist, continue
            }
            
            // Add unique constraint
            $table->unique('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email']);
        });
    }
};
