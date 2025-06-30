<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FgpCategory;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class FgpCategoryController extends Controller
{
    // Display all categories
    public function index()
    {
        if (request()->ajax()) {
            // Eager load parent and items to avoid N+1 queries
            $categories = FgpCategory::with(['parent', 'items']);

            // Count only branch
            if (request()->has('count_only')) {
                return response()->json(['count' => $categories->count()]);
            }

            return DataTables::of($categories)
                ->addColumn('flavor_items', function ($category) {
                    return $category->items->count();
                })
                ->addColumn('parent_name', function ($category) {
                    return optional($category->parent)->name;
                })
                ->addColumn('action', function ($category) {
                    $editUrl = route('franchise.fgpcategory.edit', [
                        'franchise' => $category->id,
                        'fgpcategory' => $category->id
                    ]);
                    $deleteUrl = route('franchise.fgpcategory.destroy', [
                        'franchise' => $category->id,
                        'fgpcategory' => $category->id
                    ]);

                    $actions = '<div class="d-flex align-items-center">';

                    if (Auth::user()->can('flavor_category.edit')) {
                        $actions .= '<a href="' . $editUrl . '" class="btn ' .
                            ($category->id <= 12 ? 'disabled' : '') . '">
                            <i class="ti ti-edit fs-20 text-warning"></i>
                        </a>';
                    }

                    if (Auth::user()->can('flavor_category.delete')) {
                        $actions .= '<form action="' . $deleteUrl . '" method="POST">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn ' .
                            ($category->id <= 12 ? 'disabled' : '') . '">
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
        $parents = FgpCategory::whereNull('parent_id')->get();
        return view('corporate_admin.fgp_category.create', compact('parents'));
    }

    // Store the new category
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:fgp_categories,id'
        ]);

        FgpCategory::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id
        ]);

        return redirect()->route('franchise.fgpcategory.index')->with('success', 'Category created successfully.');
    }


    // Show edit form
    public function edit(FgpCategory $fgpcategory)
    {
        if ($fgpcategory->id <= 12) {
            return redirect()->route('franchise.fgpcategory.index')->with('error', 'Unauthorized access');
        }
        $parents = FgpCategory::whereNull('parent_id')->get();
        return view('corporate_admin.fgp_category.edit', compact('fgpcategory', 'parents'));
    }


    // Update category
    public function update(Request $request, FgpCategory $fgpcategory)
    {
        if ($fgpcategory->id <= 12) {
            return redirect()->route('franchise.fgpcategory.index')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'name' => 'required|max:255',
            'parent_id' => 'nullable|exists:fgp_categories,id'
        ]);

        $fgpcategory->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id
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
