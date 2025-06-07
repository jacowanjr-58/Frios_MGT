<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryLocation;
use Illuminate\Support\Facades\Auth;

class InventoryLocationController extends Controller
{
    public function index()
    {
        $locations = InventoryLocation::where('franchisee_id', Auth::user()->franchisee_id)->get();
        $locationCount = InventoryLocation::where('franchisee_id', Auth::user()->franchisee_id)->count();
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

        InventoryLocation::create([
            'name' => $request->name,
            'franchisee_id' => Auth::user()->franchisee_id,
        ]);

        return redirect()->route('franchise.locations.index')->with('success', 'Location created successfully.');
    }

    public function edit(InventoryLocation $location)
    {
     //   $this->authorizeLocation($location);

        return view('franchise_admin.inventory.location.edit', compact('location'));
    }

    public function update(Request $request, InventoryLocation $location)
    {
       // $this->authorizeLocation($location);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $location->update([
            'name' => $request->name,
        ]);

        return redirect()->route('franchise.locations.index')->with('success', 'Location updated successfully.');
    }

    public function destroy(InventoryLocation $location)
    {
       // $this->authorizeLocation($location);

        $location->delete();

        return redirect()->route('franchise.locations.index')->with('success', 'Location deleted successfully.');
    }

    private function authorizeLocation(InventoryLocation $location)
    {
        if ($location->franchisee_id !== Auth::user()->franchisee_id) {
            abort(403, 'Unauthorized action.');
        }
    }
}
