<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SystemModulePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // System modules based on the sidebar structure
        $modules = [
            'dashboard' => [
                'view'
            ],
            'franchises' => [
                'view', 'create', 'edit', 'delete'
            ],
            'owners' => [
                'view', 'create', 'edit', 'delete'
            ],
            'frios_flavors' => [
                'view', 'create', 'edit', 'delete', 'categories'
            ],
            'franchise_orders' => [
                'view', 'create', 'edit', 'delete', 'edit_charges'
            ],
            'payments' => [
                'view', 'create', 'edit', 'delete', 'by_franchisee'
            ],
            'expenses' => [
                'view', 'create', 'edit', 'delete', 'by_category', 'by_franchisee', 'categories'
            ],
            'customers' => [
                'view', 'create', 'edit', 'delete', 'by_franchisee'
            ],
            'events' => [
                'view', 'create', 'edit', 'delete', 'calendar', 'report'
            ],
            'inventory' => [
                'view', 'create', 'edit', 'delete', 'bulk_adjust', 'bulk_price_adjust', 'allocate', 'locations'
            ],
            'orders' => [
                'view', 'create', 'edit', 'delete', 'pops'
            ],
            'invoices' => [
                'view', 'create', 'edit', 'delete'
            ],
            'transactions' => [
                'view', 'create', 'edit', 'delete'
            ],
            'pos' => [
                'view', 'create', 'edit', 'delete', 'access'
            ],
            'sales' => [
                'view', 'create', 'edit', 'delete'
            ],
            'flavors' => [
                'view'
            ],
            'staff' => [
                'view', 'create', 'edit', 'delete'
            ],
            'locations' => [
                'view', 'create', 'edit', 'delete'
            ],
            'roles' => [
                'view', 'create', 'edit', 'delete'
            ],
            'permissions' => [
                'view', 'assign'
            ]
        ];

        // Create permissions for each module
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permissionName = "{$module}.{$action}";
                if (!Permission::where('name', $permissionName)->exists()) {
                    Permission::create(['name' => $permissionName]);
                    $this->command->info("Created permission: {$permissionName}");
                }
            }
        }

        // Get roles
        $corporateAdmin = Role::where('name', 'corporate_admin')->first();
        $franchiseAdmin = Role::where('name', 'franchise_admin')->first();
        $franchiseManager = Role::where('name', 'franchise_manager')->first();
        $franchiseStaff = Role::where('name', 'franchise_staff')->first();

        if (!$corporateAdmin || !$franchiseAdmin || !$franchiseManager || !$franchiseStaff) {
            $this->command->error('Roles not found! Please run RolesAndPermissionsSeeder first.');
            return;
        }

        // Define role-specific module access
        $rolePermissions = [
            'corporate_admin' => [
                // Full access to all modules
                'dashboard', 'franchises', 'owners', 'frios_flavors', 'franchise_orders', 
                'payments', 'expenses', 'customers', 'events', 'inventory', 'orders', 
                'invoices', 'transactions', 'pos', 'sales', 'flavors', 'staff', 
                'locations', 'roles', 'permissions'
            ],
            'franchise_admin' => [
                // Limited access based on sidebar
                'dashboard', 'inventory', 'orders', 'invoices', 'transactions', 
                'expenses', 'customers', 'events', 'staff', 'locations'
            ],
            'franchise_manager' => [
                // More limited access
                'dashboard', 'inventory', 'orders', 'expenses', 'customers', 
                'events', 'staff', 'locations'
            ],
            'franchise_staff' => [
                // Most limited access
                'dashboard', 'pos', 'flavors', 'customers', 'sales', 'events'
            ]
        ];

        // Assign permissions to roles
        foreach ($rolePermissions as $roleName => $allowedModules) {
            $role = Role::where('name', $roleName)->first();
            
            foreach ($allowedModules as $module) {
                if (isset($modules[$module])) {
                    foreach ($modules[$module] as $action) {
                        $permissionName = "{$module}.{$action}";
                        $permission = Permission::where('name', $permissionName)->first();
                        
                        if ($permission && !$role->hasPermissionTo($permission)) {
                            // For corporate_admin, give all permissions
                            if ($roleName === 'corporate_admin') {
                                $role->givePermissionTo($permission);
                            }
                            // For other roles, give limited permissions based on role hierarchy
                            elseif ($roleName === 'franchise_admin') {
                                // Franchise admin gets most permissions except some corporate-only ones
                                if (!in_array($module, ['franchises', 'owners', 'frios_flavors', 'franchise_orders', 'payments', 'roles', 'permissions'])) {
                                    $role->givePermissionTo($permission);
                                }
                            }
                            elseif ($roleName === 'franchise_manager') {
                                // Franchise manager gets limited permissions (mostly edit/create but not delete)
                                if (!in_array($action, ['delete']) || in_array($module, ['staff', 'locations', 'events'])) {
                                    $role->givePermissionTo($permission);
                                }
                            }
                            elseif ($roleName === 'franchise_staff') {
                                // Franchise staff gets mostly view permissions plus specific create/edit for their modules
                                if ($action === 'view' || $action == || $action === 'access' || $action === 'calendar' || $action === 'report') {
                                    $role->givePermissionTo($permission);
                                } elseif (in_array($module, ['pos', 'sales', 'customers']) && in_array($action, ['create', 'edit'])) {
                                    $role->givePermissionTo($permission);
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->command->info('System module permissions assigned successfully!');
    }
} 