<?php

namespace App\Http\Controllers;

use App\Models\Kingdom;
use App\Models\Battle;
use App\Models\Tribe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BattleController extends Controller
{
    /**
     * =============================
     * PVP / BATTLE NORMAL
     * =============================
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

        $targetKingdoms = Kingdom::with('user', 'tribe')
            ->where('id', '!=', $userKingdom->id)
            ->where('total_troops', '>', 0)
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
     * =============================
     * AI GENERATOR
     * =============================
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
            $ai->tribe = $tribe;

            $ai->user = (object)[
                'username' => 'NPC_' . Str::upper(Str::random(3))
            ];

            $aiTargets->push($ai);
        }

        return $aiTargets;
    }

    /**
     * =============================
     * TRAINING VIEW
     * =============================
     */
    public function showTraining()
    {
        $userKingdom = Auth::user()->kingdom;

        // Generate AI (dan simpan ke session)
        $aiTargets = $this->generateAiTargets(3, $userKingdom);
        session(['training_ai' => $aiTargets]);

        // Training history
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
     * =============================
     * ATTACK TRAINING (AI ONLY)
     * =============================
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
            'training'
        );
    }

    /**
     * =============================
     * ATTACK (PVP / AI FALLBACK)
     * =============================
     */
    public function attack(Request $request)
    {
        $request->validate([
            'defender_id' => 'required'
        ]);

        $attacker = Auth::user()->kingdom;
        $defenderId = $request->defender_id;

        if ($defenderId >= 100000) {
            $aiTargets = $this->generateAiTargets(10, $attacker);
            $defender = $aiTargets->firstWhere('id', $defenderId);

            if (!$defender) {
                return back()->with('error', 'Target tidak ditemukan.');
            }

            return $this->resolveBattle(
                $attacker,
                null,
                $defender->name,
                $defender->total_troops,
                $defender->total_defense_power,
                'pvp'
            );
        }

        $defender = Kingdom::find($defenderId);

        if (!$defender) {
            return back()->with('error', 'Target tidak ditemukan.');
        }

        return $this->resolveBattle(
            $attacker,
            $defender,
            $defender->name,
            $defender->total_troops,
            $defender->total_defense_power,
            'pvp'
        );
    }

    /**
     * =============================
     * CORE BATTLE ENGINE
     * =============================
     */
    protected function resolveBattle(
        Kingdom $attacker,
        $defenderModel,
        string $defenderName,
        int $defTroops,
        int $defPower,
        string $type
    ) {
        $attPower = $attacker->total_attack_power + rand(-10, 10);
        $defPower = $defPower + rand(-10, 10);

        $isWin = $attPower >= $defPower;
        $isTraining = $type === 'training';

        $goldStolen = 0;

        if ($isWin && !$isTraining) {
            $goldStolen = rand(10, 100);
            $attacker->gold += $goldStolen;
            $attacker->save();
        }

        Battle::create([
            'attacker_id' => $attacker->id,
            'defender_id' => $defenderModel->id ?? null,
            'attacker_troops' => $attacker->total_troops,
            'defender_troops' => $defTroops,
            'attacker_power' => $attPower,
            'defender_power' => $defPower,
            'gold_stolen' => $goldStolen,
            'result' => $isWin ? 'win' : 'lose',
            'battle_log' => $isTraining
                ? "[TRAINING] Battle vs {$defenderName}"
                : ($isWin
                    ? "You attacked {$defenderName} and looted {$goldStolen} gold."
                    : "Your attack on {$defenderName} failed."),
            'type' => $type,
        ]);

        return redirect()->back()->with('battle_result', [
            'result' => $isWin ? 'win' : 'lose',
            'gold_stolen' => $goldStolen,
            'log' => $isTraining
                ? "Training battle against {$defenderName}"
                : ($isWin
                    ? "You looted {$goldStolen} gold."
                    : "Attack failed.")
        ]);
    }

}
