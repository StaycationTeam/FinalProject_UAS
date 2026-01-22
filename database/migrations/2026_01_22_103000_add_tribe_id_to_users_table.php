<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tribe_id')->nullable()->after('password')
                  ->constrained('tribes')->onDelete('set null');
            $table->index('tribe_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tribe_id']);
            $table->dropIndex(['tribe_id']);
            $table->dropColumn('tribe_id');
        });
    }
};
