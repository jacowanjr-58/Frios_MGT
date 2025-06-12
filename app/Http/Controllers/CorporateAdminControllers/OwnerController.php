<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Franchisee;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class OwnerController extends Controller
{
    public function index()
    {
        $totalUsers = User::where('role', 'franchise_admin')->count();

        if (request()->ajax()) {
            $users = User::where('role', 'franchise_admin');

            return DataTables::of($users)
                ->addColumn('franchisee', function ($user) {
                    $franchises = $user->franchisees;
                    if ($franchises->isEmpty()) {
                        return '<span class="badge bg-danger">No Franchise Assigned</span>';
                    }
                    
                    return $franchises->map(function($franchise) {
                        return '<span class="badge bg-primary me-1">' . $franchise->business_name . '</span>';
                    })->implode(' ');
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
                            <i class="ti ti-edit fs-20" style="color: #FF7B31;"></i>
                        </a>
                        <form action="'.$deleteUrl.'" method="POST">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="submit" class="ms-4 delete-user">
                                <i class="ti ti-trash fs-20" style="color: #FF3131;"></i>
                            </button>
                        </form>
                    </div>';
                })
                ->rawColumns(['action', 'franchisee'])
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
            // 'franchisee_id' => $request->franchisee_id,
            // 'clearance' => $request->clearance,
            // 'security' => $request->security,
            'created_date' => Carbon::now()->toDateString(), // Storing the current date
        ]);

        // Assign the role using Spatie Role Permission
        $user->assignRole('franchise_admin');
        // Attach franchisee
        $user->franchisees()->attach($request->franchisee_id);

        return redirect()->route('corporate_admin.owner.index')->with('success', 'Owner created successfully.');
    }

    public function edit(User $owner)
    {
        // Get the franchisees assigned to this user
        $assignedFranchiseIds = $owner->franchisees->pluck('franchisee_id');
    
        // Franchises not assigned to any user
        $availableFranchises = Franchisee::whereDoesntHave('users')->get();
    
        // Franchises assigned to this user (should still appear in the dropdown)
        $assignedFranchises = Franchisee::whereIn('franchisee_id', $assignedFranchiseIds)->get();
    
        // Merge both collections and remove duplicates
        $franchises = $availableFranchises->merge($assignedFranchises)->unique('franchisee_id');
    
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
            // 'franchisee_id' => $request->franchisee_id,
            // 'clearance' => $request->clearance,
            // 'security' => $request->security,
        ]);

        if ($request->filled('password')) {
            $owner->update(['password' => bcrypt($request->password)]);
        }

        $owner->franchisees()->sync($request->franchisee_id);
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
