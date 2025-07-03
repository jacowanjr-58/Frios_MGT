<?php

namespace App\Http\Controllers\FranchiseAdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Franchise;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function index($franchisee)
    {
        $franchiseeId = $franchisee;
        $totalUsers = User::when($franchiseeId != 'all', function ($query) use ($franchiseeId) {
                return $query->whereHas('franchises', function ($query) use ($franchiseeId) {
                    $query->where('franchises.id', $franchiseeId);
                });
            })
            ->whereIn('role', ['franchise_manager', 'franchise_staff'])
            ->count();

        $users = User::with('franchises')
            ->when($franchiseeId != 'all', function ($query) use ($franchiseeId) {
                return $query->whereHas('franchises', function ($query) use ($franchiseeId) {
                    $query->where('franchises.id', $franchiseeId);
                });
            })
            ->whereIn('role', ['franchise_manager', 'franchise_staff']);

        if (request()->ajax()) {
            return DataTables::of($users)
                ->addColumn('franchise_name', function ($user) {
                    return $user->franchises->first() ? $user->franchises->first()->business_name : 'N/A';
                })
                ->addColumn('date_joined', function ($user) {
                    return $user->date_joined ?? 'N/A';
                })
                ->addColumn('role', function ($user) {
                    return ucwords(str_replace('_', ' ', $user->role));
                })
                ->addColumn('action', function ($user) use ($franchiseeId) {
                    $editUrl = route('franchise.staff.edit', ['franchise' => $franchiseeId, 'staff' => $user->id]);
                    $deleteUrl = route('franchise.staff.destroy', ['franchise' => $franchiseeId, 'staff' => $user->id]);

                    $actions = '<div class="d-flex">';
                    
                    // Edit button
                    $actions .= '<a href="'.$editUrl.'" class="edit-staff">
                        <i class="ti ti-edit fs-20" style="color: #FF7B31;"></i>
                    </a>';
                    
                    // Delete button
                    $actions .= '<form action="'.$deleteUrl.'" method="POST">
                        '.csrf_field().'
                        '.method_field('DELETE').'
                        <button type="submit" class="ms-4 delete-staff">
                            <i class="ti ti-trash fs-20" style="color: #FF3131;"></i>
                        </button>
                    </form>';
                    
                    $actions .= '</div>';
                    
                    return $actions;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('franchise_admin.staff.index', compact('totalUsers', 'franchiseeId'));
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

        $franchiseeId = $request->franchise_id;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'phone_number' => $request->phone_number,
            'date_joined' => $request->date_joined,
        ]);

        $user->franchises()->attach($franchiseeId);
        $user->assignRole($request->role);

        // Role-based redirection
        if (Auth::user()->hasRole('franchise_admin')) {
            return redirect()->route('franchise.staff.index', ['franchisee' => $franchiseeId])->with('success', 'Staff created successfully.');
        } elseif (Auth::user()->hasRole('franchise_manager')) {
            return redirect()->route('franchise.staff.index', ['franchisee' => $franchiseeId])->with('success', 'Staff created successfully.');
        }

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

        $franchiseeId = $request->franchise_id;

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone_number' => $request->phone_number,
            'date_joined' => $request->date_joined,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }

        $staff->update($updateData);
        $staff->franchises()->sync([$franchiseeId]);

        // Role-based redirection
        if (Auth::user()->hasRole('franchise_admin')) {
            return redirect()->route('franchise.staff.index', ['franchise' => $franchiseeId])->with('success', 'Staff updated successfully.');
        } elseif (Auth::user()->hasRole('franchise_manager')) {
            return redirect()->route('franchise.staff.index', ['franchise' => $franchiseeId])->with('success', 'Staff updated successfully.');
        }

        return redirect()->back()->with('success', 'Staff updated successfully.');
    }

    public function destroy($franchisee, $id)
    {
        try {
            $user = User::find($id);
            if ($user) {
                if ($franchisee === 'all') {
                    $user->franchises()->detach();
                } else {
                    $user->franchises()->detach($franchisee);
                }
                $user->delete();
            }

            return redirect()->back()->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete user.');
        }
    }
}
