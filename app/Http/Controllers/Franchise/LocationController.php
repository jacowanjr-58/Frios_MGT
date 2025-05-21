<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::where('franchisee_id', Auth::user()->franchisee_id)->get();
        $locationCount = Location::where('franchisee_id', Auth::user()->franchisee_id)->count();
        return view('franchise_admin.inventory.location.index', compact('locations','locationCount'));
    }

    public function create()
    {
        return view('franchise_admin.inventory.location.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Location::create([
            'name' => $request->name,
            'franchisee_id' => Auth::user()->franchisee_id,
        ]);

        return redirect()->route('franchise.locations.index')->with('success', 'Location created successfully.');
    }

    public function edit(Location $location)
    {
        $this->authorizeLocation($location);

        return view('franchise_admin.inventory.location.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $this->authorizeLocation($location);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $location->update([
            'name' => $request->name,
        ]);

        return redirect()->route('franchise.locations.index')->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location)
    {
        $this->authorizeLocation($location);

        $location->delete();

        return redirect()->route('franchise.locations.index')->with('success', 'Location deleted successfully.');
    }

    private function authorizeLocation(Location $location)
    {
        if ($location->franchisee_id !== Auth::user()->franchisee_id) {
            abort(403, 'Unauthorized action.');
        }
    }
}
