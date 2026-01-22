<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_configs', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert default configurations
        DB::table('game_configs')->insert([
            [
                'key' => 'default_gold_per_minute',
                'value' => '5',
                'description' => 'Default gold generation per minute for all kingdoms',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'gold_mine_production',
                'value' => '10',
                'description' => 'Gold production per minute per mine',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'barracks_troop_production',
                'value' => '5',
                'description' => 'Troops produced per minute per barracks',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'attack_gold_steal_percentage',
                'value' => '90',
                'description' => 'Percentage of gold stolen on successful attack',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('game_configs');
    }
};
