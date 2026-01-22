<?php

namespace App\Http\Controllers;

use App\Models\Kingdom;
use App\Models\Building;
use App\Services\BuildingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KingdomController extends Controller
{
    protected $buildingService;

    public function __construct(BuildingService $buildingService)
    {
        $this->buildingService = $buildingService;
    }

    public function showBuildings()
    {
        $kingdom = Auth::user()->kingdom;
        $buildings = Building::where('is_active', true)->get();
        
        return view('game.buildings', compact('kingdom', 'buildings'));
    }

    public function buildBarracks(Request $request)
    {
        $kingdom = Auth::user()->kingdom;
        
        $building = Building::where('type', 'barracks')->first();
        
        if ($kingdom->gold < $building->gold_cost) {
            return redirect()->back()->with('error', 'Not enough gold!');
        }

        $kingdom->updatePower();
        $this->buildingService->buildBarracks($kingdom, $building);
        
        return redirect()->back()->with('success', 'Barracks built successfully!');
    }

    public function buildMine(Request $request)
    {
        $kingdom = Auth::user()->kingdom;
        
        $building = Building::where('type', 'mine')->first();
        
        if ($kingdom->gold < $building->gold_cost) {
            return redirect()->back()->with('error', 'Not enough gold!');
        }

        $this->buildingService->buildMine($kingdom, $building);
        
        return redirect()->back()->with('success', 'Mine built successfully!');
    }

    public function buildWalls(Request $request)
    {
        $kingdom = Auth::user()->kingdom;
        
        $building = Building::where('type', 'walls')->first();
        
        if ($kingdom->gold < $building->gold_cost) {
            return redirect()->back()->with('error', 'Not enough gold!');
        }

        $kingdom->updatePower();
        $this->buildingService->buildWalls($kingdom, $building);
        
        return redirect()->back()->with('success', 'Walls built successfully!');
    }

    public function upgradeMainBuilding(Request $request)
    {
        $kingdom = Auth::user()->kingdom;
        
        $building = Building::where('type', 'main')->first();
        $upgradeCost = $building->gold_cost * $kingdom->main_building_level;
        
        if ($kingdom->gold < $upgradeCost) {
            return redirect()->back()->with('error', 'Not enough gold!');
        }

        $this->buildingService->upgradeMainBuilding($kingdom, $building);
        
        return redirect()->back()->with('success', 'Main building upgraded successfully!');
    }
}