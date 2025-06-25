<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\InventoryMaster;
use App\Models\FgpItem;
use App\Models\InventoryLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Franchise;
use Yajra\DataTables\Facades\DataTables;

class InventoryController extends Controller
{
    /**
     * Display a listing of the franchisee's inventory master records.
     */
    public function index(Request $request, $franchisee)
    {
        $user = Auth::user();

        // Verify that the user has access to this franchisee
        $hasAccess = DB::table('user_franchisees')
            ->where('user_id', $user->user_id)
            ->where('franchise_id', (int) $franchisee)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Unauthorized access to this franchisee');
        }

        if ($request->ajax()) {
            $query = InventoryMaster::with('flavor')
                ->where('franchise_id', (int) $franchisee)
                ->where('total_quantity', '>', 0);

            return DataTables::of($query)
                ->filterColumn('item_name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('custom_item_name', 'like', "%{$keyword}%")
                            ->orWhereHas('flavor', function ($q) use ($keyword) {
                                $q->where('name', 'like', "%{$keyword}%");
                            });
                    });
                })
                ->addColumn('item_name', function ($inventory) {
                    return $inventory->item_name;
                })
                ->addColumn('total_quantity', function ($inventory) {
                    return $inventory->total_quantity;
                })
                ->addColumn('totals_breakdown', function ($inventory) {
                    return $inventory->cases . ' cases, ' . $inventory->units . ' units';
                })
                ->addColumn('base_cost', function ($inventory) {
                    if ($inventory->flavor) {
                        return '$' . number_format($inventory->flavor->case_cost, 2);
                    }
                    return '$' . number_format($inventory->cogs_case, 2);
                })
                ->addColumn('action', function ($inventory) use ($franchisee) {
                    return '<a href="' . route('franchise.inventory.edit', ['franchisee' => $franchisee, 'inventoryMaster' => $inventory->inventory_id]) . '" class="text-blue-600 hover:underline">Edit</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $totalInventories = InventoryMaster::where('franchise_id', (int) $franchisee)
            ->where('total_quantity', '>', 0)
            ->count();

        return view('franchise_admin.inventory.index', compact('totalInventories'));
    }


    /**
     * Show the form for creating a new inventory master record.
     * Create either an item from Corporate FGP items or a custom item.
     */
    public function create(Request $request, $franchisee)
    {

        $user = Auth::user();

        // Verify that the user has access to this franchisee
        $hasAccess = DB::table('user_franchisees')
            ->where('user_id', $user->user_id)
            ->where('franchise_id', (int) $franchisee)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Unauthorized access to this franchisee');
        }

        $fgpItems = FgpItem::orderBy('name')->get();
        $locations = InventoryLocation::where('franchise_id', $franchisee)
            ->orderBy('name')
            ->get();
        $today = now()->format('Y-m-d');

        return view('franchise_admin.inventory.create', compact(
            'fgpItems',
            'locations',
            'today'
        ));
    }

    /**
     * Store a newly created inventory master record in storage.
     *
     * This method handles both corporate items (by fpg_items_id) and custom items (by name).
     */
    public function store(Request $request, $franchisee)
    {
        $user = Auth::user();

        // Verify that the user has access to this franchisee
        $hasAccess = DB::table('user_franchisees')
            ->where('user_id', $user->user_id)
            ->where('franchise_id', (int) $franchisee)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Unauthorized access to this franchisee');
        }

        // 1️⃣ Validation
        $data = $request->validate([
            'fgp_item_id' => ['nullable', 'exists:fgp_items,fgp_item_id'],
            'custom_item_name' => ['nullable', 'string', 'max:255'],
            'stock_count_date' => ['required', 'date'],
            'case_quantity' => ['required', 'integer', 'min:0'],
            'unit_quantity' => ['required', 'integer', 'min:0'],
            'split_factor' => ['required', 'integer', 'min:1'],
            'cogs_case' => ['nullable', 'numeric', 'min:0'],
            'cogs_unit' => ['nullable', 'numeric', 'min:0'],
            'wholesale_case' => ['nullable', 'numeric', 'min:0'],
            'wholesale_unit' => ['nullable', 'numeric', 'min:0'],
            'retail_case' => ['nullable', 'numeric', 'min:0'],
            'retail_unit' => ['nullable', 'numeric', 'min:0'],
            'image1' => ['nullable', 'image', 'max:2048'],
            'image2' => ['nullable', 'image', 'max:2048'],
            'image3' => ['nullable', 'image', 'max:2048'],
            'allocations' => ['required', 'array'],
            'allocations.*.cases' => ['required', 'integer', 'min:0'],
            'allocations.*.units' => ['required', 'integer', 'min:0'],
        ]);

        // 2️⃣ Ensure at least one of corporate vs custom
        if (empty($data['fgp_item_id']) && empty($data['custom_item_name'])) {
            return back()->withInput()->withErrors([
                'fgp_item_id' => 'Please select a corporate item or enter a custom name.'
            ]);
        }

        // 3️⃣ Handle duplicates: if corporate selected with no custom name and record exists
        if (!empty($data['fgp_item_id']) && empty($data['custom_item_name'])) {
            $exists = InventoryMaster::where('franchise_id', $franchisee)
                ->where('fgp_item_id', $data['fgp_item_id'])
                ->exists();
            if ($exists) {
                $fgp = FgpItem::find($data['fgp_item_id']);
                $data['custom_item_name'] = $fgp->name . ' copy';
                session()->flash('warning', 'A record already exists; creating a duplicate as "' . $data['custom_item_name'] . '".');
            }
        }

        // 4️⃣ Compute and verify total quantity
        $totalQty = $data['case_quantity'] * $data['split_factor'] + $data['unit_quantity'];
        $sumAlloc = 0;
        foreach ($data['allocations'] as $alloc) {
            $sumAlloc += $alloc['cases'] * $data['split_factor'] + $alloc['units'];
        }
        if ($sumAlloc !== $totalQty) {
            return back()->withInput()->withErrors([
                'allocations' => "Sum of allocations ({$sumAlloc}) must equal total quantity ({$totalQty})."
            ]);
        }

        // 5️⃣ Persist master and allocations
        DB::transaction(function () use ($data, $franchisee, $request, $totalQty) {
            $master = InventoryMaster::create([
                'franchise_id' => (int)$franchisee,
                'fgp_item_id' => $data['fgp_item_id'],
                'custom_item_name' => $data['custom_item_name'],
                'stock_count_date' => $data['stock_count_date'],
                'total_quantity' => $totalQty,
                'split_factor' => $data['split_factor'],
                'cogs_case' => $data['cogs_case'],
                'cogs_unit' => $data['cogs_unit'],
                'wholesale_case' => $data['wholesale_case'],
                'wholesale_unit' => $data['wholesale_unit'],
                'retail_case' => $data['retail_case'],
                'retail_unit' => $data['retail_unit'],
            ]);
            

            // 6️⃣ Handle images: upload or inherit
            $fgpItem = $data['fgp_item_id'] ? FgpItem::find($data['fgp_item_id']) : null;
            foreach (['image1', 'image2', 'image3'] as $imgField) {
                if ($request->hasFile($imgField)) {
                    $master->$imgField = $request->file($imgField)->store('inventory_images', 'public');
                } elseif ($fgpItem && $fgpItem->$imgField) {
                    $master->$imgField = $fgpItem->$imgField;
                }
            }
            $master->save();

            // 7️⃣ Create allocations
            foreach ($data['allocations'] as $locId => $alloc) {
                $qty = $alloc['cases'] * $master->split_factor + $alloc['units'];
                if ($qty > 0) {
                    $master->allocations()->create([
                        'franchise_id' => (int)$franchisee,
                        'inventory_id' => $master->inventory_id,
                        'location_id' => $locId,
                        'location' => $locId,
                        'allocated_cases' => $alloc['cases'],
                        'allocated_units' => $alloc['units'],
                        'allocated_quantity' => $qty,
                        'quantity' => $qty,
                    ]);
                }
            }
        });

        return redirect()->route('franchise.inventory.index', ['franchisee' => $franchisee])
            ->with('success', 'Inventory created successfully.');
    }
    /**
     * Show the form for editing the specified inventory master record.
     *
     */
    public function edit($franchisee, $inventoryMaster)
    {
        $user = Auth::user();
        
        // Verify that the user has access to this franchisee
        $hasAccess = DB::table('user_franchisees')
            ->where('user_id', $user->user_id)
            ->where('franchise_id', (int) $franchisee)
            ->exists();
           
        if (!$hasAccess) {
            abort(403, 'Unauthorized access to this franchisee');
        }

        // Get the specific inventory master record
        $inventoryMaster = InventoryMaster::where('franchise_id', (int) $franchisee)
            ->where('inventory_id', $inventoryMaster)
            ->firstOrFail();

        // eager-load the corporate item and allocations
        $inventoryMaster->load('flavor', 'allocations');

        // fetch locations for the allocation grid
        $locations = InventoryLocation::where('franchise_id', (int) $franchisee)
            ->orderBy('name')
            ->get();

        // build cases/units breakdown
        $existingAllocations = $inventoryMaster->allocations
            ->mapWithKeys(function ($alloc) use ($inventoryMaster) {
                $split = $inventoryMaster->split_factor;
                $qty = $alloc->allocated_quantity;
                return [
                    $alloc->location_id => [
                        'cases' => intdiv($qty, $split),
                        'units' => $qty % $split,
                    ]
                ];
            })->toArray();

        return view('franchise_admin.inventory.edit', compact(
            'inventoryMaster',
            'locations',
            'existingAllocations'
        ));
    }

    /**
     * Update the specified inventory master record in storage.
     */
    public function update(Request $request, $franchisee, $inventoryMaster)
    {
        $user = Auth::user();
        
        // Verify that the user has access to this franchisee
        $hasAccess = DB::table('user_franchisees')
            ->where('user_id', $user->user_id)
            ->where('franchise_id', (int) $franchisee)
            ->exists();
           
        if (!$hasAccess) {
            abort(403, 'Unauthorized access to this franchisee');
        }

        // Get the specific inventory master record
        $inventoryMaster = InventoryMaster::where('franchise_id', (int) $franchisee)
            ->where('inventory_id', $inventoryMaster)
            ->firstOrFail();

        // 1️⃣ Validation
        $data = $request->validate([
            'stock_count_date' => 'required|date',
            'split_factor' => 'required|integer|min:1',
            'cogs_case' => 'nullable|numeric|min:0',
            'cogs_unit' => 'nullable|numeric|min:0',
            'wholesale_case' => 'nullable|numeric|min:0',
            'wholesale_unit' => 'nullable|numeric|min:0',
            'retail_case' => 'nullable|numeric|min:0',
            'retail_unit' => 'nullable|numeric|min:0',
            'total_quantity' => 'required|integer|min:0',
            'custom_item_name' => 'nullable|string|max:255',
            'image1' => 'nullable|image|max:2048',
            'image2' => 'nullable|image|max:2048',
            'image3' => 'nullable|image|max:2048',
            'allocations' => 'required|array',
            'allocations.*.cases' => 'required|integer|min:0',
            'allocations.*.units' => 'required|integer|min:0',
        ]);

        // 2️⃣ Verify allocation totals match
        $computed = 0;
        foreach ($data['allocations'] as $locId => $alloc) {
            $computed += $alloc['cases'] * $data['split_factor'] + $alloc['units'];
        }
        if ($computed !== (int) $data['total_quantity']) {
            return back()
                ->withInput()
                ->withErrors([
                    'allocations' => "Sum of allocations ({$computed}) must equal total quantity ({$data['total_quantity']})."
                ]);
        }

        // 3️⃣ Atomically update master, images, and allocations
        try {
            DB::transaction(function () use ($inventoryMaster, $data, $request) {
                // Update all the simple fields
                $inventoryMaster->update([
                    'stock_count_date' => $data['stock_count_date'],
                    'split_factor' => $data['split_factor'],
                    'cogs_case' => $data['cogs_case'] ?? 0,
                    'cogs_unit' => $data['cogs_unit'] ?? 0,
                    'wholesale_case' => $data['wholesale_case'] ?? 0,
                    'wholesale_unit' => $data['wholesale_unit'] ?? 0,
                    'retail_case' => $data['retail_case'] ?? 0,
                    'retail_unit' => $data['retail_unit'] ?? 0,
                    'total_quantity' => $data['total_quantity'],
                    'custom_item_name' => $data['custom_item_name'],
                ]);

                // Handle images
                foreach (['image1', 'image2', 'image3'] as $imgField) {
                    if ($request->hasFile($imgField)) {
                        // Delete old file if it exists and is not a corporate image
                        if ($inventoryMaster->$imgField && 
                            (!$inventoryMaster->flavor || 
                             $inventoryMaster->$imgField !== $inventoryMaster->flavor->$imgField)) {
                            Storage::disk('public')->delete($inventoryMaster->$imgField);
                        }
                        $inventoryMaster->$imgField = $request->file($imgField)->store('inventory_images', 'public');
                    }
                }
                $inventoryMaster->save();

                // Clear & re‐create allocations
                $inventoryMaster->allocations()->delete();
                foreach ($data['allocations'] as $locId => $alloc) {
                    $qty = $alloc['cases'] * $inventoryMaster->split_factor + $alloc['units'];
                    if ($qty > 0) {
                        $inventoryMaster->allocations()->create([
                            'franchise_id' => (int)$franchisee,
                            'location_id' => $locId,
                            'location' => $locId,
                            'allocated_cases' => $alloc['cases'],
                            'allocated_units' => $alloc['units'],
                            'allocated_quantity' => $qty,
                            'quantity' => $qty,
                        ]);
                    }
                }
            });

            return redirect()
                ->route('franchise.inventory.index', ['franchisee' => $franchisee])
                ->with('success', 'Inventory updated successfully.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update inventory. Please try again.']);
        }
    }

    /**
     * Remove the specified inventory master record from storage.
     */
    public function destroy(InventoryMaster $inventoryMaster)
    {
        //  $this->authorize('delete', $inventoryMaster);

        $franchiseId = Auth::user()->franchise_id;
        if ($inventoryMaster->franchise_id !== $franchiseId) {
            abort(403);
        }

        // Deleting the master row will cascade to allocations & transactions via FKs
        $inventoryMaster->delete();

        return redirect()
            ->route('franchise.inventory.index')
            ->with('success', 'Inventory line deleted.');
    }
}
