<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class RolePermissionController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(['auth', 'role:super_admin']);
    // }

    /**
     * Display a listing of roles and their permissions
     */
    public function index()
    {
        $totalRoles = Role::count();

        if (request()->ajax()) {
            $roles = Role::with('permissions');

            return DataTables::of($roles)
                ->addColumn('formatted_name', function ($role) {
                    return ucfirst(str_replace('_', ' ', $role->name));
                })
                ->addColumn('permissions_display', function ($role) {
                    if($role->permissions->count() > 0) {
                        $displayCount = 5;
                        $permissions = $role->permissions->take($displayCount);
                        $remainingCount = $role->permissions->count() - $displayCount;
                        
                        $html = '<div class="permissions-container">';
                        foreach($permissions as $permission) {
                            $html .= '<span class="badge bg-primary me-1 mb-1">' . $permission->name . '</span>';
                        }
                        
                        if($remainingCount > 0) {
                            $html .= '<span class="badge bg-secondary me-1 mb-1">+' . $remainingCount . ' more</span>';
                        }
                        
                        if($role->permissions->count() > $displayCount) {
                            $html .= '<button class="btn btn-link btn-sm p-0 ms-2" onclick="showAllPermissions(' . $role->id . ')" title="View all permissions"><i class="fa fa-eye"></i></button>';
                        }
                        
                        $html .= '</div>';
                        return $html;
                    } else {
                        return '<span class="text-muted">No permissions assigned</span>';
                    }
                })
                ->addColumn('action', function ($role) {
                    $editUrl = route('roles.edit', $role);
                    $deleteUrl = route('roles.destroy', $role);
                    
                    $protectedRoles = ['corporate_admin', 'franchise_admin', 'franchise_manager', 'franchise_staff','super_admin'];
                    $isProtected = in_array($role->name, $protectedRoles);

                    $html = '<div class="d-flex gap-1">';
                    
                    // Edit button - check permission
                    if (Auth::check() && Auth::user()->can('roles.edit')) {
                        $html .= '<a href="'.$editUrl.'" class="btn btn-primary btn-sm" title="Edit Role"><i class="fa fa-edit"></i></a>';
                    }
                    
                    // Delete button - check permission and role protection
                    if (Auth::check() && Auth::user()->can('roles.delete')) {
                        $html .= '<form action="'.$deleteUrl.'" method="POST" style="display: inline;" class="delete-form">';
                        $html .= csrf_field() . method_field('DELETE');
                        $html .= '<button type="submit" class="btn btn-danger btn-sm delete-role" title="Delete Role"><i class="fa fa-trash"></i></button>';
                        $html .= '</form>';
                    } elseif ($isProtected) {
                        $html .= '<button class="btn btn-secondary btn-sm" title="System role cannot be deleted" disabled><i class="fa fa-trash"></i></button>';
                    }
                    
                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['action', 'permissions_display'])
                ->make(true);
        }

        return view('corporate_admin.roles.index', compact('totalRoles'));
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        $permissions = $this->getGroupedPermissions();
        return view('corporate_admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
       

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'give_all_permissions' => 'nullable|boolean'
        ]);

        DB::beginTransaction();
        try {
            // Create the role
            $role = Role::create(['name' => $request->name]);

            // Check if "Give All Permissions" is checked first
            if ($request->has('give_all_permissions') && $request->give_all_permissions) {
                // Assign all permissions
                $role->givePermissionTo(Permission::all());
            } elseif ($request->has('permissions') && is_array($request->permissions) && !empty($request->permissions)) {
                // Assign only the selected permissions
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->givePermissionTo($permissions);
            }
            // If neither condition is met, the role is created with no permissions

            DB::commit();

            $message = $request->has('give_all_permissions') && $request->give_all_permissions 
                ? 'Role created successfully with all permissions assigned.'
                : 'Role created successfully with selected permissions assigned.';

            return redirect()->route('roles.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Error creating role: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing a role
     */
    public function edit(Role $role)
    {
        $permissions = $this->getGroupedPermissions();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('corporate_admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role)
    {
       
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'give_all_permissions' => 'nullable|boolean'
        ]);

        DB::beginTransaction();
        try {
            // Update role name
            $role->update(['name' => $request->name]);

            // Sync permissions based on user selection
            if ($request->has('give_all_permissions') && $request->give_all_permissions) {
                // Assign all permissions
                $role->syncPermissions(Permission::all());
            } elseif ($request->has('permissions') && is_array($request->permissions) && !empty($request->permissions)) {
                // Assign only the selected permissions
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            } else {
                // Remove all permissions if none selected
                $role->syncPermissions([]);
            }

            DB::commit();

            $message = $request->has('give_all_permissions') && $request->give_all_permissions 
                ? 'Role updated successfully with all permissions assigned.'
                : 'Role updated successfully with selected permissions assigned.';

            return redirect()->route('roles.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Error updating role: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified role
     */
    public function destroy(Role $role)
    {
        // Prevent deletion of core system roles
        $protectedRoles = ['corporate_admin', 'franchise_admin', 'franchise_manager', 'franchise_staff'];
        
        if (in_array($role->name, $protectedRoles)) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete system role: ' . $role->name);
        }

        try {
            $role->delete();
            return redirect()->route('roles.index')
                ->with('success', 'Role deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('roles.index')
                ->with('error', 'Error deleting role: ' . $e->getMessage());
        }
    }

    /**
     * Get role permissions for modal display
     */
    public function getPermissions(Role $role)
    {
        return response()->json([
            'role' => $role->name,
            'permissions' => $role->permissions->pluck('name')->toArray()
        ]);
    }

    /**
     * Get permissions grouped by module
     */
    private function getGroupedPermissions()
    {
        $permissions = Permission::all();
        $grouped = [];

        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);
            $module = $parts[0];
            $action = $parts[1] ?? '';

            // Convert module names to display names
            $displayName = $this->getModuleDisplayName($module);
            
            if (!isset($grouped[$displayName])) {
                $grouped[$displayName] = [];
            }
            
            $grouped[$displayName][] = [
                'id' => $permission->id,
                'name' => $permission->name,
                'action' => $action,
                'display_name' => $this->getActionDisplayName($action)
            ];
        }

        // Sort modules and permissions
        ksort($grouped);
        foreach ($grouped as $module => $perms) {
            usort($grouped[$module], function($a, $b) {
                $order = ['view', 'list', 'create', 'edit', 'delete'];
                $aIndex = array_search($a['action'], $order);
                $bIndex = array_search($b['action'], $order);
                if ($aIndex === false) $aIndex = 999;
                if ($bIndex === false) $bIndex = 999;
                return $aIndex <=> $bIndex;
            });
        }

        return $grouped;
    }

    /**
     * Get display name for module
     */
    private function getModuleDisplayName($module)
    {
        $displayNames = [
            'dashboard' => 'Dashboard',
            'franchises' => 'Franchises',
            'owners' => 'Franchise Owners',
            'frios_flavors' => 'Frios Flavors',
            'frios_availability' => 'Frios Availability',
            'flavor_category' => 'Flavor Categories',
            'franchise_orders' => 'Franchise Orders',
            'additional_charges' => 'Additional Charges',
            'payments' => 'Payments',
            'expenses' => 'Expenses',
            'expense_categories' => 'Expense Categories',
            'customers' => 'Customers',
            'events' => 'Events',
            'inventory' => 'Inventory',
            'orders' => 'Orders',
            'invoices' => 'Invoices',
            'transactions' => 'Transactions',
            'pos' => 'POS System',
            'sales' => 'Sales',
            'flavors' => 'Flavors',
            'staff' => 'Staff Management',
            'locations' => 'Locations',
            'accounts' => 'Accounts',
            'roles' => 'Role Management',
            'permissions' => 'Permission Management',
            'users' => 'User Management'
        ];

        return $displayNames[$module] ?? ucfirst(str_replace('_', ' ', $module));
    }

    /**
     * Get display name for action
     */
    private function getActionDisplayName($action)
    {
        $displayNames = [
            'view' => 'View',
            'list' => 'List',
            'create' => 'Create',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'access' => 'Access',
            'assign' => 'Assign',
            'availability' => 'Availability',
            'categories' => 'Categories',
            'by_franchisee' => 'By Franchisee',
            'by_category' => 'By Category',
            'calendar' => 'Calendar',
            'report' => 'Report',
            'bulk_adjust' => 'Bulk Adjust',
            'bulk_price_adjust' => 'Bulk Price Adjust',
            'allocate' => 'Allocate',
            'locations' => 'Locations',
            'pops' => 'Pops',
            'edit_charges' => 'Edit Charges'
        ];

        return $displayNames[$action] ?? ucfirst(str_replace('_', ' ', $action));
    }

    /**
     * Get role permission summary for AJAX
     */
    public function getPermissionSummary(Role $role)
    {
        return response()->json([
            'role' => $role->name,
            'permissions' => $role->permissions->pluck('name')->toArray()
        ]);
    }
} 