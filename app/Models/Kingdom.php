<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function calculateTotalAttackPower()
    {
        $tribe = $this->tribe;
        $troops = $this->troops;
        
        $melee_attack = ($tribe->melee_attack * $troops->quantity) / 100;
        $range_attack = ($tribe->range_attack * $troops->quantity) / 100;
        $magic_attack = ($tribe->magic_attack * $troops->quantity) / 100;
        
        return $melee_attack + $range_attack + $magic_attack;
    }

    public function calculateTotalDefensePower()
    {
        $tribe = $this->tribe;
        $troops = $this->troops;
        $walls_bonus = $this->walls_count * 10;
        
        $melee_defense = ($tribe->melee_defense * $troops->quantity) / 100;
        $range_defense = ($tribe->range_defense * $troops->quantity) / 100;
        $magic_defense = ($tribe->magic_defense * $troops->quantity) / 100;
        
        return $melee_defense + $range_defense + $magic_defense + $walls_bonus;
    }

    public function updatePower()
    {
        $this->total_attack_power = $this->calculateTotalAttackPower();
        $this->total_defense_power = $this->calculateTotalDefensePower();
        $this->save();
    }

}