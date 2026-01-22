<?php

namespace App\Http\Controllers;

use App\Models\Battle;
use App\Models\Kingdom;
use App\Models\GameConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BattleController extends Controller
{
    /**
     * Show available kingdoms to attack
     */
    public function showBattle()
    {
        $userKingdom = Auth::user()->kingdom;
        $userKingdom->updateResources(); // Update resources first
        
        // Get kingdoms that can be attacked (has barracks AND mine)
        $targetKingdoms = Kingdom::with(['user', 'tribe', 'kingdomBuildings.building'])
            ->where('id', '!=', $userKingdom->id)
            ->get()
            ->filter(function($kingdom) {
                return $kingdom->canBeAttacked();
            });

        // Get battle history
        $battleHistory = Battle::where('attacker_id', $userKingdom->id)
            ->orWhere('defender_id', $userKingdom->id)
            ->with(['attacker', 'defender'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('game.battle', compact('targetKingdoms', 'userKingdom', 'battleHistory'));
    }

    /**
     * Execute attack
     */
    public function attack(Request $request)
    {
        $request->validate([
            'defender_id' => 'required|exists:kingdoms,id'
        ]);

        $attacker = Auth::user()->kingdom;
        $attacker->updateResources(); // Update resources first
        
        $defender = Kingdom::with('troops')->findOrFail($request->defender_id);

        // Validation 1: Cannot attack yourself
        if ($attacker->id === $defender->id) {
            return redirect()->back()->with('error', 'You cannot attack yourself!');
        }

        // Validation 2: Attacker must have barracks AND mine to attack
        if (!$attacker->canBeAttacked()) {
            return redirect()->back()->with('error', 'You need at least 1 Barracks and 1 Gold Mine to attack other kingdoms!');
        }

        // Validation 3: Defender must be attackable
        if (!$defender->canBeAttacked()) {
            return redirect()->back()->with('error', 'This kingdom cannot be attacked yet (missing barracks or mine).');
        }

        // Validation 4: Must have troops
        if ($attacker->total_troops <= 0) {
            return redirect()->back()->with('error', 'You have no troops to attack!');
        }

        DB::transaction(function() use ($attacker, $defender, &$result) {
            $attackPower = $attacker->calculateTotalAttackPower();
            $defensePower = $defender->calculateTotalDefensePower();

            $goldStealPercentage = GameConfig::getAttackGoldStealPercentage();

            // Determine battle outcome
            if ($attackPower > $defensePower) {
                // ATTACKER WINS
                $goldStolen = (int) ($defender->gold * ($goldStealPercentage / 100));
                
                // Transfer gold
                $defender->decrement('gold', $goldStolen);
                $attacker->increment('gold', $goldStolen);

                // Calculate defender troop losses
                $powerDifference = $attackPower - $defensePower;
                $defenderTroops = $defender->troops;
                
                if ($defenderTroops) {
                    $troopsKilled = (int) min($defenderTroops->quantity, ceil($powerDifference / 10));
                    $defenderTroops->decrement('quantity', $troopsKilled);
                    $defender->update(['total_troops' => $defenderTroops->quantity]);
                }

                $result = [
                    'success' => true,
                    'message' => "Victory! You stole {$goldStolen} gold from {$defender->name}!",
                    'gold_stolen' => $goldStolen,
                    'attacker_troops_lost' => 0,
                    'defender_troops_lost' => $troopsKilled ?? 0,
                ];
            } else {
                // DEFENDER WINS - All attacker troops die
                $attackerTroops = $attacker->troops;
                $attackerTroopsKilled = $attackerTroops->quantity;
                
                $attackerTroops->update(['quantity' => 0]);
                $attacker->update(['total_troops' => 0]);

                // Calculate defender troop losses
                $powerDifference = $defensePower - $attackPower;
                $defenderTroops = $defender->troops;
                
                if ($defenderTroops && $defenderTroops->quantity > 0) {
                    // Calculate surviving troops
                    $troopDefenseValue = $powerDifference / $defenderTroops->quantity;
                    $survivingTroops = (int) max(0, ceil($troopDefenseValue));
                    $troopsLost = $defenderTroops->quantity - $survivingTroops;
                    
                    $defenderTroops->update(['quantity' => $survivingTroops]);
                    $defender->update(['total_troops' => $survivingTroops]);
                } else {
                    $troopsLost = 0;
                }

                $result = [
                    'success' => false,
                    'message' => "Defeat! All your troops ({$attackerTroopsKilled}) were killed!",
                    'gold_stolen' => 0,
                    'attacker_troops_lost' => $attackerTroopsKilled,
                    'defender_troops_lost' => $troopsLost,
                ];
            }

            // Record battle
            Battle::create([
                'attacker_id' => $attacker->id,
                'defender_id' => $defender->id,
                'attacker_troops' => $attacker->total_troops,
                'defender_troops' => $defender->total_troops,
                'gold_stolen' => $result['gold_stolen'],
                'winner_id' => $result['success'] ? $attacker->id : $defender->id,
            ]);

            // Update power calculations
            $attacker->updatePower();
            $defender->updatePower();
        });

        return redirect()->back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    /**
     * Show training mode (practice battles)
     */
    public function showTraining()
    {
        $kingdom = Auth::user()->kingdom;
        $kingdom->updateResources();
        
        return view('game.training', compact('kingdom'));
    }

    /**
     * Training attack (practice mode)
     */
    public function trainingAttack(Request $request)
    {
        $kingdom = Auth::user()->kingdom;
        
        $attackPower = $kingdom->calculateTotalAttackPower();
        $defensePower = rand(50, 200); // Random AI defense

        $message = $attackPower > $defensePower
            ? "Training Success! Your attack power ({$attackPower}) defeated the AI ({$defensePower})"
            : "Training Failed! The AI defense ({$defensePower}) was stronger than your attack ({$attackPower})";

        return redirect()->back()->with('info', $message);
    }
}
