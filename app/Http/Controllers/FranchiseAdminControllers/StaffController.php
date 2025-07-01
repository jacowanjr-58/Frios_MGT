<?php

namespace App\Http\Controllers\FranchiseAdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Franchise;
use Carbon\Carbon;
class StaffController extends Controller
{
    public function index($franchisee)
    {
        $franchiseeId = $franchisee;

        $users = User::
            whereIn('role', ['franchise_manager', 'franchise_staff'])
            ->get();

        $totalUsers = $users->count();

        return view('franchise_admin.staff.index', compact('users', 'totalUsers', 'franchiseeId'));
    }


    // Show create form

    public function create($franchisee)
    {
        return view('franchise_admin.staff.create', compact('franchisee'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required',
            'phone_number' => 'nullable|string|regex:/^\(\d{3}\) \d{3}-\d{4}$/',
            'security' => 'nullable|string',
        ]);

        $franchiseeId = auth()->user()->franchise_id;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role, // Storing role in the database
            'franchise_id' => $franchiseeId,
            'phone_number' => $request->phone_number,
            'security' => $request->security,
            'created_date' => Carbon::now()->toDateString(), // Storing the current date
        ]);

        // Assign the role using Spatie Role Permission
        $user->assignRole($request->role);

        // Role-based redirection
        if (auth()->user()->hasRole('franchise_admin')) {
            return redirect()->route('franchise.staff.index')->with('success', 'Staff created successfully.');
        } elseif (auth()->user()->hasRole('franchise_manager')) {
            return redirect()->route('franchise.staff.index')->with('success', 'Staff created successfully.');
        }

        // Default redirection if no role matches
        return redirect()->back()->with('success', 'Staff created successfully.');
    }


    public function edit($franchisee, User $staff)
    {
        return view('franchise_admin.staff.edit', compact('staff', 'franchisee'));
    }

    public function update(Request $request, $franchisee, User $staff)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $staff->user_id . ',user_id',
            'password' => 'nullable|min:6',
            'role' => 'required',
            'phone_number' => 'nullable|string|regex:/^\(\d{3}\) \d{3}-\d{4}$/',
            'security' => 'nullable|string',
        ]);
        $franchiseeId = auth()->user()->franchise_id;

        $staff->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role, // Storing role in the database
            'franchise_id' => $franchiseeId,
            'phone_number' => $request->phone_number,
            'security' => $request->security,
        ]);

        if ($request->filled('password')) {
            $staff->update(['password' => bcrypt($request->password)]);
        }

        // dd($request);
        // Role-based redirection
        if (auth()->user()->hasRole('franchise_admin')) {
            return redirect()->route('franchise.staff.index')->with('success', 'Staff updated successfully.');
        } elseif (auth()->user()->hasRole('franchise_manager')) {
            return redirect()->route('franchise.staff.index')->with('success', 'Staff updated successfully.');
        }

        // Default redirection if no role matches
        return redirect()->back()->with('success', 'Staff updated successfully.');
    }
    public function destroy($franchisee, $id)
    {
        try {
            $user = User::where('user_id', $id)->firstOrFail(); // Find user by user_id
            $user->delete();

            if (auth()->user()->hasRole('franchise_admin')) {
                return redirect()->route('franchise.staff.index')->with('success', 'Staff updated successfully.');
            } elseif (auth()->user()->hasRole('franchise_manager')) {
                return redirect()->route('franchise.staff.index')->with('success', 'Staff updated successfully.');
            }

            // Default redirection if no role matches
            return redirect()->back()->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            if (auth()->user()->hasRole('franchise_admin')) {
                return redirect()->route('franchise.staff.index')->with('success', 'Staff updated successfully.');
            } elseif (auth()->user()->hasRole('franchise_manager')) {
                return redirect()->route('franchise.staff.index')->with('success', 'Staff updated successfully.');
            }

            // Default redirection if no role matches
            return redirect()->back()->with('error', 'Failed to delete user.');
        }
    }


}
