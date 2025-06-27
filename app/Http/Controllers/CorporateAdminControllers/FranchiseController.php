<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Franchise;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class FranchiseController extends Controller
{
    // Show all franchises
    public function index()
    {
       
        $franchiseeId = session('franchise_id');
        $totalFranchises = Franchise::count();
        if (request()->ajax()) {
            $franchisees = Franchise::query();

            return DataTables::of($franchisees)
                ->addColumn('location_zip', function ($franchisee) {
                    $zipCodes = explode(',', $franchisee->location_zip);
                    $formattedZips = '';

                    foreach($zipCodes as $zip) {
                        if (trim($zip)) {
                            $formattedZips .= '<span class="badge bg-primary me-2 mb-1">'.trim($zip).'</span>';
                        }
                    }
                    return '<div class="d-flex flex-wrap">'.$formattedZips.'</div>';
                })
                ->filterColumn('location_zip', function ($query, $keyword) {
                    $query->where('location_zip', 'like', "%$keyword%");
                })
                ->addColumn('customer_count', function ($franchisee) {
                    $customerCount = Customer::where('franchise_id', $franchisee->id)->count();
                    $franchiseCustomerUrl = route('franchise.franchise_customer', ['franchise' => $franchisee->id]);
                    
                    return '<a href="' . $franchiseCustomerUrl . '" class="text-primary fw-bold text-decoration-none bg-color-primary">
                                <span class="badge bg-primary fs-12">' . $customerCount . ' Customers</span>
                            </a>';
                })
                ->addColumn('action', function ($franchisee) {
                    $editUrl = route('franchise.edit', $franchisee->id);
                    $deleteUrl = route('franchise.destroy', $franchisee->id);

                    $actions = '<div class="d-flex">';
                    
                    // Edit button - check permission
                    if (Auth::check() && Auth::user()->can('franchises.edit')) {
                        $actions .= '<a href="'.$editUrl.'" class="edit-franchisee">
                            <i class="ti ti-edit fs-20" style="color: #FF7B31;"></i>
                        </a>';
                    }
                    
                    // Delete button - check permission
                    if (Auth::check() && Auth::user()->can('franchises.delete')) {
                        $actions .= '<form action="'.$deleteUrl.'" method="POST">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="submit" class="ms-4 delete-franchisee">
                                <i class="ti ti-trash fs-20" style="color: #FF3131;"></i>
                            </button>
                        </form>';
                    }
                    
                    $actions .= '</div>';
                    
                    return $actions;
                })
                ->rawColumns(['action', 'location_zip', 'customer_count'])
                ->make(true);
        }

        return view('corporate_admin.franchise.index', compact('totalFranchises', 'franchiseeId'));
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
    ]);

    // Convert array of zip codes into a comma-separated string before storing
    $requestData = $request->all();
    $requestData['location_zip'] = implode(',', $request->location_zip);

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

        $franchise->update($requestData);

        return redirect()->route('franchise.index')->with('success', 'Franchise updated successfully.');
    }

    // Delete franchise
    public function destroy(Franchise $franchise)
    {
        $franchise->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('franchise.index')->with('success', 'Franchise deleted successfully.');
    }

    public function show(Franchise $franchise)
{
    // If you want ZIP codes as an array for the view:
    $franchise->location_zip = explode(',', $franchise->location_zip ?? '');

    return view('corporate_admin.franchise.view', compact('franchise'));
}
}
