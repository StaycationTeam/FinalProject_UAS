<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Battle extends Model
{
    use HasFactory;

    protected $fillable = [
        'attacker_id',
        'defender_id',
        'attacker_troops',
        'defender_troops',
        'attacker_power',
        'defender_power',
        'gold_stolen',
        'result',
        'battle_log'
    ];

    public function attacker()
    {
        return $this->belongsTo(Kingdom::class, 'attacker_id');
    }

    public function defender()
    {
        return $this->belongsTo(Kingdom::class, 'defender_id');
    }
}
