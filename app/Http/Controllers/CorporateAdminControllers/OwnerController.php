<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Franchise;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;


class OwnerController extends Controller
{
    public function index($franchise)
    {
        $franchiseeId = $franchise;
        $totalUsers = User::where('role', 'franchise_admin')->count();

        if (request()->ajax()) {
            $users = User::where('role', 'franchise_admin');

            return DataTables::of($users)
                ->addColumn('franchise', function ($user) {
                    $franchises = $user->franchisees;
                    if ($franchises->isEmpty()) {
                        return '<span class="text-muted">No Franchise Assigned</span>';
                    }
                    
                    $franchiseNames = $franchises->map(function($franchise) {
                        return $franchise->business_name ?? 'N/A';
                    })->toArray();
                    
                    return implode(', ', $franchiseNames);
                })
                ->addColumn('formatted_role', function ($user) {
                    return ucwords(str_replace('_', ' ', $user->role));
                })
                ->addColumn('formatted_date', function ($user) {
                    return $user->created_date ? Carbon::parse($user->created_date)->format('d/m/Y') : 'N/A';
                })
                ->addColumn('action', function ($user) {
                    $editUrl = route('owner.edit', $user->user_id);
                    $deleteUrl = route('owner.destroy', $user->user_id);

                    $actions = '<div class="d-flex">';
                    
                    // Edit button - check permission
                    if (Auth::check() && Auth::user()->can('owners.edit')) {
                        $actions .= '<a href="'.$editUrl.'" class="edit-user">
                            <i class="ti ti-edit fs-20" style="color: #FF7B31;"></i>
                        </a>';
                    }
                    
                    // Delete button - check permission
                    if (Auth::check() && Auth::user()->can('owners.delete')) {
                        $actions .= '<form action="'.$deleteUrl.'" method="POST">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="submit" class="ms-4 delete-user">
                                <i class="ti ti-trash fs-20" style="color: #FF3131;"></i>
                            </button>
                        </form>';
                    }
                    
                    $actions .= '</div>';
                    
                    return $actions;
                })
                ->rawColumns(['action', 'franchise'])
                ->make(true);
        }

        return view('corporate_admin.owners.index', compact('totalUsers', 'franchiseeId'));
    }

    // Show create form

    public function create()
    {
       
        $franchises = Franchise::whereDoesntHave('users')->get();

        return view('corporate_admin.owners.create', compact('franchises'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'franchise_id' => 'required|exists:franchisees,franchise_id',
            'ein_ssn' => 'nullable|string|max:255',
            'contract_document' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB max
            'date_joined' => 'nullable|date',
            // 'clearance' => 'nullable|string',
            // 'security' => 'nullable|string',
        ], [
            'franchise_id.required' => 'Franchise is required.', // Custom error message
            'franchise_id.exists' => 'Selected franchise does not exist.', // Custom error message for invalid franchise
            'contract_document.mimes' => 'Contract document must be a PDF, DOC, or DOCX file.',
            'contract_document.max' => 'Contract document must not exceed 10MB.',
        ]);

        // Handle file upload
        $contractDocumentPath = null;
        if ($request->hasFile('contract_document')) {
            $file = $request->file('contract_document');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Store file directly in public/contracts directory
            $file->move(public_path('contracts'), $filename);
            $contractDocumentPath = 'contracts/' . $filename;
        }

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'franchise_admin', // Storing role in the database
            'contract_document_path' => $contractDocumentPath,
            'date_joined' => $request->date_joined,
            'created_date' => Carbon::now()->toDateString(), // Storing the current date
        ];

        // Handle EIN/SSN hashing if provided
        if ($request->filled('ein_ssn')) {
            $userData['ein_ssn_hash'] = encrypt($request->ein_ssn);
        }

        $user = User::create($userData);

        // Assign the role using Spatie Role Permission
        $user->assignRole('franchise_admin');
        // Attach franchise
        $user->franchisees()->attach($request->franchise_id);

        return redirect()->route('owner.index')->with('success', 'Owner created successfully.');
    }

    public function edit(User $owner)
    {
        // Get the franchisees assigned to this user
        $assignedFranchiseIds = $owner->franchisees->pluck('franchise_id');
    
        // Franchises not assigned to any user
        $availableFranchises = Franchise::whereDoesntHave('users')->get();
    
        // Franchises assigned to this user (should still appear in the dropdown)
        $assignedFranchises = Franchise::whereIn('franchise_id', $assignedFranchiseIds)->get();
    
        // Merge both collections and remove duplicates
        $franchises = $availableFranchises->merge($assignedFranchises)->unique('franchise_id');
    
        return view('corporate_admin.owners.edit', compact('owner', 'franchises'));
    }
    

    public function update(Request $request, User $owner)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $owner->user_id . ',user_id', // Corrected validation
            'password' => 'nullable|min:6',
            'franchise_id' => 'nullable|exists:franchisees,franchise_id',
            'ein_ssn' => 'nullable|string|max:255',
            'contract_document' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB max
            'date_joined' => 'nullable|date',
            // 'clearance' => 'nullable|string',
            // 'security' => 'nullable|string',
        ], [
            'contract_document.mimes' => 'Contract document must be a PDF, DOC, or DOCX file.',
            'contract_document.max' => 'Contract document must not exceed 10MB.',
        ]);

        // Handle file upload
        $contractDocumentPath = $owner->contract_document_path; // Keep existing path if no new file
        if ($request->hasFile('contract_document')) {
            // Delete old file if exists
            if ($owner->contract_document_path && file_exists(public_path($owner->contract_document_path))) {
                unlink(public_path($owner->contract_document_path));
            }
            
            $file = $request->file('contract_document');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Store file directly in public/contracts directory
            $file->move(public_path('contracts'), $filename);
            $contractDocumentPath = 'contracts/' . $filename;
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'contract_document_path' => $contractDocumentPath,
            'date_joined' => $request->date_joined,
        ];

        // Handle EIN/SSN hashing if provided
        if ($request->filled('ein_ssn')) {
            $updateData['ein_ssn_hash'] = encrypt($request->ein_ssn);
        }

        $owner->update($updateData);

        if ($request->filled('password')) {
            $owner->update(['password' => bcrypt($request->password)]);
        }

        $owner->franchisees()->sync($request->franchise_id);
        return redirect()->route('owner.index')->with('success', 'Owner updated successfully.');
    }
    public function destroy($id)
    {
        try {
            $user = User::where('user_id', $id)->firstOrFail(); // Find user by user_id
            
            // Delete contract document if exists
            if ($user->contract_document_path && file_exists(public_path($user->contract_document_path))) {
                unlink(public_path($user->contract_document_path));
            }
            
            $user->delete();

            return redirect()->route('owner.index')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('owner.index')->with('error', 'Failed to delete user.');
        }
    }


}
