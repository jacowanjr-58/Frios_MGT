<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FgpCategory; // Import the model
use App\Models\Franchise;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class FgpCategoryController extends Controller
{
    // Display all categories
    public function index()
    {
        // Check permission for viewing flavor categories
        if (!Auth::check() || !Auth::user()->can('flavor_category.view')) {
            abort(403, 'Unauthorized access to Flavor Categories');
        }

        if (request()->ajax()) {
            // Start with categories query
            $categories = FgpCategory::query();
          
            // If only count is requested, return just the count
            if (request()->has('count_only')) {
                return response()->json(['count' => $categories->count()]);
            }

            return DataTables::of($categories)
                ->addColumn('created_at', function ($category) {
                    return $category->formatted_created_at;
                })
                ->addColumn('action', function ($category) {
                    $editUrl = route('franchise.fgpcategory.edit', ['franchise' => $category->id, 'fgpcategory' => $category->id]);
                    $deleteUrl = route('franchise.fgpcategory.destroy', ['franchise' => $category->id, 'fgpcategory' => $category->id]);

                    $actions = '<div class="d-flex">';
                    
                    // Edit button - check permission
                    if (Auth::check() && Auth::user()->can('flavor_category.update') && $category->id > 12) {
                        $actions .= '<a href="'.$editUrl.'" class="edit-category">
                            <i class="ti ti-edit fs-20" style="color: #FF7B31;"></i>
                        </a>';
                    }
                    
                    // Delete button - check permission and ID condition
                    if (Auth::check() && Auth::user()->can('flavor_category.delete') && $category->id > 12) {
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

        // Get category count for the specific franchise
        $totalCategories = FgpCategory::count();
        $franchiseData = null;
        
        return view('corporate_admin.fgp_category.index', [
            'totalCategories' => $totalCategories,
            'franchiseData' => $franchiseData
        ]);
    }

    // Show form to create a new category
    public function create()
    {
        
        // Check permission for creating flavor categories
        if (!Auth::check() || !Auth::user()->can('flavor_category.create')) {
            abort(403, 'Unauthorized access to create Flavor Categories');
        }

        $types = FgpCategory::select('type')->distinct()->pluck('type');
    
        return view('corporate_admin.fgp_category.create', compact('types'));
    }

    // Store the new category
    public function store(Request $request)
    {
        
        
        // Check permission for creating flavor categories
        if (!Auth::check() || !Auth::user()->can('flavor_category.create')) {
            abort(403, 'Unauthorized access to create Flavor Categories');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
        ]);

        FgpCategory::create([
            'name' => $request->name,
            'type' => $request->type, // Store as array to match model cast
          
        ]);

        return redirect()->route('franchise.fgpcategory.index')->with('success', 'Category created successfully.');
    }


    // Show edit form
    public function edit(FgpCategory $fgpcategory)
    {
        $types = FgpCategory::select('type')->distinct()->pluck('type');
    
        return view('corporate_admin.fgp_category.edit', compact('fgpcategory', 'types'));
    }


    // Update category
    public function update(Request $request, FgpCategory $fgpcategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
        ]);

        $fgpcategory->update([
            'name' => $request->name,
            'type' => $request->type, // Store as array to match model cast
        ]);

        return redirect()->route('franchise.fgpcategory.index')->with('success', 'Category updated successfully.');
    }


    // Delete category
    public function destroy(FgpCategory $fgpcategory)
    {
        try {
            $fgpcategory->delete();
            return redirect()->route('franchise.fgpcategory.index')->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('franchise.fgpcategory.index')->with('error', 'Failed to delete category.');
        }
    }
}
