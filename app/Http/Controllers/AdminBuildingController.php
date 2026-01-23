<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:main,barracks,mine,walls,other',
            'description' => 'required|string',
            'gold_cost' => 'required|integer|min:0',
            'level' => 'required|integer|min:1',
            'gold_production' => 'nullable|integer|min:0',
            'troop_production' => 'nullable|integer|min:0',
            'defense_bonus' => 'nullable|integer|min:0',
        ]);

        // Set default values for nullable fields
        $validated['gold_production'] = $validated['gold_production'] ?? 0;
        $validated['troop_production'] = $validated['troop_production'] ?? 0;
        $validated['defense_bonus'] = $validated['defense_bonus'] ?? 0;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        Building::create($validated);

        return redirect()->route('admin.buildings.index')
            ->with('success', 'Building created successfully!');
    }

    public function edit(Building $building)
    {
        return view('admin.buildings.edit', compact('building'));
    }

    public function update(Request $request, Building $building)
    {
        try {
            // Log incoming request for debugging
            Log::info('Building Update Request', [
                'building_id' => $building->id,
                'request_data' => $request->except(['_token', '_method'])
            ]);

            // Validate request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:main,barracks,mine,walls,other',
                'description' => 'required|string',
                'gold_cost' => 'required|integer|min:0',
                'level' => 'required|integer|min:1',
                'gold_production' => 'nullable|integer|min:0',
                'troop_production' => 'nullable|integer|min:0',
                'defense_bonus' => 'nullable|integer|min:0',
            ], [
                'name.required' => 'Building name is required',
                'type.required' => 'Building type is required',
                'type.in' => 'Invalid building type selected',
                'description.required' => 'Description is required',
                'gold_cost.required' => 'Gold cost is required',
                'gold_cost.min' => 'Gold cost must be at least 0',
                'level.required' => 'Level is required',
                'level.min' => 'Level must be at least 1',
            ]);

            // Set default values for nullable fields
            $validated['gold_production'] = $request->input('gold_production', 0);
            $validated['troop_production'] = $request->input('troop_production', 0);
            $validated['defense_bonus'] = $request->input('defense_bonus', 0);
            $validated['is_active'] = $request->has('is_active') ? 1 : 0;

            // Use database transaction for data integrity
            DB::beginTransaction();

            // Update building
            $building->update($validated);

            // Log successful update
            Log::info('Building Updated Successfully', [
                'building_id' => $building->id,
                'building_name' => $building->name,
                'updated_data' => $validated
            ]);

            DB::commit();

            return redirect()->route('admin.buildings.index')
                ->with('success', 'Building "' . $building->name . '" updated successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation failed - return with errors
            Log::warning('Building Update Validation Failed', [
                'building_id' => $building->id,
                'errors' => $e->errors()
            ]);
            
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            
            // Log error
            Log::error('Building Update Failed', [
                'building_id' => $building->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update building: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Building $building)
    {
        try {
            $buildingName = $building->name;
            $building->delete();

            Log::info('Building Deleted', [
                'building_name' => $buildingName
            ]);

            return redirect()->route('admin.buildings.index')
                ->with('success', 'Building "' . $buildingName . '" deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Building Deletion Failed', [
                'building_id' => $building->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete building: ' . $e->getMessage());
        }
    }

    public function toggleActive(Building $building)
    {
        try {
            $building->update(['is_active' => !$building->is_active]);

            $status = $building->is_active ? 'activated' : 'deactivated';
            
            Log::info('Building Status Toggled', [
                'building_id' => $building->id,
                'building_name' => $building->name,
                'new_status' => $status
            ]);

            return redirect()->route('admin.buildings.index')
                ->with('success', 'Building "' . $building->name . '" ' . $status . ' successfully!');

        } catch (\Exception $e) {
            Log::error('Building Toggle Failed', [
                'building_id' => $building->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update building status: ' . $e->getMessage());
        }
    }
}
