<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Franchisee;
use Illuminate\Http\Request;
class FranchiseController extends Controller
{
    // Show all franchises
    public function index()
    {
        $franchisees = Franchisee::all();
        $totalFranchises = Franchisee::count();
        return view('corporate_admin.franchise.index', compact('franchisees','totalFranchises'));
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
