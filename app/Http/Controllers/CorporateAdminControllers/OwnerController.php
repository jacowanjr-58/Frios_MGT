<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Franchisee;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OwnerController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'franchise_admin')->get();
        $totalUsers = $users->count();
        return view('corporate_admin.owners.index', compact('users', 'totalUsers'));
    }

     // Show create form

     public function create()
     {
        $franchises = Franchisee::all();
        return view('corporate_admin.owners.create', compact('franchises'));
     }
     

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'franchisee_id' => 'required|exists:franchisees,franchisee_id',
            'clearance' => 'nullable|string',
            'security' => 'nullable|string',
        ], [
            'franchisee_id.required' => 'Franchise is required.', // Custom error message
            'franchisee_id.exists' => 'Selected franchise does not exist.', // Custom error message for invalid franchise
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'franchise_admin', // Storing role in the database
            'franchisee_id' => $request->franchisee_id,
            'clearance' => $request->clearance,
            'security' => $request->security,
            'created_date' => Carbon::now()->toDateString(), // Storing the current date
        ]);
    
        // Assign the role using Spatie Role Permission
        $user->assignRole('franchise_admin');
    
        return redirect()->route('corporate_admin.owner.index')->with('success', 'Owner created successfully.');
    }
     
    public function edit(User $owner)
    {
        $franchises = Franchisee::all(); // Fetch all franchises
        return view('corporate_admin.owners.edit', compact('owner', 'franchises'));
    }
        
    public function update(Request $request, User $owner)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $owner->user_id . ',user_id', // Corrected validation
            'password' => 'nullable|min:6',
            'franchisee_id' => 'nullable|exists:franchisees,franchisee_id',
            'clearance' => 'nullable|string',
            'security' => 'nullable|string',
        ]);
    
        $owner->update([
            'name' => $request->name,
            'email' => $request->email,
            'franchisee_id' => $request->franchisee_id,
            'clearance' => $request->clearance,
            'security' => $request->security,
        ]);
    
        if ($request->filled('password')) {
            $owner->update(['password' => bcrypt($request->password)]);
        }
    
        return redirect()->route('corporate_admin.owner.index')->with('success', 'Owner updated successfully.');
    }
    public function destroy($id)
    {
        try {
            $user = User::where('user_id', $id)->firstOrFail(); // Find user by user_id
            $user->delete();

            return redirect()->route('corporate_admin.owner.index')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('corporate_admin.owner.index')->with('error', 'Failed to delete user.');
        }
    }    
    
    
}
