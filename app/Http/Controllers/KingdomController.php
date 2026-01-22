<?php

namespace App\Http\Controllers;

use App\Models\Kingdom;
use App\Models\Building;
use App\Models\KingdomBuilding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KingdomController extends Controller
{
    public function showBuildings()
    {
        $kingdom = Auth::user()->kingdom;
        $buildings = Building::where('is_active', true)->get();
        $ownedBuildings = $kingdom->kingdomBuildings()->with('building')->get();
        
        return view('game.buildings', compact('kingdom', 'buildings', 'ownedBuildings'));
    }

    /**
     * Purchase a building
     */
    public function purchaseBuilding(Request $request)
    {
        $request->validate([
            'building_id' => 'required|exists:buildings,id'
        ]);

        $kingdom = Auth::user()->kingdom;
        $building = Building::findOrFail($request->building_id);

        // Check if building is active
        if (!$building->is_active) {
            return redirect()->back()->with('error', 'This building is not available.');
        }

        // Check gold
        if ($kingdom->gold < $building->gold_cost) {
            return redirect()->back()->with('error', 'Not enough gold! Need ' . $building->gold_cost . ' gold.');
        }

        DB::transaction(function() use ($kingdom, $building) {
            // Deduct gold
            $kingdom->decrement('gold', $building->gold_cost);

            // Check if kingdom already has this building
            $kingdomBuilding = KingdomBuilding::where('kingdom_id', $kingdom->id)
                ->where('building_id', $building->id)
                ->first();

            if ($kingdomBuilding) {
                // Increment quantity
                $kingdomBuilding->increment('quantity');
            } else {
                // Create new entry
                KingdomBuilding::create([
                    'kingdom_id' => $kingdom->id,
                    'building_id' => $building->id,
                    'quantity' => 1,
                    'level' => 1,
                ]);
            }

            // Update legacy counts for backward compatibility
            if ($building->type === 'barracks') {
                $kingdom->increment('barracks_count');
            } elseif ($building->type === 'mine') {
                $kingdom->increment('mines_count');
            } elseif ($building->type === 'walls') {
                $kingdom->increment('walls_count');
            }

            $kingdom->updatePower();
        });

        return redirect()->back()->with('success', $building->name . ' purchased successfully!');
    }

    /**
     * Upgrade a building
     */
    public function upgradeBuilding(Request $request)
    {
        $request->validate([
            'kingdom_building_id' => 'required|exists:kingdom_buildings,id'
        ]);

        $kingdom = Auth::user()->kingdom;
        $kingdomBuilding = KingdomBuilding::with('building')
            ->where('kingdom_id', $kingdom->id)
            ->findOrFail($request->kingdom_building_id);

        $upgradeCost = $kingdomBuilding->building->gold_cost * $kingdomBuilding->level;

        if ($kingdom->gold < $upgradeCost) {
            return redirect()->back()->with('error', 'Not enough gold! Need ' . $upgradeCost . ' gold.');
        }

        DB::transaction(function() use ($kingdom, $kingdomBuilding, $upgradeCost) {
            $kingdom->decrement('gold', $upgradeCost);
            $kingdomBuilding->increment('level');
            
            if ($kingdomBuilding->building->type === 'main') {
                $kingdom->increment('main_building_level');
            }
            
            $kingdom->updatePower();
        });

        return redirect()->back()->with('success', $kingdomBuilding->building->name . ' upgraded to level ' . $kingdomBuilding->level . '!');
    }

    // Legacy methods for backward compatibility
    public function buildBarracks(Request $request)
    {
        $building = Building::where('type', 'barracks')->where('is_active', true)->first();
        if (!$building) {
            return redirect()->back()->with('error', 'Barracks not available.');
        }
        
        return $this->purchaseBuilding(new Request(['building_id' => $building->id]));
    }

    public function buildMine(Request $request)
    {
        $building = Building::where('type', 'mine')->where('is_active', true)->first();
        if (!$building) {
            return redirect()->back()->with('error', 'Mine not available.');
        }
        
        return $this->purchaseBuilding(new Request(['building_id' => $building->id]));
    }

    public function buildWalls(Request $request)
    {
        $building = Building::where('type', 'walls')->where('is_active', true)->first();
        if (!$building) {
            return redirect()->back()->with('error', 'Walls not available.');
        }
        
        return $this->purchaseBuilding(new Request(['building_id' => $building->id]));
    }

    public function upgradeMainBuilding(Request $request)
    {
        $kingdom = Auth::user()->kingdom;
        $kingdomBuilding = $kingdom->kingdomBuildings()
            ->whereHas('building', function($q) {
                $q->where('type', 'main');
            })
            ->first();

        if (!$kingdomBuilding) {
            return redirect()->back()->with('error', 'Main building not found.');
        }

        return $this->upgradeBuilding(new Request(['kingdom_building_id' => $kingdomBuilding->id]));
    }
}
