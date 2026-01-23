<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TribeAppearanceSeeder extends Seeder
{
    public function run()
    {
        // IDs based on wfp_final.sql: 1=Marksman, 2=Tank, 3=Mage, 4=Warrior
        $tribes = [
            1 => ['name' => 'Marksman', 'color' => '22c55e', 'icon' => '🏹'],
            2 => ['name' => 'Tank', 'color' => '64748b', 'icon' => '🛡️'],
            3 => ['name' => 'Mage', 'color' => '3b82f6', 'icon' => '🔮'],
            4 => ['name' => 'Warrior', 'color' => 'ef4444', 'icon' => '⚔️'],
        ];

        $parts = [
            ['type' => 'body', 'order' => 1, 'z' => 1],
            ['type' => 'legs', 'order' => 2, 'z' => 1],
            ['type' => 'head', 'order' => 3, 'z' => 2],
            ['type' => 'arms', 'order' => 4, 'z' => 3],
        ];

        foreach ($tribes as $id => $tribe) {
            foreach ($parts as $part) {
                // Check if exists to avoid duplicates
                $exists = DB::table('tribe_appearance_parts')
                    ->where('tribe_id', $id)
                    ->where('part_type', $part['type'])
                    ->exists();

                if (!$exists) {
                    DB::table('tribe_appearance_parts')->insert([
                        'tribe_id' => $id,
                        'part_type' => $part['type'],
                        'name' => $tribe['name'] . ' Default ' . ucfirst($part['type']),
                        // Using placehold.co for reliable generated images with transparent backgrounds
                        // We use different colors for tribes to distinguish them
                        'image_url' => "https://placehold.co/400x400/{$tribe['color']}/ffffff/png?text=" . urlencode("{$tribe['icon']}\n{$tribe['name']}\n{$part['type']}"),
                        'is_default' => true,
                        'is_active' => true,
                        'display_order' => $part['order'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
