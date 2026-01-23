<?php

namespace App\Http\Controllers;

use App\Models\TribeAppearancePart;
use App\Models\Tribe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TribeAppearanceController extends Controller
{
    public function index(Request $request)
    {
        $query = TribeAppearancePart::with('tribe');

        if ($request->filled('tribe_id')) {
            $query->where('tribe_id', $request->tribe_id);
        }

        if ($request->filled('part_type')) {
            $query->where('part_type', $request->part_type);
        }

        $parts = $query->orderBy('tribe_id')->orderBy('part_type')->orderBy('display_order')->paginate(20);
        $tribes = Tribe::all();
        $partTypes = ['head', 'body', 'legs', 'arms'];

        return view('admin.appearance.index', compact('parts', 'tribes', 'partTypes'));
    }

    public function create()
    {
        $tribes = Tribe::all();
        $partTypes = ['head', 'body', 'legs', 'arms'];
        
        return view('admin.appearance.create', compact('tribes', 'partTypes'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tribe_id' => 'required|exists:tribes,id',
                'part_type' => 'required|in:head,body,legs,arms',
                'name' => 'required|string|max:255',
                'image' => 'nullable|required_without:image_url_text|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'image_url_text' => 'nullable|required_without:image|url',
                'display_order' => 'nullable|integer|min:0',
                'description' => 'nullable|string',
            ]);

            // Handle image logic
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('appearance_parts', $filename, 'public');
                $validated['image_url'] = $path;
            } else {
                // Use the text URL
                $validated['image_url'] = $request->image_url_text;
            }

            if ($request->has('is_default') && $request->is_default) {
                TribeAppearancePart::where('tribe_id', $validated['tribe_id'])
                    ->where('part_type', $validated['part_type'])
                    ->update(['is_default' => false]);
            }

            $validated['is_default'] = $request->has('is_default') ? 1 : 0;
            $validated['is_active'] = $request->has('is_active') ? 1 : 0;
            $validated['display_order'] = $validated['display_order'] ?? 0;

            TribeAppearancePart::create($validated);

            Log::info('Appearance Part Created', ['name' => $validated['name']]);

            return redirect()->route('admin.appearance.index')
                ->with('success', 'Appearance part created successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create appearance part: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(TribeAppearancePart $appearance)
    {
        $tribes = Tribe::all();
        $partTypes = ['head', 'body', 'legs', 'arms'];
        
        return view('admin.appearance.edit', compact('appearance', 'tribes', 'partTypes'));
    }

    public function update(Request $request, TribeAppearancePart $appearance)
    {
        try {
            $validated = $request->validate([
                'tribe_id' => 'required|exists:tribes,id',
                'part_type' => 'required|in:head,body,legs,arms',
                'name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'image_url_text' => 'nullable|url',
                'display_order' => 'nullable|integer|min:0',
                'description' => 'nullable|string',
            ]);

            // Handle image update
            if ($request->hasFile('image')) {
                // Delete old if it was a local file
                if ($appearance->image_url && !filter_var($appearance->image_url, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($appearance->image_url)) {
                    Storage::disk('public')->delete($appearance->image_url);
                }
                $image = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('appearance_parts', $filename, 'public');
                $validated['image_url'] = $path;
            } elseif ($request->filled('image_url_text')) {
                // If user provided a URL string, use it
                $validated['image_url'] = $request->image_url_text;
            }

            if ($request->has('is_default') && $request->is_default) {
                TribeAppearancePart::where('tribe_id', $validated['tribe_id'])
                    ->where('part_type', $validated['part_type'])
                    ->where('id', '!=', $appearance->id)
                    ->update(['is_default' => false]);
            }

            $validated['is_default'] = $request->has('is_default') ? 1 : 0;
            $validated['is_active'] = $request->has('is_active') ? 1 : 0;
            $validated['display_order'] = $validated['display_order'] ?? $appearance->display_order;

            $appearance->update($validated);

            Log::info('Appearance Part Updated', ['id' => $appearance->id]);

            return redirect()->route('admin.appearance.index')
                ->with('success', 'Appearance part updated successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(TribeAppearancePart $appearance)
    {
        try {
            if ($appearance->image_url && !filter_var($appearance->image_url, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($appearance->image_url)) {
                Storage::disk('public')->delete($appearance->image_url);
            }

            $appearance->delete();
            return redirect()->route('admin.appearance.index')->with('success', 'Part deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete part.');
        }
    }

    public function toggleActive(TribeAppearancePart $appearance)
    {
        $appearance->update(['is_active' => !$appearance->is_active]);
        return redirect()->back()->with('success', 'Status toggled successfully!');
    }

    public function setDefault(TribeAppearancePart $appearance)
    {
        TribeAppearancePart::where('tribe_id', $appearance->tribe_id)
            ->where('part_type', $appearance->part_type)
            ->where('id', '!=', $appearance->id)
            ->update(['is_default' => false]);

        $appearance->update(['is_default' => true]);
        return redirect()->back()->with('success', 'Default set successfully!');
    }
}
