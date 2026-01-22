<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KingdomBuilding extends Model
{
    use HasFactory;

    protected $fillable = [
        'kingdom_id',
        'building_id',
        'quantity',
        'level',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'level' => 'integer',
    ];

    public function kingdom()
    {
        return $this->belongsTo(Kingdom::class);
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Get total production from this building
     */
    public function getTotalGoldProductionAttribute()
    {
        return $this->building->gold_production * $this->quantity * $this->level;
    }

    public function getTotalTroopProductionAttribute()
    {
        return $this->building->troop_production * $this->quantity * $this->level;
    }

    public function getTotalDefenseBonusAttribute()
    {
        return $this->building->defense_bonus * $this->quantity * $this->level;
    }
}
