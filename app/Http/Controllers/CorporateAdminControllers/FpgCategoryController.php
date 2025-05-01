<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FpgCategory; // Import the model

class FpgCategoryController extends Controller
{
    // Display all categories
    public function index()
    {
        $categories = FpgCategory::all();
        $totalCategories = $categories->count();
        return view('corporate_admin.fpg_category.index', compact('categories','totalCategories'));
    }

    // Show form to create a new category
    public function create()
    {
        return view('corporate_admin.fpg_category.create');
    }

    // Store the new category
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255', // Changed from array to string
        ]);
    
        FpgCategory::create([
            'name' => $request->name,
            'type' => $request->type, // Store as a simple string
        ]);
    
        return redirect()->route('corporate_admin.fpgcategory.index')->with('success', 'Category created successfully.');
    }
    

    // Show edit form
    public function edit(FpgCategory $fpgcategory)
    {
        return view('corporate_admin.fpg_category.edit', compact('fpgcategory'));
    }
    

    // Update category
    public function update(Request $request, FpgCategory $fpgcategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255', // Ensure type is a string (single-select)
        ]);
    
        $fpgcategory->update([
            'name' => $request->name,
            'type' => $request->type, // Store as a string (not JSON)
        ]);
    
        return redirect()->route('corporate_admin.fpgcategory.index')->with('success', 'Category updated successfully.');
    }
    

    // Delete category
    public function destroy(FpgCategory $fpgcategory)
    {
        
        try {

            $fpgcategory->delete();
            return redirect()->route('corporate_admin.fpgcategory.index')->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('corporate_admin.fpgcategory.index')->with('error', 'Failed to delete user.');
        }
    }
}
