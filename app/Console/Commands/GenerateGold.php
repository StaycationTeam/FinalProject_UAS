<?php

namespace App\Console\Commands;

use App\Models\Kingdom;
use Illuminate\Console\Command;

class GenerateGold extends Command
{
    protected $signature = 'game:generate-gold';
    protected $description = 'Generate gold for all kingdoms based on their production rate';

    public function handle()
    {
        $kingdoms = Kingdom::all();
        $count = 0;

        foreach ($kingdoms as $kingdom) {
            $goldProduction = $kingdom->getTotalGoldProductionPerMinute();
            
            $kingdom->increment('gold', $goldProduction);
            $kingdom->update(['last_resource_update' => now()]);
            
            $count++;
        }

        $this->info("Generated gold for {$count} kingdoms.");
        \Log::info("Gold generation completed for {$count} kingdoms at " . now());
        
        return Command::SUCCESS;
    }
}
