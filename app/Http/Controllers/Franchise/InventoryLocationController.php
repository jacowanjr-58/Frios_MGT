<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryLocation;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class InventoryLocationController extends Controller
{
    public function index($franchisee)
    {
        $franchiseId = (int)$franchisee;

        if (request()->ajax()) {
            $query = InventoryLocation::where('franchise_id', $franchiseId);

            return DataTables::of($query)
                ->addColumn('action', function ($location) use ($franchiseId) {
                    return '
                        <div class="d-flex">
                            <a href="'.route('franchise.locations.edit', ['franchisee' => $franchiseId, 'location' => $location->locations_ID]).'">
                                <i class="ti ti-edit fs-20" style="color: #FF7B31;"></i>
                            </a>
                            <form action="'.route('franchise.locations.destroy', ['franchisee' => $franchiseId, 'location' => $location->locations_ID]).'" method="POST" class="ms-4">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="submit" class="delete-location" style="border: none; background: none; padding: 0;">
                                   <i class="ti ti-trash fs-20" style="color: #FF3131;"></i>
                                </button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $locationCount = InventoryLocation::where('franchise_id', $franchiseId)->count();
        return view('franchise_admin.inventory.location.index', compact('locationCount'));
    }

    public function create($franchisee)
    {
        return view('franchise_admin.inventory.location.create');
    }

    public function store(Request $request, $franchisee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        InventoryLocation::create([
            'name' => $request->name,
            'franchise_id' => $franchisee,
        ]);

        return redirect()->route('franchise.locations.index', ['franchisee' => $franchisee])
            ->with('success', 'Location created successfully.');
    }

    public function edit($franchisee, $location)
    {
        $location = InventoryLocation::findOrFail($location);
        return view('franchise_admin.inventory.location.edit', compact('location', 'franchisee'));
    }

    public function update(Request $request, $franchisee, $location)
    {
        $location = InventoryLocation::findOrFail($location);
        
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $location->update([
            'name' => $request->name,
        ]);

        return redirect()->route('franchise.locations.index', ['franchisee' => $franchisee])
            ->with('success', 'Location updated successfully.');
    }

    public function destroy($franchisee, $location)
    {
        $location = InventoryLocation::findOrFail($location);
        $location->delete();

        return redirect()->route('franchise.locations.index', ['franchisee' => $franchisee])
            ->with('success', 'Location deleted successfully.');
    }

    private function authorizeLocation(InventoryLocation $location)
    {
        if ($location->franchise_id !== Auth::user()->franchise_id) {
            abort(403, 'Unauthorized action.');
        }
    }
}
