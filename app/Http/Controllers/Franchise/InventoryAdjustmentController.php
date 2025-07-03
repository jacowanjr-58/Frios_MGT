<?php

namespace App\Http\Controllers\Franchise;

use App\Models\Franchise;
use Illuminate\Http\Request;
use App\Models\InventoryMaster;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\Auth;

class InventoryAdjustmentController extends Controller
{
    public function adjustForm(Franchise $franchise)
    {
        $inventoryMasters = InventoryMaster::where('franchise_id', $franchise->id)->get();
        return view('franchise_admin.inventory.adjust', [
            'inventoryMasters' => $inventoryMasters
        ]);
    }

   // ... existing code ...
   public function adjustUpdate(Request $request, Franchise $franchise)
   {
       $inventoryMasters = InventoryMaster::where('franchise_id', $franchise->id)->get();

       DB::beginTransaction();
       try {
           foreach ($inventoryMasters as $master) {
               $id = $master->inventory_id;

               $new_cases = (int) $request->input("total_cases_$id");
               $new_units = (int) $request->input("total_units_$id");
               $split_factor = (int) $request->input("split_factor_$id");
               $new_total = ($new_cases * $split_factor) + $new_units;
               $notes = $request->input("notes_$id", '');  // Fixed: Changed from note_ to notes_

               // Only process if quantities changed or notes updated
               if ($new_total !== (int)$master->total_quantity || $notes !== $master->notes) {
                   // Document the change in InventoryTransaction
                   InventoryTransaction::create([
                       'inventory_id' => $id,
                       'reference' => "Adjustment for inventory ID: $id",
                       'created_by' => Auth::id(),
                       'quantity' => $new_total - (int)$master->total_quantity,
                       'note' => $notes,  // Use the new notes
                       'type' => 'Manual Adjustment',
                       'created_at' => now(),
                       'updated_at' => now(),
                   ]);

                   // Update the master record
                   $master->update([
                       'total_quantity' => $new_total,
                       'notes' => $notes,  // Save notes to master record
                       // Also update the cases and units
                       'case_quantity' => $new_cases,
                       'unit_quantity' => $new_units
                   ]);
               }
           }

           DB::commit();
           return redirect()->back()->with('success', 'Inventory adjusted successfully.');
           
       } catch (\Exception $e) {
           DB::rollback();
           return redirect()->back()
               ->with('error', 'Failed to adjust inventory. Please try again.')
               ->withInput();
       }
   }
// ... existing code ...


    /**
     * Show the bulk price adjustment form.
     */
    public function showBulkPriceForm($franchisee)
    {
        $franchiseId = (int)$franchisee; //Auth::user()->franchise_id;
        $inventoryMasters = InventoryMaster::where('franchise_id', $franchiseId)->get();
        // Return a view (to be created) for bulk price adjustment
       
        return view('franchise_admin.inventory.bulk_price', [
            'inventoryMasters' => $inventoryMasters
        ]);
    }

    /**
     * Handle the bulk price adjustment update.
     */
    public function updateBulkPrice(Request $request, $franchisee)
    {
        $franchiseId = (int)$franchisee;
        $inventoryMasters = InventoryMaster::where('franchise_id', $franchiseId)->get();

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
