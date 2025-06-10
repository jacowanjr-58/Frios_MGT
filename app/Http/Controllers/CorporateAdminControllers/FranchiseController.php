<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Franchisee;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FranchiseController extends Controller
{
    // Show all franchises
    public function index()
    {
        $totalFranchises = Franchisee::count();
        if (request()->ajax()) {
            $franchisees = Franchisee::query();
            
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
                ->addColumn('action', function ($franchisee) {
                    $editUrl = route('corporate_admin.franchise.edit', $franchisee->franchisee_id);
                    $deleteUrl = route('corporate_admin.franchise.destroy', $franchisee->franchisee_id);
                    
                    return '
                    <div class="d-flex">
                        <a href="'.$editUrl.'" class="edit-franchisee">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17 3C17.2626 2.73735 17.5744 2.52901 17.9176 2.38687C18.2608 2.24473 18.6286 2.17157 19 2.17157C19.3714 2.17157 19.7392 2.24473 20.0824 2.38687C20.4256 2.52901 20.7374 2.73735 21 3C21.2626 3.26264 21.471 3.57444 21.6131 3.9176C21.7553 4.26077 21.8284 4.62856 21.8284 5C21.8284 5.37143 21.7553 5.73923 21.6131 6.08239C21.471 6.42555 21.2626 6.73735 21 7L7.5 20.5L2 22L3.5 16.5L17 3Z" stroke="#FF7B31" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                        <form action="'.$deleteUrl.'" method="POST" onsubmit="return confirm(\'Are you sure you want to delete this franchisee?\')">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="submit" class="ms-4 delete-franchisee">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 6H5H21" stroke="#FF3131" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="#FF3131" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </form>
                    </div>';
                })
                ->rawColumns(['action', 'location_zip'])
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
    Franchisee::create($requestData);

    // Notify success message
    notify()->success('Franchise created successfully.');

    // Redirect to Index Page
    return redirect()->route('corporate_admin.franchise.index');
}

    
    // Show edit form
    public function edit(Franchisee $franchise)
    {
        $franchise->location_zip = explode(',', $franchise->location_zip ?? '');
        return view('corporate_admin.franchise.edit', compact('franchise'));
    }
    

    // Update franchise
    public function update(Request $request, Franchisee $franchise)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
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
    
        return redirect()->route('corporate_admin.franchise.index')->with('success', 'Franchise updated successfully.');
    }
    

    // Delete franchise
    public function destroy(Franchisee $franchise)
    {
        $franchise->delete();
        return redirect()->route('corporate_admin.franchise.index')->with('success', 'Franchise deleted successfully.');
    }
    
}
