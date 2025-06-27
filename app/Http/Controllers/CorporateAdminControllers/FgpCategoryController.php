<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FgpCategory;
use Yajra\DataTables\Facades\DataTables;

class FgpCategoryController extends Controller
{
    // Display all categories
    public function index()
    {
        if (request()->ajax()) {
            // Start with categories query
            $categories = FgpCategory::query();

            // If only count is requested, return just the count
            if (request()->has('count_only')) {
                return response()->json(['count' => $categories->count()]);
            }

            return DataTables::of($categories)
                ->addColumn('flavor_items', function ($category) {
                    return $category->items->count();
                })
                ->addColumn('action', function ($category) {
                    $editUrl = route('franchise.fgpcategory.edit', ['franchise' => $category->id, 'fgpcategory' => $category->id]);
                    $deleteUrl = route('franchise.fgpcategory.destroy', ['franchise' => $category->id, 'fgpcategory' => $category->id]);

                    $actions = '<div class="d-flex align-items-center">';

                    // Edit button - check permission
                    if (auth()->user()->can('flavor_category.edit')) {
                        $actions .= '<a href="' . $editUrl . '" class="btn ' . ($category->id <= 12 ? 'disabled' : '') . '">
                                <i class="ti ti-edit fs-20 text-warning"></i>
                            </a>';
                    }

                    // Delete button - check permission and ID condition
                    if (auth()->user()->can('flavor_category.delete')) {
                        $actions .= '<form action="' . $deleteUrl . '" method="POST">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn ' . ($category->id <= 12 ? 'disabled' : '') . '">
                                <i class="ti ti-trash fs-20 text-danger"></i>
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
        $types = FgpCategory::select('type')->distinct()->pluck('type');

        return view('corporate_admin.fgp_category.create', compact('types'));
    }

    // Store the new category
    public function store(Request $request)
    {
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
        if ($fgpcategory->id <= 12) {
            return redirect()->route('franchise.fgpcategory.index')->with('error', 'Unauthorized access to edit Flavor Categories');
        }

        $types = FgpCategory::select('type')->distinct()->pluck('type');

        return view('corporate_admin.fgp_category.edit', compact('fgpcategory', 'types'));
    }


    // Update category
    public function update(Request $request, FgpCategory $fgpcategory)
    {
        if ($fgpcategory->id <= 12) {
            return redirect()->route('franchise.fgpcategory.index')->with('error', 'Unauthorized access to edit Flavor Categories');
        }

        $request->validate([
            'name' => 'required|max:255',
            'type' => 'required|max:255',
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
        if ($fgpcategory->id <= 12) {
            return redirect()->route('franchise.fgpcategory.index')->with('error', 'Unauthorized access to delete Flavor Categories');
        }

        try {
            $fgpcategory->delete();
            return redirect()->route('franchise.fgpcategory.index')->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('franchise.fgpcategory.index')->with('error', 'Failed to delete category.');
        }
    }
}
