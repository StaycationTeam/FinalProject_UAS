<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Kingdom extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'tribe_id', 'name', 'gold', 'main_building_level',
        'barracks_count', 'mines_count', 'walls_count', 'total_troops',
        'total_attack_power', 'total_defense_power', 'last_resource_update'
    ];

    protected $casts = [
        'last_resource_update' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tribe()
    {
        return $this->belongsTo(Tribe::class);
    }

    public function troops()
    {
        return $this->hasOne(Troop::class);
    }

    public function attacks()
    {
        return $this->hasMany(Battle::class, 'attacker_id');
    }

    public function defenses()
    {
        return $this->hasMany(Battle::class, 'defender_id');
    }

    public function kingdomBuildings()
    {
        return $this->hasMany(KingdomBuilding::class);
    }

    public function buildings()
    {
        return $this->belongsToMany(Building::class, 'kingdom_buildings')
                    ->withPivot('quantity', 'level')
                    ->withTimestamps();
    }

    /**
     * Get building by type
     */
    public function getBuilding($type)
    {
        return $this->kingdomBuildings()
            ->whereHas('building', function($q) use ($type) {
                $q->where('type', $type);
            })
            ->first();
    }

    /**
     * Check if kingdom has building
     */
    public function hasBuilding($type)
    {
        return $this->getBuilding($type) !== null;
    }

    /**
     * Get total gold production per minute
     */
    public function getTotalGoldProductionPerMinute()
    {
        $baseProduction = 5; // Default 5 gold per minute
        $mineProduction = $this->mines_count * 10; // Each mine = +10 gold/min
        
        return $baseProduction + $mineProduction;
    }

    /**
     * Get total troop production per minute
     */
    public function getTotalTroopProductionPerMinute()
    {
        $baseProduction = $this->tribe->troop_production_rate ?? 3;
        $barracksProduction = $this->barracks_count * 5; // Each barracks = +5 troops/min
        
        return $baseProduction + $barracksProduction;
    }

    /**
     * Get total defense bonus from buildings
     */
    public function getTotalDefenseBonus()
    {
        return $this->walls_count * 10; // Each wall = +10 defense
    }

    /**
     * Update resources based on time elapsed since last update
     */
    public function updateResources()
    {
        $now = Carbon::now();
        $lastUpdate = $this->last_resource_update ?? $this->created_at;
        
        // Calculate minutes elapsed
        $minutesElapsed = $now->diffInMinutes($lastUpdate);
        
        if ($minutesElapsed < 1) {
            return; // No update needed if less than 1 minute
        }
        
        // Calculate production
        $goldProduced = $this->getTotalGoldProductionPerMinute() * $minutesElapsed;
        $troopsProduced = $this->getTotalTroopProductionPerMinute() * $minutesElapsed;
        
        // Update kingdom
        $this->gold += $goldProduced;
        $this->total_troops += $troopsProduced;
        $this->last_resource_update = $now;
        $this->save();
        
        // Also update troops model if exists
        if ($this->troops) {
            $this->troops->quantity += $troopsProduced;
            $this->troops->save();
        }
    }

    /**
     * Check if kingdom can be attacked
     * RULE: Kingdom must have at least 1 barracks AND 1 gold mine
     */
    public function canBeAttacked()
    {
        // Use legacy columns for reliable check
        // Kingdom MUST have both barracks_count >= 1 AND mines_count >= 1
        return ($this->barracks_count >= 1) && ($this->mines_count >= 1);
    }

    public function calculateTotalAttackPower()
    {
        $tribe = $this->tribe;
        $troops = $this->troops;
        
        if (!$troops) return 0;
        
        $melee_attack = ($tribe->melee_attack * $troops->quantity) / 100;
        $range_attack = ($tribe->range_attack * $troops->quantity) / 100;
        $magic_attack = ($tribe->magic_attack * $troops->quantity) / 100;
        
        return $melee_attack + $range_attack + $magic_attack;
    }

    public function calculateTotalDefensePower()
    {
        $tribe = $this->tribe;
        $troops = $this->troops;
        
        if (!$troops) return 0;
        
        $building_defense = $this->getTotalDefenseBonus();
        
        $melee_defense = ($tribe->melee_defense * $troops->quantity) / 100;
        $range_defense = ($tribe->range_defense * $troops->quantity) / 100;
        $magic_defense = ($tribe->magic_defense * $troops->quantity) / 100;
        
        return $melee_defense + $range_defense + $magic_defense + $building_defense;
    }

    public function updatePower()
    {
        $this->total_attack_power = $this->calculateTotalAttackPower();
        $this->total_defense_power = $this->calculateTotalDefensePower();
        $this->save();
    }
}
