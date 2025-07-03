<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Franchise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class FranchiseController extends Controller
{
    // Show all franchises
    public function index()
    {
        $totalFranchises = Franchise::count();

        $franchises = Franchise::query();

        if (request()->ajax()) {
            return DataTables::eloquent($franchises)
                ->filter(function ($query) {
                    // Handle global search
                    if (request()->has('search') && !empty(request('search')['value'])) {
                        $searchValue = request('search')['value'];
                        $query->where(function($q) use ($searchValue) {
                            $q->where('business_name', 'like', '%' . $searchValue . '%')
                              ->orWhere('frios_territory_name', 'like', '%' . $searchValue . '%')
                              ->orWhere('contact_number', 'like', '%' . $searchValue . '%')
                              ->orWhere('city', 'like', '%' . $searchValue . '%')
                              ->orWhere('state', 'like', '%' . $searchValue . '%')
                              ->orWhere('location_zip', 'like', '%' . $searchValue . '%')
                              ->orWhere('address1', 'like', '%' . $searchValue . '%')
                              ->orWhere('zip_code', 'like', '%' . $searchValue . '%');
                        });
                    }
                    
                    // Handle individual column searches
                    if (request()->has('columns')) {
                        $columns = request('columns');
                        foreach ($columns as $column) {
                            if (!empty($column['search']['value'])) {
                                $columnName = $column['data'];
                                $searchValue = $column['search']['value'];
                                
                                // Map display column names to database column names if needed
                                $columnMapping = [
                                    'location_zipp' => 'location_zip'
                                ];
                                
                                $dbColumnName = $columnMapping[$columnName] ?? $columnName;
                                
                                // Apply column-specific search
                                if (in_array($dbColumnName, ['business_name', 'frios_territory_name', 'contact_number', 'city', 'state', 'location_zip', 'address1', 'zip_code'])) {
                                    $query->where($dbColumnName, 'like', '%' . $searchValue . '%');
                                }
                            }
                        }
                    }
                    
                    // Handle custom filters
                    if (request()->has('state') && request('state') != '') {
                        $query->where('state', request('state'));
                    }
                    if (request()->has('location_zip') && request('location_zip') != '') {
                        $query->where('location_zip', 'like', '%' . request('location_zip') . '%');
                    }
                })
                ->addColumn('location_zipp', function ($franchise) {
                    $zipCodes = explode(',', $franchise->location_zip);
                    $formattedZips = '';

                    foreach ($zipCodes as $zip) {
                        if (trim($zip)) {
                            $formattedZips .= '<span class="badge bg-primary me-2 mb-1">' . trim($zip) . '</span>';
                        }
                    }
                    return '<div class="d-flex flex-wrap">' . $formattedZips . '</div>';
                })
                ->addColumn('action', function ($franchise) {
                    $editUrl = route('franchise.edit', $franchise->id);
                    $deleteUrl = route('franchise.destroy', $franchise->id);

                    $actions = '<div class="d-flex">';

                    // Edit button
                    if (Auth::check()) {
                        $actions .= '<a href="' . $editUrl . '" class="edit-franchisee">
                            <i class="ti ti-edit fs-20" ></i>
                        </a>';
                    }
                    

                    // Delete button
                    if (Auth::check()) {
                        $actions .= '<form action="' . $deleteUrl . '" method="POST">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="ms-4 text-danger delete-franchisee">
                                <i class="ti ti-trash fs-20"></i>
                            </button>
                        </form>';
                    }

                    $actions .= '</div>';

                    return $actions;
                })
                ->rawColumns(['action', 'location_zipp'])
                ->make(true);
        }

        return view('corporate_admin.franchise.index', compact('totalFranchises'));
    }

    // Show create form
    public function create()
    {
        return view('corporate_admin.franchise.create');
    }

    // Store franchise
    public function store(Request $request)
    {
        // Validate Input Fields
        $request->validate([
            'business_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'frios_territory_name' => 'nullable|string|max:255',
            'address1' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'zip_code' => 'required|string|regex:/^\d{5}$/',
            'state' => 'required|string|max:2',
            'location_zip' => 'required|array', // Ensure it's an array of zip codes
            'location_zip.*' => 'string|max:10', // Validate each zip code
            // 'ein_ssn' => 'required|string|max:11',
        ]);

        // Convert array of zip codes into a comma-separated string before storing
        $requestData = $request->all();
        $requestData['location_zip'] = implode(',', $request->location_zip);
        
        // Handle EIN/SSN encryption - let the model mutator handle it
        if (isset($requestData['ein_ssn'])) {
            $requestData['ein_ssn_hashed'] = $requestData['ein_ssn'];
            unset($requestData['ein_ssn']);
        }

        // Insert Data into Database
        Franchise::create($requestData);

        // Notify success message
        notify()->success('Franchise created successfully.');

        // Redirect to Index Page
        return redirect()->route('franchise.index');
    }

    // Show edit form
    public function edit(Franchise $franchise)
    {
        $franchise->location_zip = explode(',', $franchise->location_zip ?? '');
        return view('corporate_admin.franchise.edit', compact('franchise'));
    }

    // Update franchise
    public function update(Request $request, Franchise $franchise)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'frios_territory_name' => 'nullable|string|max:255',
            'address1' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'zip_code' => 'required|string|regex:/^\d{5}$/',
            'state' => 'required|string|max:255',
            'location_zip' => 'required|array',
            'location_zip.*' => 'string|max:10',
        ]);

        // Ensure unique ZIP codes before storing
        $uniqueZips = array_unique($request->location_zip);

        // Convert ZIP codes array to a comma-separated string
        $requestData = $request->all();
        $requestData['location_zip'] = implode(',', $uniqueZips);
        
        // Handle EIN/SSN encryption - let the model mutator handle it
        if (isset($requestData['ein_ssn'])) {
            $requestData['ein_ssn_hashed'] = $requestData['ein_ssn'];
            unset($requestData['ein_ssn']);
        }

        $franchise->update($requestData);

        return redirect()->route('franchise.index')->with('success', 'Franchise updated successfully.');
    }

    // Delete franchise
    public function destroy(Franchise $franchise)
    {
        // Check for related records that would prevent deletion
        $relatedData = [];
        
        // Check for orders
        if ($franchise->orders()->count() > 0) {
            $relatedData[] = $franchise->orders()->count() . ' order(s)';
        }
        
        // Check for customers
        if ($franchise->customers()->count() > 0) {
            $relatedData[] = $franchise->customers()->count() . ' customer(s)';
        }
        
        // Check for events
        if ($franchise->events()->count() > 0) {
            $relatedData[] = $franchise->events()->count() . ' event(s)';
        }
        
        // Check for sales
        if ($franchise->sales()->count() > 0) {
            $relatedData[] = $franchise->sales()->count() . ' sale(s)';
        }
        
        // Check for users (franchise staff)
        if ($franchise->users()->count() > 0) {
            $relatedData[] = $franchise->users()->count() . ' user(s)';
        }
        
        // Check for invoices
        if ($franchise->invoices()->count() > 0) {
            $relatedData[] = $franchise->invoices()->count() . ' invoice(s)';
        }
        
        // If there are related records, prevent deletion
        if (!empty($relatedData)) {
            $message = 'Cannot delete this franchise because it has associated: ' . implode(', ', $relatedData) . '. Please remove or reassign these records first.';
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => $message
                ], 422);
            }
            
            return redirect()->route('franchise.index')
                ->with('error', $message);
        }
        
        try {
            $franchise->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true]);
            }
            
            return redirect()->route('franchise.index')->with('success', 'Franchise deleted successfully.');
            
        } catch (\Exception $e) {
            $errorMessage = 'Unable to delete franchise due to database constraints. Please contact system administrator.';
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => $errorMessage
                ], 500);
            }
            
            return redirect()->route('franchise.index')->with('error', $errorMessage);
        }
    }

    public function show(Franchise $franchise)
    {
        // If you want ZIP codes as an array for the view:
        $franchise->location_zip = explode(',', $franchise->location_zip ?? '');

        return view('corporate_admin.franchise.view', compact('franchise'));
    }

    public function getFilterOptions()
    {
        try {
            $states = Franchise::pluck('state')->unique()->values();
            $locationZips = Franchise::whereNotNull('location_zip')
                ->pluck('location_zip')
                ->unique()
                ->values();

            return response()->json([
                'success' => true,
                'states' => $states,
                'locationZips' => $locationZips
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading filter options: ' . $e->getMessage()
            ], 500);
        }
    }
}
