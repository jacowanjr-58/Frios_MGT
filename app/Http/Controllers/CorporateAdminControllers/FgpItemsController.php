<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Models\FgpItem;
use App\Models\FgpCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class FgpItemsController extends Controller
{
    public function index()
    {
        $items = FgpItem::with('categories')->get();
        $totalItems = $items->count();
        return view('corporate_admin.fgp_items.index', compact('items','totalItems'));
    }

    public function create()
    {
        $categorizedCategories = [
            'Availability' => FgpCategory::whereJsonContains('type', 'Availability')->get(),
            'Flavor' => FgpCategory::whereJsonContains('type', 'Flavor')->get(),
            'Allergen' => FgpCategory::whereJsonContains('type', 'Allergen')->get()
        ];

        return view('corporate_admin.fgp_items.create', compact('categorizedCategories'));
    }




    public function store(Request $request)
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

        // Create the FgpItem
        $item = FgpItem::create([
            'category_ID' => null, // Since it's a many-to-many relation
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'case_cost' => $validated['case_cost'],
            'internal_inventory' => $validated['internal_inventory'],
            'image1' => $validated['image1'] ?? null,
            'image2' => $validated['image2'] ?? null,
            'image3' => $validated['image3'] ?? null,
            'orderable' => 1,
        ]);

        // Attach categories in the pivot table
        $item->categories()->attach($validated['category_ID']);

        return redirect()->route('corporate_admin.fgpitem.index')->with('success', 'Fgp Item added successfully.');
    }



    public function edit(FgpItem $fgpitem)
{
    $categorizedCategories = [
        'Availability' => FgpCategory::whereJsonContains('type', 'Availability')->get(),
        'Flavor' => FgpCategory::whereJsonContains('type', 'Flavor')->get(),
        'Allergen' => FgpCategory::whereJsonContains('type', 'Allergen')->get()
    ];

    // Fetch selected categories for the item
    $selectedCategories = $fgpitem->categories->pluck('category_ID')->toArray();

    return view('corporate_admin.fgp_items.edit', compact('fgpitem', 'categorizedCategories', 'selectedCategories'));
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

            return redirect()->route('corporate_admin.fgpitem.index')->with('success', 'Fgp Item deleted successfully.');
        } catch (\Exception $e) {
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

    public function availability()
{
    $flavors = FgpItem::with('categories')->get();
    $totalItems = $flavors->count();

    return view('corporate_admin.fgp_items.availability_flavor', compact('flavors','totalItems'));
}
public function updateStatus(Request $request, $id)
{
    $item = FgpItem::where('fgp_item_id', $id)->firstOrFail(); // Use explicit key
    $item->orderable = $request->orderable;
    $item->save();

    return response()->json(['success' => true, 'message' => 'Orderable status updated successfully.']);
}



public function updateMonth(Request $request, $id)
{
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
