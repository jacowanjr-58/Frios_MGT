<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Franchisee;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class OwnerController extends Controller
{
    public function index()
    {
        $totalUsers = User::where('role', 'franchise_admin')->count();
        
        if (request()->ajax()) {
            $users = User::where('role', 'franchise_admin');
            
            return DataTables::of($users)
                ->addColumn('franchise_name', function ($user) {
                    return $user->franchisee ? $user->franchisee->business_name : 'No Franchise Assigned';
                })
                ->addColumn('formatted_role', function ($user) {
                    return ucwords(str_replace('_', ' ', $user->role));
                })
                ->addColumn('formatted_date', function ($user) {
                    return $user->created_date ? Carbon::parse($user->created_date)->format('d/m/Y') : 'N/A';
                })
                ->addColumn('action', function ($user) {
                    $editUrl = route('corporate_admin.owner.edit', $user->user_id);
                    $deleteUrl = route('corporate_admin.owner.destroy', $user->user_id);
                    
                    return '
                    <div class="d-flex">
                        <a href="'.$editUrl.'" class="edit-user">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17 3C17.2626 2.73735 17.5744 2.52901 17.9176 2.38687C18.2608 2.24473 18.6286 2.17157 19 2.17157C19.3714 2.17157 19.7392 2.24473 20.0824 2.38687C20.4256 2.52901 20.7374 2.73735 21 3C21.2626 3.26264 21.471 3.57444 21.6131 3.9176C21.7553 4.26077 21.8284 4.62856 21.8284 5C21.8284 5.37143 21.7553 5.73923 21.6131 6.08239C21.471 6.42555 21.2626 6.73735 21 7L7.5 20.5L2 22L3.5 16.5L17 3Z" stroke="#FF7B31" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                        <form action="'.$deleteUrl.'" method="POST">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="submit" class="ms-4 delete-user">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 6H5H21" stroke="#FF3131" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="#FF3131" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </form>
                    </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        return view('corporate_admin.owners.index', compact('totalUsers'));
    }

     // Show create form

     public function create()
     {
        $franchises = Franchisee::whereDoesntHave('users')->get();
        return view('corporate_admin.owners.create', compact('franchises'));
     }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'franchisee_id' => 'required|exists:franchisees,franchisee_id',
            // 'clearance' => 'nullable|string',
            // 'security' => 'nullable|string',
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
            // 'clearance' => $request->clearance,
            // 'security' => $request->security,
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
            // 'clearance' => 'nullable|string',
            // 'security' => 'nullable|string',
        ]);

        $owner->update([
            'name' => $request->name,
            'email' => $request->email,
            'franchisee_id' => $request->franchisee_id,
            // 'clearance' => $request->clearance,
            // 'security' => $request->security,
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
