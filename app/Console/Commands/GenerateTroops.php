<?php

namespace App\Console\Commands;

use App\Models\Kingdom;
use Illuminate\Console\Command;

class GenerateTroops extends Command
{
    protected $signature = 'game:generate-troops';
    protected $description = 'Generate troops for all kingdoms based on their barracks production';

    public function handle()
    {
        $kingdoms = Kingdom::with(['kingdomBuildings.building', 'troops'])->get();
        $count = 0;

        foreach ($kingdoms as $kingdom) {
            $troopProduction = $kingdom->getTotalTroopProductionPerMinute();
            
            if ($troopProduction > 0 && $kingdom->troops) {
                $kingdom->troops->increment('quantity', $troopProduction);
                $kingdom->troops->update(['last_production_update' => now()]);
                $kingdom->increment('total_troops', $troopProduction);
                $kingdom->updatePower();
                $count++;
            }
        }

        $this->info("Generated troops for {$count} kingdoms.");
        \Log::info("Troop generation completed for {$count} kingdoms at " . now());
        
        return Command::SUCCESS;
    }
}
