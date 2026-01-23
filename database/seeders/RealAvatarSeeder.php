<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RealAvatarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define tribes with specific DiceBear seeds to ensure consistent, cool looks
        // We use the 'adventurer' style which is perfect for RPGs
        $tribes = [
            1 => [ // Marksman
                'name' => 'Marksman',
                'url' => 'https://api.dicebear.com/9.x/adventurer/png?seed=Felix&backgroundColor=d1d4f9&accessories=glasses', 
            ],
            2 => [ // Tank
                'name' => 'Tank',
                'url' => 'https://api.dicebear.com/9.x/adventurer/png?seed=Mason&backgroundColor=ffdfbf&features=beard',
            ],
            3 => [ // Mage
                'name' => 'Mage',
                'url' => 'https://api.dicebear.com/9.x/adventurer/png?seed=Celeste&backgroundColor=c0aede&eyebrows=variant02',
            ],
            4 => [ // Warrior
                'name' => 'Warrior',
                'url' => 'https://api.dicebear.com/9.x/adventurer/png?seed=Alex&backgroundColor=b6e3f4&hair=variant02',
            ],
        ];

        // We only need to set the BODY part with the full avatar URL
        // The other parts (Head, Legs, Arms) will be set to inactive or deleted to avoid overlapping
        
        foreach ($tribes as $id => $tribe) {
            // 1. Clear existing default parts for this tribe to avoid mess
            DB::table('tribe_appearance_parts')
                ->where('tribe_id', $id)
                ->where('is_default', true)
                ->delete();

            // 2. Insert the new Full Body Avatar
            DB::table('tribe_appearance_parts')->insert([
                'tribe_id' => $id,
                'part_type' => 'body', // We use 'body' as the main container
                'name' => $tribe['name'] . ' Full Avatar',
                'image_url' => $tribe['url'],
                'is_default' => true,
                'is_active' => true,
                'display_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Optional: Insert empty/transparent placeholders for other parts if logic requires them
            // But our frontend logic handles empty parts gracefully, so we just need the body.
        }
    }
}
