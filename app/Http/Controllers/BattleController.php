<?php

namespace App\Http\Controllers;

use App\Models\Kingdom;
use App\Models\Battle;
use App\Models\Tribe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BattleController extends Controller
{
    /**
     * Show battle page with valid targets
     * Only kingdoms with barracks AND mine can be attacked
     */
    public function showBattle()
    {
        $user = Auth::user();
        $userKingdom = $user->kingdom;

        $battleHistory = Battle::where('attacker_id', $userKingdom->id)
            ->where('type', 'pvp')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Filter: Only attackable kingdoms (has barracks AND mine)
        $targetKingdoms = Kingdom::with('user', 'tribe')
            ->where('id', '!=', $userKingdom->id)
            ->where('total_troops', '>', 0)
            ->where(function($query) {
                $query->where(function($q) {
                    // New system: check kingdom_buildings
                    $q->whereHas('kingdomBuildings', function($kb) {
                        $kb->whereHas('building', function($b) {
                            $b->where('type', 'barracks');
                        });
                    })->whereHas('kingdomBuildings', function($kb) {
                        $kb->whereHas('building', function($b) {
                            $b->where('type', 'mine');
                        });
                    });
                })->orWhere(function($q) {
                    // Legacy system: check old columns
                    $q->where('barracks_count', '>', 0)
                      ->where('mines_count', '>', 0);
                });
            })
            ->get();

        if ($targetKingdoms->count() === 0) {
            $targetKingdoms = $this->generateAiTargets(5, $userKingdom);
        }

        return view('game.battle', compact(
            'userKingdom',
            'targetKingdoms',
            'battleHistory'
        ));
    }

    /**
     * AI Target Generator for training
     */
    protected function generateAiTargets(int $count = 5, Kingdom $userKingdom = null)
    {
        $tribes = Tribe::inRandomOrder()->limit($count)->get();
        $aiTargets = collect();

        for ($i = 0; $i < $count; $i++) {
            $tribe = $tribes[$i % $tribes->count()];
            $userTroops = $userKingdom ? $userKingdom->total_troops : 20;

            $baseTroops = max(10, (int) round($userTroops * (0.6 + rand(0, 40) / 100)));
            $baseDefense = (
                $tribe->melee_defense +
                $tribe->range_defense +
                $tribe->magic_defense
            ) * ($baseTroops / 100);

            $ai = new \stdClass();
            $ai->id = 100000 + $i;
            $ai->name = ucfirst($tribe->name) . ' Training Camp ' . ($i + 1);
            $ai->total_troops = $baseTroops;
            $ai->total_defense_power = (int) $baseDefense + rand(0, 30);
            $ai->gold = rand(100, 500);
            $ai->tribe = $tribe;

            $ai->user = (object)[
                'username' => 'NPC_' . Str::upper(Str::random(3))
            ];

            $aiTargets->push($ai);
        }

        return $aiTargets;
    }

    /**
     * Training View
     */
    public function showTraining()
    {
        $userKingdom = Auth::user()->kingdom;

        $aiTargets = $this->generateAiTargets(3, $userKingdom);
        session(['training_ai' => $aiTargets]);

        $trainingHistory = Battle::where('attacker_id', $userKingdom->id)
            ->where('type', 'training')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('game.training', compact(
            'userKingdom',
            'aiTargets',
            'trainingHistory'
        ));
    }

    /**
     * Training Attack (AI only)
     */
    public function trainingAttack(Request $request)
    {
        $request->validate([
            'defender_id' => 'required'
        ]);

        $attacker = Auth::user()->kingdom;

        $aiTargets = session('training_ai', collect());
        $defender = $aiTargets->firstWhere('id', $request->defender_id);

        if (!$defender) {
            return back()->with('error', 'Training target expired. Refresh page.');
        }

        return $this->resolveBattle(
            $attacker,
            null,
            $defender->name,
            $defender->total_troops,
            $defender->total_defense_power,
            $defender->gold ?? 0,
            'training'
        );
    }

    /**
     * PVP Attack
     */
    public function attack(Request $request)
    {
        $request->validate([
            'defender_id' => 'required'
        ]);

        $attacker = Auth::user()->kingdom;
        $defenderId = $request->defender_id;

        // AI Target
        if ($defenderId >= 100000) {
            $aiTargets = $this->generateAiTargets(10, $attacker);
            $defender = $aiTargets->firstWhere('id', $defenderId);

            if (!$defender) {
                return back()->with('error', 'Target not found.');
            }

            return $this->resolveBattle(
                $attacker,
                null,
                $defender->name,
                $defender->total_troops,
                $defender->total_defense_power,
                $defender->gold ?? 0,
                'pvp'
            );
        }

        // Real Player Target
        $defender = Kingdom::find($defenderId);

        if (!$defender) {
            return back()->with('error', 'Target not found.');
        }

        // Check if target can be attacked
        if (!$defender->canBeAttacked()) {
            return back()->with('error', 'This kingdom cannot be attacked yet. They need barracks and mine.');
        }

        return $this->resolveBattle(
            $attacker,
            $defender,
            $defender->name,
            $defender->total_troops,
            $defender->total_defense_power,
            $defender->gold,
            'pvp'
        );
    }

    /**
     * Battle Resolution Engine - According to Requirements
     * 
     * Rules:
     * - If attack > defense: steal 90% of defender's gold
     * - If attack fails: all attacker troops die
     * - Defender troops: (defense_power - attack_power) / troops_quantity = survivors
     */
    protected function resolveBattle(
        Kingdom $attacker,
        $defenderModel,
        string $defenderName,
        int $defTroops,
        int $defPower,
        int $defGold,
        string $type
    ) {
        $attPower = $attacker->total_attack_power;
        $isWin = $attPower > $defPower;
        $isTraining = $type === 'training';

        $goldStolen = 0;
        $attackerTroopsLost = 0;
        $defenderTroopsLost = 0;
        $battleLog = '';

        DB::transaction(function() use (
            &$goldStolen, &$attackerTroopsLost, &$defenderTroopsLost, &$battleLog,
            $attacker, $defenderModel, $defenderName, $defTroops, $defPower, $defGold,
            $attPower, $isWin, $isTraining
        ) {
            if ($isWin) {
                // ATTACKER WINS
                if (!$isTraining && $defenderModel) {
                    // Steal 90% gold
                    $goldStolen = (int) floor($defGold * 0.9);
                    $attacker->increment('gold', $goldStolen);
                    $defenderModel->decrement('gold', $goldStolen);
                    
                    // All defender troops die
                    $defenderTroopsLost = $defTroops;
                    if ($defenderModel->troops) {
                        $defenderModel->troops->update(['quantity' => 0]);
                    }
                    $defenderModel->update(['total_troops' => 0]);
                    $defenderModel->updatePower();
                }
                
                $battleLog = "Victory! You defeated {$defenderName}" . 
                    ($goldStolen > 0 ? " and stole {$goldStolen} gold (90% of their treasury)!" : "!");
            } else {
                // ATTACKER LOSES
                // All attacker troops die
                $attackerTroopsLost = $attacker->total_troops;
                if ($attacker->troops) {
                    $attacker->troops->update(['quantity' => 0]);
                }
                $attacker->update(['total_troops' => 0]);
                $attacker->updatePower();

                // Defender troops calculation: (defense_points - attack_points) / troops = survivors
                if (!$isTraining && $defenderModel && $defTroops > 0) {
                    $pointDifference = $defPower - $attPower;
                    $survivalRatio = $pointDifference / $defTroops;
                    $survivingTroops = max(0, (int) floor($defTroops * ($survivalRatio / 100)));
                    $defenderTroopsLost = $defTroops - $survivingTroops;
                    
                    if ($defenderModel->troops) {
                        $defenderModel->troops->update(['quantity' => $survivingTroops]);
                    }
                    $defenderModel->update(['total_troops' => $survivingTroops]);
                    $defenderModel->updatePower();
                }

                $battleLog = "Defeat! {$defenderName} repelled your attack. All your troops were lost!";
            }
        });

        // Record battle
        Battle::create([
            'attacker_id' => $attacker->id,
            'defender_id' => $defenderModel->id ?? null,
            'attacker_troops' => $attacker->total_troops,
            'defender_troops' => $defTroops,
            'attacker_power' => $attPower,
            'defender_power' => $defPower,
            'gold_stolen' => $goldStolen,
            'result' => $isWin ? 'win' : 'lose',
            'battle_log' => $battleLog,
            'type' => $type,
        ]);

        return redirect()->back()->with('battle_result', [
            'result' => $isWin ? 'win' : 'lose',
            'gold_stolen' => $goldStolen,
            'troops_lost' => $attackerTroopsLost,
            'log' => $battleLog
        ]);
    }
}
