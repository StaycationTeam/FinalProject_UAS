<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildingSeeder extends Seeder
{
    public function run()
    {
        $buildings = [
            [
                'name' => 'Castle Kingdom',
                'type' => 'main',
                'description' => 'Bangunan utama kerajaan, menjadi dasar untuk membangun infrastruktur yang lain.',
                'gold_cost' => 100,
                'level' => 1,
                'gold_production' => 0,
                'troop_production' => 0,
                'defense_bonus' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Barracks',
                'type' => 'barracks',
                'description' => 'Tempat memproduksi pasukan. Setiap barak menghasilkan 5 pasukan per menit.',
                'gold_cost' => 50,
                'level' => 1,
                'gold_production' => 0,
                'troop_production' => 5,
                'defense_bonus' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gold Mine',
                'type' => 'mine',
                'description' => 'Tambang emas yang menambah pendapatan emas sebanyak 10 emas per menit.',
                'gold_cost' => 75,
                'level' => 1,
                'gold_production' => 10,
                'troop_production' => 0,
                'defense_bonus' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Defense Walls',
                'type' => 'walls',
                'description' => 'Pagar pertahanan yang membuat pertahanan makin bagus. Setiap dinding memberikan +10 defense.',
                'gold_cost' => 60,
                'level' => 1,
                'gold_production' => 0,
                'troop_production' => 0,
                'defense_bonus' => 10,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('buildings')->insert($buildings);
    }
}