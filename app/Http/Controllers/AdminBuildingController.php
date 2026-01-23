<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Http\Request;

class AdminBuildingController extends Controller
{
    public function index()
    {
        $buildings = Building::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.buildings.index', compact('buildings'));
    }

    public function create()
    {
        return view('admin.buildings.create');
    }

    public function store(Request $request)
    {
        try {
            \Log::info('Store Building Request:', $request->all());
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'description' => 'required|string',
                'gold_cost' => 'required|integer|min:0',
                'level' => 'nullable|integer|min:1',
                'gold_production' => 'nullable|integer|min:0',
                'troop_production' => 'nullable|integer|min:0',
                'defense_bonus' => 'nullable|integer|min:0',
            ]);

            $validated['gold_production'] = $validated['gold_production'] ?? 0;
            $validated['troop_production'] = $validated['troop_production'] ?? 0;
            $validated['defense_bonus'] = $validated['defense_bonus'] ?? 0;
            $validated['is_active'] = $request->has('is_active') ? 1 : 0;

            \Log::info('Validated Data:', $validated);

            Building::create($validated);

            \Log::info('Building created successfully');

            return redirect()->route('admin.buildings.index')
                ->with('success', 'Building created successfully!');
                
        } catch (\Exception $e) {
            \Log::error('Building Creation Error: ' . $e->getMessage());
            \Log::error('Stack Trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit(Building $building)
    {
        return view('admin.buildings.edit', compact('building'));
    }

    public function update(Request $request, Building $building)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'required|string',
            'gold_cost' => 'required|integer|min:0',
            'level' => 'nullable|integer|min:1',
            'gold_production' => 'nullable|integer|min:0',
            'troop_production' => 'nullable|integer|min:0',
            'defense_bonus' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Set default values for nullable fields
        $validated['gold_production'] = $validated['gold_production'] ?? 0;
        $validated['troop_production'] = $validated['troop_production'] ?? 0;
        $validated['defense_bonus'] = $validated['defense_bonus'] ?? 0;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $building->update($validated);

        return redirect()->route('admin.buildings.index')
            ->with('success', 'Building updated successfully!');
    }

    public function destroy(Building $building)
    {
        $building->delete();

        return redirect()->route('admin.buildings.index')
            ->with('success', 'Building deleted successfully!');
    }

    public function toggleActive(Building $building)
    {
        $building->update(['is_active' => !$building->is_active]);

        return redirect()->route('admin.buildings.index')
            ->with('success', 'Building status updated!');
    }
}
