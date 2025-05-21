<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FgpCategory; // Import the model

class FgpCategoryController extends Controller
{
    // Display all categories
    public function index()
    {
        $categories = FgpCategory::all();
        $totalCategories = $categories->count();
        return view('corporate_admin.fgp_category.index', compact('categories','totalCategories'));
    }

    // Show form to create a new category
    public function create()
    {
        return view('corporate_admin.fgp_category.create');
    }

    // Store the new category
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255', // Changed from array to string
        ]);

        FgpCategory::create([
            'name' => $request->name,
            'type' => $request->type, // Store as a simple string
        ]);

        return redirect()->route('corporate_admin.fgpcategory.index')->with('success', 'Category created successfully.');
    }


    // Show edit form
    public function edit(FgpCategory $fgpcategory)
    {
        return view('corporate_admin.fgp_category.edit', compact('fgpcategory'));
    }


    // Update category
    public function update(Request $request, FgpCategory $fgpcategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255', // Ensure type is a string (single-select)
        ]);

        $fgpcategory->update([
            'name' => $request->name,
            'type' => $request->type, // Store as a string (not JSON)
        ]);

        return redirect()->route('corporate_admin.fgpcategory.index')->with('success', 'Category updated successfully.');
    }


    // Delete category
    public function destroy(FgpCategory $fgpcategory)
    {

        try {

            $fgpcategory->delete();
            return redirect()->route('corporate_admin.fgpcategory.index')->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('corporate_admin.fgpcategory.index')->with('error', 'Failed to delete user.');
        }
    }
}
