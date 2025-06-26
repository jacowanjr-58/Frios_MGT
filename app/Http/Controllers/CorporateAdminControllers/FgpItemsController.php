<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Models\FgpItem;
use App\Models\FgpCategory;
use App\Models\Franchise;
use App\Models\FgpOrder;
use App\Models\FgpOrderItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class FgpItemsController extends Controller
{
    public function index( $franchise)
    {
       
        $franchise = Franchise::find(intval($franchise));
        
        if (request()->ajax()) {
            // Updated filtering logic: Get FGP items that have been ordered by the selected franchise
            // Logic: franchise_id → fgp_orders → fgp_order_items → fgp_item_id
            $items = FgpItem::whereHas('orderItems', function ($query) use ($franchise) {
                $query->whereHas('order', function ($orderQuery) use ($franchise) {
                    $orderQuery->where('franchise_id', $franchise->id);
                });
            })->with('categories');

            $totalItems = $items->count();

            return DataTables::of($items)
                ->addColumn('categories', function ($item) {
                    $formattedCategories = '';
                    if($item->categories->isNotEmpty()) {
                        foreach($item->categories as $category) {
                            $formattedCategories .= '<span class="badge bg-primary me-2 mb-1">'.$category->name.'</span>';
                        }
                    } else {
                        $formattedCategories = 'No Category';
                    }
                    return '<div class="d-flex flex-wrap">'.$formattedCategories.'</div>';
                })
                ->filterColumn('categories', function ($query, $keyword) {
                    $query->whereHas('categories', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%$keyword%");
                    });
                })
                ->addColumn('action', function ($item) use ($franchise) {
                    // Check if user has any permissions for actions
                    if (!auth()->check() || !(auth()->user()->can('frios_flavors.edit') || auth()->user()->can('frios_flavors.delete'))) {
                        return ''; // Return empty string if no permissions
                    }

                    $editUrl = route('franchise.fgpitem.edit', ['franchise' => $franchise->id, 'fgpitem' => $item->id]);
                    $deleteUrl = route('franchise.fgpitem.destroy', ['franchise' => $franchise->id, 'fgpitem' => $item->id]);

                    $actions = '<div class="d-flex">';
                    
                    // Edit button - check permission
                    if (auth()->user()->can('frios_flavors.edit')) {
                        $actions .= '<a href="'.$editUrl.'" class="edit-user">
                            <i class="ti ti-edit fs-20" style="color: #FF7B31;"></i>
                        </a>';
                    }
                    
                    // Delete button - check permission
                    if (auth()->user()->can('frios_flavors.delete')) {
                        $actions .= '<form action="'.$deleteUrl.'" method="POST">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="submit" class="ms-4 delete-fgpitem">
                                <i class="ti ti-trash fs-20" style="color: #FF3131;"></i>
                            </button>
                        </form>';
                    }
                    
                    $actions .= '</div>';
                    
                    return $actions;
                })
                ->rawColumns(['action', 'categories'])
                ->make(true);
        }

        // Get total items for this franchise (based on orders)
        $totalItems = FgpItem::whereHas('orderItems', function ($query) use ($franchise) {
            $query->whereHas('order', function ($orderQuery) use ($franchise) {
                $orderQuery->where('franchise_id', $franchise->id);
            });
        })->count();

        return view('corporate_admin.fgp_items.index', compact('totalItems' , 'franchise'));
    }

    public function create($franchise)
    {
        $categorizedCategories = [
            'Availability' => FgpCategory::where('type', 'Availability')->get(),
            'Flavor' => FgpCategory::where('type', 'Flavor')->get(),
            'Allergen' => FgpCategory::where('type', 'Allergen')->get()
        ];

        $categories = FgpCategory::all();

        return view('corporate_admin.fgp_items.create', compact('categorizedCategories', 'categories', 'franchise'));
    }

    public function store(Request $request, $franchise)
    {
       
        $franchise = Franchise::find(intval($franchise));
        $franchise_id = $franchise->id;
        
        $validated = $request->validate([
            'fgp_category_id' => 'required|exists:fgp_categories,id',   
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'case_cost' => 'required|numeric|min:0',
            'internal_inventory' => 'required|integer|min:0',
            'image1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image uploads
        if ($request->hasFile('image1')) {
            $validated['image1'] = $request->file('image1')->store('images/fgp_items', 'public');
        }
        if ($request->hasFile('image2')) {
            $validated['image2'] = $request->file('image2')->store('images/fgp_items', 'public');
        }
        if ($request->hasFile('image3')) {
            $validated['image3'] = $request->file('image3')->store('images/fgp_items', 'public');
        }

        // Create the FgpItem with proper field names matching migration
        $item = FgpItem::create([
            'fgp_category_id' => $validated['fgp_category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'case_cost' => $validated['case_cost'],
            'internal_inventory' => $validated['internal_inventory'],
            'split_factor' => 48, // Default value from migration
            'image1' => $validated['image1'] ?? null,
            'image2' => $validated['image2'] ?? null,
            'image3' => $validated['image3'] ?? null,
            'orderable' => 1, // Default value from migration
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('franchise.fgpitem.index', ['franchise' => $franchise->id])->with('success', 'Flavor Item added successfully.');
    }



    public function edit(FgpItem $fgpitem, $franchise)
    {
        $categorizedCategories = [
            'Availability' => FgpCategory::whereJsonContains('type', 'Availability')->get(),
            'Flavor' => FgpCategory::whereJsonContains('type', 'Flavor')->get(),
            'Allergen' => FgpCategory::whereJsonContains('type', 'Allergen')->get()
        ];

        // Fetch selected categories for the item
        $selectedCategories = $fgpitem->categories->pluck('category_ID')->toArray();

        return view('corporate_admin.fgp_items.edit', compact('fgpitem', 'categorizedCategories', 'selectedCategories', 'franchise'));
    }

    public function update(Request $request, FgpItem $fgpitem)
    {
        $validated = $request->validate([
            'category_ID' => 'required|array',
            'category_ID.*' => 'exists:fgp_categories,category_ID',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'case_cost' => 'required|numeric',
            'internal_inventory' => 'required|integer',
            'image1' => 'nullable|image',
            'image2' => 'nullable|image',
            'image3' => 'nullable|image',
        ]);

        // Preserve old images if new images are not uploaded
        $validated['image1'] = $request->hasFile('image1') ?
            $request->file('image1')->store('images/fgp_items', 'public') : $fgpitem->image1;

        $validated['image2'] = $request->hasFile('image2') ?
            $request->file('image2')->store('images/fgp_items', 'public') : $fgpitem->image2;

        $validated['image3'] = $request->hasFile('image3') ?
            $request->file('image3')->store('images/fgp_items', 'public') : $fgpitem->image3;

        // Update the item
        $fgpitem->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'case_cost' => $validated['case_cost'],
            'internal_inventory' => $validated['internal_inventory'],
            'image1' => $validated['image1'],
            'image2' => $validated['image2'],
            'image3' => $validated['image3'],
            'orderable' => 1,
        ]);

        // Sync categories in the pivot table
        $fgpitem->categories()->sync($validated['category_ID']);

        return redirect()->route('corporate_admin.fgpitem.index')->with('success', 'Fgp Item updated successfully.');
    }



    public function destroy(FgpItem $fgpitem)
    {
        try {
            // Delete associated images if they exist
            if ($fgpitem->image1) {
                Storage::disk('public')->delete($fgpitem->image1);
            }
            if ($fgpitem->image2) {
                Storage::disk('public')->delete($fgpitem->image2);
            }
            if ($fgpitem->image3) {
                Storage::disk('public')->delete($fgpitem->image3);
            }

            // Detach related categories in the pivot table
            $fgpitem->categories()->detach();

            // Delete the item
            $fgpitem->delete();

            if (request()->ajax()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('corporate_admin.fgpitem.index')->with('success', 'Fgp Item deleted successfully.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to delete Fgp Item.']);
            }

            return redirect()->route('corporate_admin.fgpitem.index')->with('error', 'Failed to delete Fgp Item.');
        }
    }

    public function updateOrderable(Request $request)
    {
        try {
            $item = FgpItem::findOrFail($request->id);
            $item->orderable = $request->orderable;
            $item->save();

            return response()->json(['success' => true, 'message' => 'Orderable status updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update orderable status.']);
        }
    }

    public function availability($franchise)
    {
            // Check permission for viewing Frios Availability
    if (!Auth::check() || !Auth::user()->can('frios_availability.view')) {
        abort(403, 'Unauthorized access to Frios Availability');
    }

        // Get flavors that have been ordered by this franchise using orderItems relationship
        $flavors = FgpItem::whereHas('orderItems', function ($query) use ($franchise) {
            $query->whereHas('order', function ($orderQuery) use ($franchise) {
                $orderQuery->where('franchise_id', $franchise);
            });
        })->with('categories')->get();
        
        $totalItems = $flavors->count();

        return view('corporate_admin.fgp_items.availability_flavor', compact('flavors','totalItems' , 'franchise'));
    }

public function updateStatus(Request $request, $id)
{
  
    // Check permission for updating Frios Availability
    if (!Auth::check() || !Auth::user()->can('frios_availability.update')) {
        return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
    }

    $item = FgpItem::findOrFail($id);
    $item->orderable = $request->orderable;
    $item->save();

    return response()->json(['success' => true, 'message' => 'Orderable status updated successfully.']);
}



public function updateMonth(Request $request, $id)
{
    // Check permission for updating Frios Availability
    if (!Auth::check() || !Auth::user()->can('frios_availability.update')) {
        return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
    }

    $item = FgpItem::findOrFail($id);
    $datesAvailable = json_decode($item->dates_available, true) ?? [];

    if ($request->available) {
        if (!in_array($request->month, $datesAvailable)) {
            $datesAvailable[] = $request->month;
        }
    } else {
        $datesAvailable = array_filter($datesAvailable, fn($m) => $m != $request->month);
    }

    $item->dates_available = json_encode(array_values($datesAvailable));
    $item->save();

    return response()->json(['success' => true, 'message' => 'Availability updated successfully.']);
}




}
