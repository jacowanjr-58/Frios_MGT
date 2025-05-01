<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Models\FpgItem;
use App\Models\FpgCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class FpgItemsController extends Controller
{
    public function index()
    {
        $items = FpgItem::with('categories')->get();
        $totalItems = $items->count();
        return view('corporate_admin.fpg_items.index', compact('items','totalItems'));
    }

    public function create()
    {
        $categorizedCategories = [
            'Availability' => FpgCategory::whereJsonContains('type', 'Availability')->get(),
            'Flavor' => FpgCategory::whereJsonContains('type', 'Flavor')->get(),
            'Allergen' => FpgCategory::whereJsonContains('type', 'Allergen')->get()
        ];
    
        return view('corporate_admin.fpg_items.create', compact('categorizedCategories'));
    }
    
    
    
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_ID' => 'required|array',
            'category_ID.*' => 'exists:fpg_categories,category_ID',
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
            $validated['image1'] = $request->file('image1')->store('images/fpg_items', 'public');
        }
        if ($request->hasFile('image2')) {
            $validated['image2'] = $request->file('image2')->store('images/fpg_items', 'public');
        }
        if ($request->hasFile('image3')) {
            $validated['image3'] = $request->file('image3')->store('images/fpg_items', 'public');
        }
    
        // Create the FpgItem
        $item = FpgItem::create([
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
    
        return redirect()->route('corporate_admin.fpgitem.index')->with('success', 'FPG Item added successfully.');
    }
    

    
    public function edit(FpgItem $fpgitem)
{
    $categorizedCategories = [
        'Availability' => FpgCategory::whereJsonContains('type', 'Availability')->get(),
        'Flavor' => FpgCategory::whereJsonContains('type', 'Flavor')->get(),
        'Allergen' => FpgCategory::whereJsonContains('type', 'Allergen')->get()
    ];

    // Fetch selected categories for the item
    $selectedCategories = $fpgitem->categories->pluck('category_ID')->toArray();

    return view('corporate_admin.fpg_items.edit', compact('fpgitem', 'categorizedCategories', 'selectedCategories'));
}

    public function update(Request $request, FpgItem $fpgitem)
    {
        $validated = $request->validate([
            'category_ID' => 'required|array',
            'category_ID.*' => 'exists:fpg_categories,category_ID',
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
            $request->file('image1')->store('images/fpg_items', 'public') : $fpgitem->image1;
    
        $validated['image2'] = $request->hasFile('image2') ? 
            $request->file('image2')->store('images/fpg_items', 'public') : $fpgitem->image2;
    
        $validated['image3'] = $request->hasFile('image3') ? 
            $request->file('image3')->store('images/fpg_items', 'public') : $fpgitem->image3;
    
        // Update the item
        $fpgitem->update([
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
        $fpgitem->categories()->sync($validated['category_ID']);
    
        return redirect()->route('corporate_admin.fpgitem.index')->with('success', 'FPG Item updated successfully.');
    }
    
    

    public function destroy(FpgItem $fpgitem)
    {
        try {
            // Delete associated images if they exist
            if ($fpgitem->image1) {
                Storage::disk('public')->delete($fpgitem->image1);
            }
            if ($fpgitem->image2) {
                Storage::disk('public')->delete($fpgitem->image2);
            }
            if ($fpgitem->image3) {
                Storage::disk('public')->delete($fpgitem->image3);
            }
    
            // Detach related categories in the pivot table
            $fpgitem->categories()->detach();
    
            // Delete the item
            $fpgitem->delete();
    
            return redirect()->route('corporate_admin.fpgitem.index')->with('success', 'FPG Item deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('corporate_admin.fpgitem.index')->with('error', 'Failed to delete FPG Item.');
        }
    }
    
    public function updateOrderable(Request $request)
    {
        try {
            $item = FpgItem::findOrFail($request->id);
            $item->orderable = $request->orderable;
            $item->save();

            return response()->json(['success' => true, 'message' => 'Orderable status updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update orderable status.']);
        }
    }

    public function availability()
{
    $flavors = FpgItem::with('categories')->get();
    $totalItems = $flavors->count();

    return view('corporate_admin.fpg_items.availability_flavor', compact('flavors','totalItems'));
}
public function updateStatus(Request $request, $id)
{
    $item = FpgItem::where('fgp_item_id', $id)->firstOrFail(); // Use explicit key
    $item->orderable = $request->orderable;
    $item->save();

    return response()->json(['success' => true, 'message' => 'Orderable status updated successfully.']);
}



public function updateMonth(Request $request, $id)
{
    $item = FpgItem::findOrFail($id);
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
