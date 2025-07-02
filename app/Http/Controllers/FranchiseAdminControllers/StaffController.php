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
            when($franchiseeId != 'all', function ($query) use ($franchiseeId) {
                return $query->whereHas('franchises', function ($query) use ($franchiseeId) {
                    $query->where('franchises.id', $franchiseeId);
                });
            })
            ->whereIn('role', ['franchise_manager', 'franchise_staff'])
            ->get();

        $totalUsers = $users->count();

        return view('franchise_admin.staff.index', compact('users', 'totalUsers', 'franchiseeId'));
    }


    // Show create form

    public function create($franchisee)
    {
        $franchises = Franchise::all();
        return view('franchise_admin.staff.create', compact('franchisee', 'franchises'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
            'role' => 'required',
            'phone_number' => 'nullable|string|regex:/^\(\d{3}\) \d{3}-\d{4}$/',
            'date_joined' => 'nullable|date',
            'franchise_id' => 'required|exists:franchises,id',
        ]);

        // dd($request->all());

        $franchiseeId = $request->franchise_id;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role, // Storing role in the database
            'phone_number' => $request->phone_number,
            'date_joined' => $request->date_joined, // Storing the current date
        ]);

        $user->franchises()->attach($franchiseeId);

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
        $franchises = Franchise::all();
        $selectedFranchiseId = $staff->franchises->first()->id;
        return view('franchise_admin.staff.edit', compact('staff', 'franchisee', 'franchises', 'selectedFranchiseId'));
    }

    public function update(Request $request, $franchisee, User $staff)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $staff->id . ',id',
            'password' => 'nullable|min:6|confirmed',
            'password_confirmation' => 'nullable|required_with:password',
            'role' => 'required',
            'phone_number' => 'nullable|string|regex:/^\(\d{3}\) \d{3}-\d{4}$/',
            'date_joined' => 'nullable|date',
            'franchise_id' => 'required|exists:franchises,id',
        ]);

        // dd($request->all());
        $franchiseeId = $request->franchise_id;

        $staff->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role, // Storing role in the database
            'phone_number' => $request->phone_number,
            'date_joined' => $request->date_joined,
        ]);

        $staff->franchises()->sync([$franchiseeId]);

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
            $user = User::find($id); // Find user by user_id
            if ($user) {
                $user->franchises()->detach($franchisee);
                $user->delete();
            }

            return redirect()->back()->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete user.');
        }
    }


}
