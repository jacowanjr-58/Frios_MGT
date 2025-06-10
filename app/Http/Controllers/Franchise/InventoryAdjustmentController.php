<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\InventoryMaster;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryAdjustmentController extends Controller
{
    public function adjustForm()
    {
        $franchiseId = Auth::user()->franchisee_id;
        $inventoryMasters = InventoryMaster::where('franchisee_id', $franchiseId)->get();
        return view('franchise_admin.inventory.adjust', [
            'inventoryMasters' => $inventoryMasters
        ]);
    }

    public function adjustUpdate(Request $request)
    {
        $franchiseId = Auth::user()->franchisee_id;
        $inventoryMasters = InventoryMaster::where('franchisee_id', $franchiseId)->get();

        foreach ($inventoryMasters as $master) {
            $id = $master->inventory_id;

            $new_cases = (int) $request->input("total_cases_$id");
            $new_units = (int) $request->input("total_units_$id");
            $split_factor = (int) $request->input("split_factor_$id");
            $new_total = ($new_cases * $split_factor) + $new_units;

            // Only process if changed
            if ($new_total !== (int)$master->total_quantity) {
                // Document the change in InventoryTransaction
                InventoryTransaction::create([
                    'inventory_id' => $id,
                    'reference' => "Adjustment for inventory ID: $id",
                    'created_by' => Auth::id(),
                    'quantity' => $new_total - (int)$master->total_quantity,
                    'note' => $request->input("note_$id", ''),
                    'type' => 'Manual Adjustment',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update the master record
                $master->total_quantity = $new_total;
                $master->save();
            }
        }

        return redirect()->back()->with('success', 'Inventory adjusted successfully.');
    }


    /**
     * Show the bulk price adjustment form.
     */
    public function showBulkPriceForm()
    {
        $franchiseId = Auth::user()->franchisee_id;
        $inventoryMasters = InventoryMaster::where('franchisee_id', $franchiseId)->get();
        // Return a view (to be created) for bulk price adjustment
        return view('franchise_admin.inventory.bulk_price', [
            'inventoryMasters' => $inventoryMasters
        ]);
    }

    /**
     * Handle the bulk price adjustment update.
     */
    public function updateBulkPrice(Request $request)
    {
        $franchiseId = Auth::user()->franchisee_id;
        $inventoryMasters = InventoryMaster::where('franchisee_id', $franchiseId)->get();

        foreach ($inventoryMasters as $master) {
            $id = $master->inventory_id;

            // Old values
            $old = [
                'cogs_case'      => $master->cogs_case,
                'cogs_unit'      => $master->cogs_unit,
                'wholesale_case' => $master->wholesale_case,
                'wholesale_unit' => $master->wholesale_unit,
                'retail_case'    => $master->retail_case,
                'retail_unit'    => $master->retail_unit,
            ];

            // New values from form
            $new = [
                'cogs_case'      => (float) $request->input("cogs_case_$id"),
                'cogs_unit'      => (float) $request->input("cogs_unit_$id"),
                'wholesale_case' => (float) $request->input("wholesale_case_$id"),
                'wholesale_unit' => (float) $request->input("wholesale_unit_$id"),
                'retail_case'    => (float) $request->input("retail_case_$id"),
                'retail_unit'    => (float) $request->input("retail_unit_$id"),
            ];

            // Calculate differentials
            $diffs = [
                $new['cogs_case']      - $old['cogs_case'],
                $new['cogs_unit']      - $old['cogs_unit'],
                $new['wholesale_case'] - $old['wholesale_case'],
                $new['wholesale_unit'] - $old['wholesale_unit'],
                $new['retail_case']    - $old['retail_case'],
                $new['retail_unit']    - $old['retail_unit'],
            ];

            // Only process if any price changed
            if (array_sum(array_map('abs', $diffs)) > 0) {
                // Encode differentials as ";" delimited string
                $diffString = implode(';', $diffs);

                InventoryTransaction::create([
                    'inventory_id' => $id,
                    'reference'    => $diffString,
                    'created_by'   => Auth::id(),
                    'quantity'     => 0,
                    'note'         => $request->input("notes_$id", ''),
                    'type'         => 'Price Change',
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);

                // Update the master record
                $master->cogs_case      = $new['cogs_case'];
                $master->cogs_unit      = $new['cogs_unit'];
                $master->wholesale_case = $new['wholesale_case'];
                $master->wholesale_unit = $new['wholesale_unit'];
                $master->retail_case    = $new['retail_case'];
                $master->retail_unit    = $new['retail_unit'];
                $master->save();
            }
        }

        return redirect()->back()->with('success', 'Prices updated successfully.');
    }
}
