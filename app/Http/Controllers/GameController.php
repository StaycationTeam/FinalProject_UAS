<?php

namespace App\Http\Controllers;

use App\Models\Kingdom;
use App\Models\Battle;
use App\Models\Troop;
use App\Models\TribeAppearancePart;
use App\Services\ResourceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    protected $resourceService;

    public function __construct(ResourceService $resourceService)
    {
        $this->resourceService = $resourceService;
    }

    /**
     * Dashboard utama game
     */
    public function dashboard()
    {
        $user = Auth::user();
        $kingdom = $user->kingdom;

        // Update resource sebelum tampil
        $kingdom->updateResources();
        $this->resourceService->updateKingdomResources($kingdom);

        $recentBattles = Battle::where('attacker_id', $kingdom->id)
            ->orWhere('defender_id', $kingdom->id)
            ->latest()
            ->take(5)
            ->get();

        // Ambil avatar parts default untuk tribe ini
        $avatarParts = TribeAppearancePart::where('tribe_id', $kingdom->tribe_id)
            ->where('is_default', true)
            ->orderBy('display_order')
            ->get();

        return view('game.dashboard', compact('kingdom', 'recentBattles', 'avatarParts'));
    }

    /**
     * Ranking seluruh pemain
     */
    public function rankings()
    {
        $kingdoms = Kingdom::with('user', 'tribe')
            ->whereNotNull('user_id') // Exclude AI training bots
            ->orderBy('total_attack_power', 'desc')
            ->paginate(10);

        return view('game.rankings', compact('kingdoms'));
    }

    /**
     * Halaman Troops
     */
    public function troops()
    {
        $user = Auth::user();
        $kingdom = $user->kingdom;

        // Update resources
        $kingdom->updateResources();

        // Ambil semua troop milik kingdom
        $troops = Troop::where('kingdom_id', $kingdom->id)->get();

        // 🔥 AMBIL RIWAYAT BATTLE (INI YANG KURANG)
        $recentBattles = Battle::where('attacker_id', $kingdom->id)
            ->orWhere('defender_id', $kingdom->id)
            ->latest()
            ->take(5)
            ->get();

        return view('game.troops', compact(
            'kingdom',
            'troops',
            'recentBattles'
        ));
    }
}
