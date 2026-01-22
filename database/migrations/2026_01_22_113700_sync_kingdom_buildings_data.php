<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration syncs data from kingdoms table (old schema) to kingdom_buildings (new schema)
     */
    public function up(): void
    {
        // Get building IDs
        $barracksId = DB::table('buildings')->where('type', 'barracks')->value('id');
        $mineId = DB::table('buildings')->where('type', 'mine')->value('id');
        $wallsId = DB::table('buildings')->where('type', 'walls')->value('id');

        if (!$barracksId || !$mineId || !$wallsId) {
            throw new \Exception('Required buildings not found in database. Please run BuildingSeeder first.');
        }

        // Get all kingdoms with buildings
        $kingdoms = DB::table('kingdoms')
            ->where(function($query) {
                $query->where('barracks_count', '>', 0)
                      ->orWhere('mines_count', '>', 0)
                      ->orWhere('walls_count', '>', 0);
            })
            ->get();

        foreach ($kingdoms as $kingdom) {
            $now = now();

            // Sync Barracks
            if ($kingdom->barracks_count > 0) {
                DB::table('kingdom_buildings')->updateOrInsert(
                    [
                        'kingdom_id' => $kingdom->id,
                        'building_id' => $barracksId,
                    ],
                    [
                        'quantity' => $kingdom->barracks_count,
                        'level' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }

            // Sync Mines
            if ($kingdom->mines_count > 0) {
                DB::table('kingdom_buildings')->updateOrInsert(
                    [
                        'kingdom_id' => $kingdom->id,
                        'building_id' => $mineId,
                    ],
                    [
                        'quantity' => $kingdom->mines_count,
                        'level' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }

            // Sync Walls
            if ($kingdom->walls_count > 0) {
                DB::table('kingdom_buildings')->updateOrInsert(
                    [
                        'kingdom_id' => $kingdom->id,
                        'building_id' => $wallsId,
                    ],
                    [
                        'quantity' => $kingdom->walls_count,
                        'level' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }

        // Log results
        $synced = DB::table('kingdom_buildings')->count();
        echo "✅ Synced {$synced} building records to kingdom_buildings table\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get building IDs
        $barracksId = DB::table('buildings')->where('type', 'barracks')->value('id');
        $mineId = DB::table('buildings')->where('type', 'mine')->value('id');
        $wallsId = DB::table('buildings')->where('type', 'walls')->value('id');

        // Remove synced data (keep only manually created ones)
        DB::table('kingdom_buildings')
            ->whereIn('building_id', [$barracksId, $mineId, $wallsId])
            ->delete();
    }
};
