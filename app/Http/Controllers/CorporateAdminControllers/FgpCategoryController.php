<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FgpCategory; // Import the model
use Yajra\DataTables\Facades\DataTables;

class FgpCategoryController extends Controller
{
    // Display all categories
    public function index()
    {
        // Check permission for viewing flavor categories
        if (!auth()->check() || !auth()->user()->can('flavor_category.view')) {
            abort(403, 'Unauthorized access to Flavor Categories');
        }

        $totalCategories = FgpCategory::count();
        if (request()->ajax()) {
            $categories = FgpCategory::query();

            return DataTables::of($categories)
                ->addColumn('created_at', function ($category) {
                    return $category->formatted_created_at;
                })
                ->addColumn('action', function ($category) {
                    $editUrl = route('fgpcategory.edit', $category->category_ID);
                    $deleteUrl = route('fgpcategory.destroy', $category->category_ID);

                    $actions = '<div class="d-flex">';
                    
                    // Edit button - check permission
                    if (auth()->check() && auth()->user()->can('flavor_category.update')) {
                        $actions .= '<a href="'.$editUrl.'" class="edit-category">
                            <i class="ti ti-edit fs-20" style="color: #FF7B31;"></i>
                        </a>';
                    }
                    
                    // Delete button - check permission
                    if (auth()->check() && auth()->user()->can('flavor_category.delete')) {
                        $actions .= '<form action="'.$deleteUrl.'" method="POST">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="submit" class="ms-4 delete-category">
                                <i class="ti ti-trash fs-20" style="color: #FF3131;"></i>
                            </button>
                        </form>';
                    }
                    
                    $actions .= '</div>';
                    
                    return $actions;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('corporate_admin.fgp_category.index', compact('totalCategories'));
    }

    // Show form to create a new category
    public function create()
    {
        // Check permission for creating flavor categories
        if (!auth()->check() || !auth()->user()->can('flavor_category.create')) {
            abort(403, 'Unauthorized access to create Flavor Categories');
        }

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

        return redirect()->route('fgpcategory.index')->with('success', 'Category updated successfully.');
    }


    // Delete category
    public function destroy(FgpCategory $fgpcategory)
    {

        try {

            $fgpcategory->delete();
            return redirect()->route('fgpcategory.index')->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('fgpcategory.index')->with('error', 'Failed to delete user.');
        }
    }
}
