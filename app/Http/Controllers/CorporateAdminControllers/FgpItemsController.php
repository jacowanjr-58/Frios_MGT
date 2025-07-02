<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Models\FgpItem;
use App\Models\FgpCategory;
use App\Models\Franchise;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class FgpItemsController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            // Show ALL items, not filtered by franchise (FGP items are global cororate items)
            $items = FgpItem::with('categories');


            return DataTables::of($items)
                ->addColumn('categories', function ($item) {
                    return $item->categories->pluck('name')
                        ->map(fn($name) => "<span class='badge bg-primary me-2 mb-1'>{$name}</span>")
                        ->implode(' ');
                })
                ->addColumn('action', function ($item) {
                    $editUrl = route('fgpitem.edit', [$item->id]);
                    $deleteUrl = route('fgpitem.destroy', [$item->id]);

                    $actions = '<div class="d-flex">';
                    if (Auth::user()->can('frios_flavors.edit')) {
                        $actions .= "<a href='{$editUrl}' class='edit-user'><i class='ti ti-edit fs-20 text-warning'></i></a>";
                    }
                    if (Auth::user()->can('frios_flavors.delete')) {
                        $actions .= "<form action='{$deleteUrl}' method='POST' class='ms-2'>"
                            . csrf_field() . method_field('DELETE')
                            . "<button type='submit' class='delete-fgpitem'><i class='ti ti-trash fs-20 text-danger'></i></button></form>";
                    }
                    $actions .= '</div>';

                    return $actions;
                })
                ->rawColumns(['categories', 'action'])
                ->make(true);
        }


        $totalItems =  FgpItem::with('categories')->count();
        return view('corporate_admin.fgp_items.index', compact('totalItems'));
    }



    public function create()
    {
        // Load top-level categories with their child subcategories
        $parents = FgpCategory::with('children')->whereNull('parent_id')->get();

        return view('corporate_admin.fgp_items.create', compact('parents'));
    }


    /*
### How Category Selections Are Stored
1. **Form Submission**: In your `create` and `edit` forms, you send an array `category_ids[]` containing selected subcategory IDs.
2. **Validation**: Controller validates each `category_ids.*` exists in `fgp_categories`.
3. **Sync Method**: Calling `$item->categories()->sync($request->category_ids)`:
   - **Sync** wipes out any existing pivot records for that item and inserts new records for each supplied ID.
   - Pivot table is `fgp_category_fgp_item` (with columns `fgp_item_id` and `fgp_category_id`).
   - Ensures the DB reflects exactly the set of categories the user selected.
4. **Eloquent Relations**: The `categories()` relation on `FgpItem` (`belongsToMany(FgpCategory::class, 'fgp_category_fgp_item')`) governs this pivot sync.

This approach keeps your many-to-many assignments clean and in sync with user selections.
 */


    public function store(Request $request)
    {
        // Validate input including array of category IDs
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:fgp_items,name',
            'description' => 'nullable|string',
            'case_cost' => 'required|numeric|min:0',
            'internal_inventory' => 'required|integer|min:0',
            'image1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_ids'   => 'array',
            'category_ids.*' => 'exists:fgp_categories,id'
        ]);

        if ($request->hasFile('image1')) {
            $validated['image1'] = $request->file('image1')->store('images/fgp_items', 'public');
        }
        if ($request->hasFile('image2')) {
            $validated['image2'] = $request->file('image2')->store('images/fgp_items', 'public');
        }
        if ($request->hasFile('image3')) {
            $validated['image3'] = $request->file('image3')->store('images/fgp_items', 'public');
        }


        // Create the item
        $item = FgpItem::create($validated);

        // Sync selected categories to pivot table fgp_category_fgp_item
        // This will insert or update rows linking the item to each selected category ID.
        $item->categories()->sync($request->category_ids ?? []);

            return redirect()->route('fgpitem.index')
            ->with('success', 'Item created successfully.');
    }



    public function edit(FgpItem $fgpitem)
    {
        $parents = FgpCategory::with('children')->whereNull('parent_id')->get();
        $fgpitem = FgpItem::findOrFail($fgpitem->id);

        return view('corporate_admin.fgp_items.edit', compact('parents', 'fgpitem'));
    }




    public function update(Request $request, FgpItem $fgpitem)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'description'        => 'nullable|string',
            'case_cost'          => 'required|numeric|min:0',
            'internal_inventory' => 'required|integer|min:0',
            'image1'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image2'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image3'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_ids'       => 'array',
            'category_ids.*'     => 'exists:fgp_categories,id'
        ]);

        if ($request->hasFile('image1')) {
            $validated['image1'] = $request->file('image1')->store('images/fgp_items', 'public');
        }
        if ($request->hasFile('image2')) {
            $validated['image2'] = $request->file('image2')->store('images/fgp_items', 'public');
        }
        if ($request->hasFile('image3')) {
            $validated['image3'] = $request->file('image3')->store('images/fgp_items', 'public');
        }


        $fgpitem->update($validated);
        // Sync pivot relations for edits as well
        $fgpitem->categories()->sync($request->category_ids ?? []);

        return redirect()->route('fgpitem.index')
            ->with('success', 'Item updated successfully.');
    }

    public function destroy(FgpItem $fgpitem)
    {
        if($fgpitem->orderItems->count() > 0) {
            return redirect()->route('fgpitem.index')->with('error', 'Fgp Item cannot be deleted because it has associated orders.');
        }

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

            return redirect()->route('fgpitem.index')->with('success', 'Fgp Item deleted successfully.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to delete Fgp Item.']);
            }

            return redirect()->route('fgpitem.index')->with('error', 'Failed to delete Fgp Item.');
        }
    }


    //this is used in the index.blade.php to update the orderable status of an item
    //it is called via an AJAX request when the user toggles the orderable switch
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

    public function availability()
    {
        // Check permission for viewing Frios Availability
        if (!Auth::check() || !Auth::user()->can('frios_availability.view')) {
            abort(403, 'Unauthorized access to Frios Availability');
        }

        // List flavors to bulk edit their availability.
        $flavors = FgpItem::with('categories')->orderby('name')->get();

        $totalItems = $flavors->count();
        return view('corporate_admin.fgp_items.availability_flavor', compact('flavors', 'totalItems'));
    }


    //this is used in the availability_flavor.blade.php to update the orderable status of an item
    //it is called via an AJAX request when the user toggles the orderable switch
    public function updateStatus(Request $request, $id)
    {
        // Check permission for updating Frios Availability
        if (!Auth::check() || !Auth::user()->can('frios_availability.edit')) {
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
        if (!Auth::check() || !Auth::user()->can('frios_availability.edit')) {
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


    public function calendarView()
    {
        $months = collect(range(1, 12))->mapWithKeys(function ($m) {
            return [$m => \Carbon\Carbon::create()->month($m)->format('M')];
        });

        $flavors = FgpItem::orderBy('name')->get();

        // Build month -> pops list
        $flavorsByMonth = [];
        foreach ($months as $num => $label) {
            $flavorsByMonth[$num] = $flavors->filter(function ($flavor) use ($num) {
                return in_array($num, $flavor->dates_available ?? []);
            });
        }

        return view('corporate_admin.fgp_items.viewCalendar', compact('months', 'flavorsByMonth'));
    }
}
