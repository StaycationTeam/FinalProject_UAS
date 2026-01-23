<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SplitAvatarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define tribes with separate parts (Head, Body, Legs)
        // Using Icons8 for high-quality, stable, realistic-style icons
        $tribes = [
            1 => [ // Marksman (Green/Nature)
                'head' => 'https://img.icons8.com/color/96/robin-hood.png',
                'body' => 'https://img.icons8.com/color/96/suit.png', // Generic vest look
                'legs' => 'https://img.icons8.com/color/96/boots.png',
            ],
            2 => [ // Tank (Heavy Armor)
                'head' => 'https://img.icons8.com/color/96/knight-helmet.png',
                'body' => 'https://img.icons8.com/color/96/armor.png',
                'legs' => 'https://img.icons8.com/color/96/armored-boots.png',
            ],
            3 => [ // Mage (Mystic)
                'head' => 'https://img.icons8.com/color/96/wizard.png', // Includes hat/face
                'body' => 'https://img.icons8.com/color/96/cloak.png',
                'legs' => 'https://img.icons8.com/color/96/shoes.png',
            ],
            4 => [ // Warrior (Gladiator/Fighter)
                'head' => 'https://img.icons8.com/color/96/gladiator.png',
                'body' => 'https://img.icons8.com/color/96/roman-armor.png',
                'legs' => 'https://img.icons8.com/color/96/sandals.png',
            ],
        ];

        foreach ($tribes as $id => $parts) {
            // 1. Clear existing parts
            DB::table('tribe_appearance_parts')
                ->where('tribe_id', $id)
                ->delete();

            // 2. Insert Head (Order 1)
            DB::table('tribe_appearance_parts')->insert([
                'tribe_id' => $id,
                'part_type' => 'head',
                'name' => 'Default Head',
                'image_url' => $parts['head'],
                'is_default' => true,
                'is_active' => true,
                'display_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. Insert Body (Order 2)
            DB::table('tribe_appearance_parts')->insert([
                'tribe_id' => $id,
                'part_type' => 'body',
                'name' => 'Default Body',
                'image_url' => $parts['body'],
                'is_default' => true,
                'is_active' => true,
                'display_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. Insert Legs (Order 3)
            DB::table('tribe_appearance_parts')->insert([
                'tribe_id' => $id,
                'part_type' => 'legs',
                'name' => 'Default Legs',
                'image_url' => $parts['legs'],
                'is_default' => true,
                'is_active' => true,
                'display_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
