<?php

namespace App\Http\Controllers\FranchiseAdminControllers;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminProfileController extends Controller
{
    public function index( $franchisee=null )
    {
        $franchisee = request()->route('franchisee');
        $user = Auth::user(); // Get logged-in admin's profile
       
        return view('franchise_admin.profile.index', compact('user', 'franchisee'));
    }

    public function edit($franchisee = null, $user = null)
    {
        // If called from general route (corporate admin), franchisee will be the user ID
        if ($user === null) {
            $user = $franchisee ?: Auth::user()->user_id;
            $franchisee = null;
        } else {
            $franchisee = request()->route('franchisee');
        }
       
        $user = Auth::user(); // Fetch the authenticated user
        return view('franchise_admin.profile.edit', compact('user', 'franchisee'));
    }


public function update(Request $request, $franchisee = null, $profile = null)
{
    // If called from general route (corporate admin), franchisee will be the user ID
    if ($profile === null) {
        $profile = $franchisee ?: Auth::user()->user_id;
        $franchisee = null;
    }

    $user = User::where('user_id', $profile)->firstOrFail();

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
        'old_password' => 'nullable|required_with:password', // Old password required if updating password
        'password' => 'nullable|min:8|confirmed',
        'phone_number' => 'nullable|string|regex:/^\(\d{3}\) \d{3}-\d{4}$/',
    ]);

    // If user wants to update password, verify old password first
    if ($request->filled('password')) {
        if (!Hash::check($request->old_password, $user->password)) {
            return redirect()->back()->withErrors(['old_password' => 'The old password is incorrect.']);
        }

        // Update password
        $user->update(['password' => bcrypt($request->password)]);
    }

    // Update other user details
    $user->update([
        'name' => $request->name,
        'email' => $request->email,
        'phone_number' => $request->phone_number,
    ]);

    if ($franchisee) {
        return redirect()->route('franchise.profile.index', $franchisee)->with('success', 'Profile updated successfully.');
    } else {
        return redirect()->route('profile.index')->with('success', 'Profile updated successfully.');
    }
}

public function show($franchisee = null, $profile = null)
{
    // If called from general route (corporate admin), franchisee will be the user ID
    if ($profile === null) {
        $profile = $franchisee ?: Auth::user()->user_id;
        $franchisee = null;
    }

    $user = User::where('user_id', $profile)->firstOrFail(); // Get the user by profile ID
    return view('franchise_admin.profile.show', compact('user', 'franchisee'));
}

}
