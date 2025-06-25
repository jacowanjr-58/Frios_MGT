<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Permission Management Seeder');
        $this->command->info('=========================');

        $this->showRolePermissionsSummary();
        $this->ensureCorporateAdminHasAllPermissions();
    }

    /**
     * Show current roles and their permission counts
     */
    private function showRolePermissionsSummary(): void
    {
        $roles = Role::with('permissions')->get();
        
        $this->command->info('Current Roles and Permission Counts:');
        $this->command->info('------------------------------------');
        
        foreach ($roles as $role) {
            $this->command->info("• {$role->name}: {$role->permissions->count()} permissions");
        }
        
        $this->command->info('');
        $this->command->info('Total Permissions in System: ' . Permission::count());
        $this->command->info('');
    }

    /**
     * Ensure corporate_admin has all permissions
     */
    private function ensureCorporateAdminHasAllPermissions(): void
    {
        $corporateAdmin = Role::where('name', 'corporate_admin')->first();
        
        if (!$corporateAdmin) {
            $this->command->error('Corporate Admin role not found!');
            return;
        }

        $allPermissions = Permission::all();
        $missingPermissions = [];

        foreach ($allPermissions as $permission) {
            if (!$corporateAdmin->hasPermissionTo($permission)) {
                $corporateAdmin->givePermissionTo($permission);
                $missingPermissions[] = $permission->name;
            }
        }

        if (count($missingPermissions) > 0) {
            $this->command->info('Added missing permissions to Corporate Admin:');
            foreach ($missingPermissions as $permission) {
                $this->command->info("  • {$permission}");
            }
        } else {
            $this->command->info('Corporate Admin already has all permissions.');
        }
    }

    /**
     * Add a new module with permissions
     */
    public function addModulePermissions(string $moduleName, array $actions, array $roleAccess = []): void
    {
        $this->command->info("Adding permissions for module: {$moduleName}");
        
        // Create permissions for the module
        foreach ($actions as $action) {
            $permissionName = "{$moduleName}.{$action}";
            
            if (!Permission::where('name', $permissionName)->exists()) {
                Permission::create(['name' => $permissionName]);
                $this->command->info("  Created permission: {$permissionName}");
            }
        }

        // Assign to corporate_admin automatically
        $corporateAdmin = Role::where('name', 'corporate_admin')->first();
        if ($corporateAdmin) {
            foreach ($actions as $action) {
                $permissionName = "{$moduleName}.{$action}";
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission && !$corporateAdmin->hasPermissionTo($permission)) {
                    $corporateAdmin->givePermissionTo($permission);
                }
            }
        }

        // Assign to other roles based on roleAccess array
        foreach ($roleAccess as $roleName => $allowedActions) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                foreach ($allowedActions as $action) {
                    $permissionName = "{$moduleName}.{$action}";
                    $permission = Permission::where('name', $permissionName)->first();
                    if ($permission && !$role->hasPermissionTo($permission)) {
                        $role->givePermissionTo($permission);
                        $this->command->info("  Assigned {$permissionName} to {$roleName}");
                    }
                }
            }
        }
    }

    /**
     * Remove permissions for a module
     */
    public function removeModulePermissions(string $moduleName): void
    {
        $this->command->info("Removing permissions for module: {$moduleName}");
        
        $permissions = Permission::where('name', 'like', "{$moduleName}.%")->get();
        
        foreach ($permissions as $permission) {
            $this->command->info("  Removing permission: {$permission->name}");
            $permission->delete();
        }
    }

    /**
     * Update role permissions for a specific module
     */
    public function updateRoleModulePermissions(string $roleName, string $moduleName, array $actions): void
    {
        $role = Role::where('name', $roleName)->first();
        
        if (!$role) {
            $this->command->error("Role {$roleName} not found!");
            return;
        }

        $this->command->info("Updating {$roleName} permissions for module: {$moduleName}");

        // Remove existing permissions for this module
        $existingPermissions = $role->permissions()
            ->where('name', 'like', "{$moduleName}.%")
            ->get();
        
        foreach ($existingPermissions as $permission) {
            $role->revokePermissionTo($permission);
            $this->command->info("  Revoked: {$permission->name}");
        }

        // Add new permissions
        foreach ($actions as $action) {
            $permissionName = "{$moduleName}.{$action}";
            $permission = Permission::where('name', $permissionName)->first();
            
            if ($permission) {
                $role->givePermissionTo($permission);
                $this->command->info("  Granted: {$permissionName}");
            }
        }
    }

    /**
     * Sync permissions for a role (remove all and add specified ones)
     */
    public function syncRolePermissions(string $roleName, array $permissions): void
    {
        $role = Role::where('name', $roleName)->first();
        
        if (!$role) {
            $this->command->error("Role {$roleName} not found!");
            return;
        }

        $this->command->info("Syncing permissions for role: {$roleName}");

        // Get permission objects
        $permissionObjects = Permission::whereIn('name', $permissions)->get();
        
        // Sync permissions (this will remove all existing and add only the specified ones)
        $role->syncPermissions($permissionObjects);
        
        $this->command->info("  Synced {$permissionObjects->count()} permissions for {$roleName}");
    }
} 