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
            $query = InventoryLocation::where('franchisee_id', $franchiseId);

            return DataTables::of($query)
                ->addColumn('action', function ($location) use ($franchiseId) {
                    return '
                        <div class="d-flex">
                            <a href="'.route('franchise.locations.edit', ['franchisee' => $franchiseId, 'location' => $location->locations_ID]).'">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17 3C17.2626 2.73735 17.5744 2.52901 17.9176 2.38687C18.2608 2.24473 18.6286 2.17157 19 2.17157C19.3714 2.17157 19.7392 2.24473 20.0824 2.38687C20.4256 2.52901 20.7374 2.73735 21 3C21.2626 3.26264 21.471 3.57444 21.6131 3.9176C21.7553 4.26077 21.8284 4.62856 21.8284 5C21.8284 5.37143 21.7553 5.73923 21.6131 6.08239C21.471 6.42555 21.2626 6.73735 21 7L7.5 20.5L2 22L3.5 16.5L17 3Z" stroke="#FF7B31" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                            <form action="'.route('franchise.locations.destroy', ['franchisee' => $franchiseId, 'location' => $location->locations_ID]).'" method="POST" class="ms-4">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="submit" class="delete-location" style="border: none; background: none; padding: 0;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3 6H5H21" stroke="#FF3131" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="#FF3131" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $locationCount = InventoryLocation::where('franchisee_id', $franchiseId)->count();
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
            'franchisee_id' => $franchisee,
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
        if ($location->franchisee_id !== Auth::user()->franchisee_id) {
            abort(403, 'Unauthorized action.');
        }
    }
}
