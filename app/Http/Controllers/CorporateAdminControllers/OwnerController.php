<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Franchise;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;


class OwnerController extends Controller
{
    public function index($franchise)
    {

       
        $franchiseId = intval($franchise);
        
        if (request()->ajax()) {
            // Start with base query for franchise_admin users
            $users = User::where('role', 'franchise_admin');
            // Apply franchise filter based on priority:
            // 1. Header dropdown filter (from frontend)
            // 2. URL franchise parameter
            if (request()->has('franchise_filter') && request()->franchise_filter != '') {
                // Header dropdown filter takes priority
                $users->whereHas('franchises', function($query) {
                    $query->where('franchise_id', request()->franchise_filter);
                });
            } elseif ($franchise && $franchise != '0') {
                // Use URL franchise parameter as fallback
                $users->whereHas('franchises', function($query) use ($franchise) {
                    $query->where('franchise_id', $franchise);
                });
            }
            
            // If only count is requested, return just the count
            if (request()->has('count_only')) {
                return response()->json(['count' => $users->count()]);
            }

            return DataTables::of($users)
                ->addColumn('franchise', function ($user) {
                    $franchises = $user->franchises;
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
                ->addColumn('date_joined', function ($user) {
                    return $user->date_joined ?? 'N/A';
                })
                ->addColumn('action', function ($user) use ($franchise) {
                    $editUrl = route('owner.edit', ['franchise' => $franchise, 'owner' => $user->id]);
                    $deleteUrl = route('owner.destroy', ['franchise' => $franchise, 'owner' => $user->id]);

                    $actions = '<div class="d-flex">';
                    
                    // Edit button - check permission
                    if (Auth::check() && Gate::allows('owners.edit')) {
                        $actions .= '<a href="'.$editUrl.'" class="edit-user">
                            <i class="ti ti-edit fs-20" style="color: #FF7B31;"></i>
                        </a>';
                    }
                    
                    // Delete button - check permission
                    if (Auth::check() && Gate::allows('owners.delete')) {
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
        
        // Calculate total users for non-AJAX requests
        $query = User::where('role', 'franchise_admin');
        if (request()->has('franchise_filter') && request()->franchise_filter != '') {
            // Header dropdown filter takes priority
            $query->whereHas('franchises', function($q) {
                $q->where('franchise_id', request()->franchise_filter);
            });
        } elseif ($franchise && $franchise != '0') {
            // Use URL franchise parameter as fallback
            $query->whereHas('franchises', function($q) use ($franchise) {
                $q->where('franchise_id', $franchise);
            });
        }
        $totalUsers = $query->count();
        
        return view('corporate_admin.owners.index', compact('totalUsers', 'franchiseId', 'franchise'));
    }

    // Show create form
    public function create($franchise)
    {
        $franchiseId = intval($franchise);
        $franchises = Franchise::whereDoesntHave('users')->get();
       
        return view('corporate_admin.owners.create', compact('franchises', 'franchiseId'));
    }


    public function store(Request $request, $franchise)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
            'contract_document' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB max
            'date_joined' => 'nullable|date',
            // 'clearance' => 'nullable|string',
            // 'security' => 'nullable|string',
        ], [
            'contract_document.mimes' => 'Contract document must be a PDF, DOC, or DOCX file.',
            'contract_document.max' => 'Contract document must not exceed 10MB.',
            'password.confirmed' => 'The password confirmation does not match.',
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
        ];

        $user = User::create($userData);

        // Assign the role using Spatie Role Permission
        $user->assignRole('franchise_admin');
        // Attach franchise
        $user->franchises()->attach($request->franchise); 

        return redirect()->route('owner.index', ['franchise' => $franchise])->with('success', 'Owner created successfully.');
    }

    public function edit($franchise, User $owner)
    {
        $franchise = intval($franchise);
    
        // Eager load franchises
        $owner->load('franchises');
    
        $assignedFranchiseIds = $owner->franchises->pluck('id');
    
        // Get franchises not assigned to any user
        $availableFranchises = Franchise::whereDoesntHave('users')->get();
    
        // Get franchises already assigned to this user
        $assignedFranchises = Franchise::whereIn('id', $assignedFranchiseIds)->get();
    
        // Combine and ensure uniqueness
        $franchises = $availableFranchises->merge($assignedFranchises)->unique('id');
    
        return view('corporate_admin.owners.edit', compact('owner', 'franchises', 'franchise'));
    }
    

    public function update(Request $request, $franchise, User $owner)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $owner->id . ',id',
            'password' => 'nullable|min:6|confirmed',
            'password_confirmation' => 'nullable|required_with:password',
            'contract_document' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB max
            'date_joined' => 'nullable|date',
        ], [
            'contract_document.mimes' => 'Contract document must be a PDF, DOC, or DOCX file.',
            'contract_document.max' => 'Contract document must not exceed 10MB.',
            'password.confirmed' => 'The password confirmation does not match.',
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

        $owner->update($updateData);

        if ($request->filled('password')) {
            $owner->update(['password' => bcrypt($request->password)]);
        }

        $owner->franchises()->sync($request->franchise);
        return redirect()->route('owner.index', ['franchise' => $franchise])->with('success', 'Owner updated successfully.');
    }
    public function destroy($franchise, User $owner)
    {
        try {
            
            // Delete contract document if exists
            if ($owner->contract_document_path && file_exists(public_path($owner->contract_document_path))) {
                unlink(public_path($owner->contract_document_path));
            }

            $owner->franchises()->detach($franchise);
            $owner->delete();

            return redirect()->route('owner.index', ['franchise' => $franchise])->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('owner.index', ['franchise' => $franchise])->with('error', 'Failed to delete user.');
        }
    }


}
