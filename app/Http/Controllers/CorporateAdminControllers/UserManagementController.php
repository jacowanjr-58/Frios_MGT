<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Franchise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class UserManagementController extends Controller
{
     /**
     * Display a listing of users
     */
    public function index()
    {
        // Only count users not assigned to any franchise
        $totalUsers = User::with('franchises')->count();
        if (request()->ajax()) {
            // Only get users who are not assigned to any franchise
            $users = User::with('roles','franchises');

            return DataTables::of($users)
                ->addColumn('role_display', function ($user) {
                    if ($user->roles->count() > 0) {
                        return $user->roles->map(function($role) {
                            return '<span class="badge bg-primary me-1">' . ucfirst(str_replace('_', ' ', $role->name)) . '</span>';
                        })->implode(' ');
                    }
                    return '<span class="text-muted">No role assigned</span>';
                })
                ->addColumn('phone_display', function ($user) {
                    return $user->phone_number ?: '<span class="text-muted">No phone</span>';
                })
                ->addColumn('formatted_date', function ($user) {
                    return $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A';
                })
                ->addColumn('action', function ($user) {
                    $editUrl = route('users.edit', $user->id);
                    $deleteUrl = route('users.destroy', $user->id);
                    $viewUrl = route('users.show', $user->id);

                    $html = '<div class="d-flex gap-1">';
                    
                    // View button - check permission
                    if (Auth::check() && Auth::user()->can('users.view')) {
                        $html .= '<a href="'.$viewUrl.'" class="btn btn-info btn-sm" title="View User"><i class="fa fa-eye"></i></a>';
                    }
                    
                    // Edit button - check permission  
                    if (Auth::check() && Auth::user()->can('users.edit')) {
                        $html .= '<a href="'.$editUrl.'" class="btn btn-primary btn-sm" title="Edit User"><i class="fa fa-edit"></i></a>';
                    }
                    
                    // Delete button - check permission
                    if (Auth::check() && Auth::user()->can('users.delete')) {
                        $html .= '<form action="'.$deleteUrl.'" method="POST" style="display: inline;" class="delete-form">';
                        $html .= csrf_field() . method_field('DELETE');
                        $html .= '<button type="submit" class="btn btn-danger btn-sm delete-user" title="Delete User"><i class="fa fa-trash"></i></button>';
                        $html .= '</form>';
                    }
                    
                    $html .= '</div>';
                    
                    return $html;
                })
                ->rawColumns(['action', 'role_display', 'phone_display'])
                ->make(true);
        }

        return view('corporate_admin.users.index', compact('totalUsers'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = Role::whereNotIn('id', [1, 3])->get();
        $franchises = Franchise::all();
       
        return view('corporate_admin.users.create', compact('roles', 'franchises'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        // Define base validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'nullable|string|regex:/^\(\d{3}\) \d{3}-\d{4}$/',
            'role' => [
                'required',
                'exists:roles,name',
                function ($attribute, $value, $fail) {
                    $role = Role::where('name', $value)->first();
                    if ($role && in_array($role->id, [1, 3])) {
                        $fail('The selected role is not allowed.');
                    }
                },
            ],
            'password' => 'required|min:8|confirmed',
        ];

        // Define base custom messages
        $messages = [
            'phone_number.regex' => 'Phone number must be in format (123) 456-7890',
            'role.required' => 'Please select a role for the user',
            'role.exists' => 'Selected role is invalid',
        ];

        // Add franchise validation only if role is not corporate_admin
        if ($request->role !== 'corporate_admin') {
            $rules['franchise_id'] = 'required|exists:franchises,franchise_id';
            $messages['franchise_id.required'] = 'Please select a franchise';
            $messages['franchise_id.exists'] = 'Selected franchise is invalid';
        }

        $request->validate($rules, $messages);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'created_date' => Carbon::now()->toDateString(),
            ]);

            // Assign role using Spatie
            $user->assignRole($request->role);

            // Attach franchise only if not corporate admin and franchise_id is provided
            if ($request->role !== 'corporate_admin' && $request->franchise_id) {
                $user->franchises()->attach($request->franchise_id);
            }

            return redirect()->route('users.index')
                ->with('success', 'User created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load('roles', 'franchises');
        return view('corporate_admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $roles = Role::whereNotIn('id', [1, 3])->get();
        $franchises = Franchise::all();
        $user->load('roles', 'franchises');
        return view('corporate_admin.users.edit', compact('user', 'roles', 'franchises'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        // Define base validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
            'phone_number' => 'nullable|string|regex:/^\(\d{3}\) \d{3}-\d{4}$/',
            'role' => [
                'required',
                'exists:roles,name',
                function ($attribute, $value, $fail) {
                    $role = Role::where('name', $value)->first();
                    if ($role && in_array($role->id, [1, 3])) {
                        $fail('The selected role is not allowed.');
                    }
                },
            ],
            'password' => 'nullable|min:8|confirmed',
        ];

        // Define base custom messages
        $messages = [
            'phone_number.regex' => 'Phone number must be in format (123) 456-7890',
            'role.required' => 'Please select a role for the user',
            'role.exists' => 'Selected role is invalid',
        ];

        // Add franchise validation only if role is not corporate_admin
        if ($request->role !== 'corporate_admin') {
            $rules['franchise_id'] = 'required|exists:franchises,franchise_id';
            $messages['franchise_id.required'] = 'Please select a franchise';
            $messages['franchise_id.exists'] = 'Selected franchise is invalid';
        }

        $request->validate($rules, $messages);

        try {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'role' => $request->role,
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // Update role using Spatie
            $user->syncRoles([$request->role]);

            // Sync franchise only if not corporate admin
            if ($request->role !== 'corporate_admin') {
                // Sync franchise (single franchise)
                if ($request->franchise_id) {
                    $user->franchises()->sync([$request->franchise_id]);
                }
            } else {
                // If changing to corporate admin, remove all franchise associations
                $user->franchises()->detach();
            }

            return redirect()->route('users.index')
                ->with('success', 'User updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        try {
            // Prevent deleting the current user
            if ($user->user_id === Auth::user()->user_id) {
                return redirect()->route('users.index')
                    ->with('error', 'You cannot delete your own account.');
            }

            $user->delete();

            return redirect()->route('users.index')
                ->with('success', 'User deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }
} 