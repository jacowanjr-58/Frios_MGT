# Roles and Permissions Seeders

This document explains the role and permission system seeders for the Frios Management System.

## Overview

The system includes comprehensive role-based access control (RBAC) using Laravel Spatie Permission package with four main roles:

- **Corporate Admin** - Full system access with exception of Franchise proprietary data manipulation (customer list, events, inventory, etc.)
- **Franchise Admin** - Franchise-level management access  (ability for multi-Franchise management)
- **Franchise Manager** - Franchise operations and management access exclusive of bank setup, payment setup, critical delete
- **Franchise Staff** - Basic Franchise operational access (POS, Sales, etc.), View Event, Pops, Inventory

## Seeders Structure

### 1. RolesAndPermissionsSeeder.php
**Primary seeder that sets up the complete role and permission system**

**Features:**
- Truncates existing roles and permissions tables
- Creates the 4 main roles
- Creates all system permissions based on modules
- Assigns appropriate permissions to each role
- Ensures corporate_admin gets ALL permissions
- Provides permission assignment logic for other roles

**Usage:**
```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### 2. SystemModulePermissionsSeeder.php
**Modular seeder for managing individual system modules**

**Features:**
- Creates permissions for specific system modules
- Flexible permission assignment based on role hierarchy
- Can be run independently to add new modules
- Prevents duplicate permission creation

**Usage:**
```bash
php artisan db:seed --class=SystemModulePermissionsSeeder
```

### 3. PermissionManagementSeeder.php
**Helper seeder for managing and maintaining permissions**

**Features:**
- Shows current roles and permission counts
- Ensures corporate_admin always has all permissions
- Provides utility methods for adding/removing module permissions
- Can sync permissions for specific roles

**Usage:**
```bash
php artisan db:seed --class=PermissionManagementSeeder
```

## System Modules and Permissions

### Corporate Admin Modules
- **Franchises**: `franchises.view`, `franchises.create`, `franchises.edit`, `franchises.delete`
- **Owners** (Franchise Admin): `owners.view`, `owners.create`, `owners.edit`, `owners.delete`
- **Frios Flavors**: `frios_flavors.view`, `frios_flavors.create`, `frios_flavors.edit`, `frios_flavors.delete`, `frios_flavors.categories`, `frios_flavors.bulk_availability`
- **Franchise Orders** (all): `franchise_orders.view`, `franchise_orders.create`, `franchise_orders.edit`, `franchise_orders.delete`,  `franchise_orders.edit_charges`, 'franchise_orders.collect_payment', 'franchise_orders.view_discrepancy'
- **Payments** (for Orders): `payments.view`, `payments.create`, `payments.edit`, `payments.delete`, `payments.by_franchisee`
- **Role Management**: `roles.view`, `roles.create`, `roles.edit`, `roles.delete`, `permissions.assign`, `permissions.view`
- **ACH/BANK**: 'ach_franchise.view_connect',
- **STRIPE**: 'stripe_franchise.view_connect',  'stripe_corporate.connect', 'stripe_corporate.disconnect',
- **Events**: 'events.view', 'events.create', 'events.edit', 'events.delete', 'events.viewAll' (can see all Franchise events)
- **Sales**(total POS/Invoice): `sales.view`, 'sales.by_franchise', 'sales.aggregate'
- **Expenses**: `expenses.view`,  `expenses.by_category`, `expenses.by_franchisee`, `expenses.categories`, 'expenses.categories.create', 'expenses.categories.edit' 

### Franchise Admin Modules
- **Franchises**: `franchises.view`
- **Owners**: 'owners.view'
- **ACH/BANK**: 'ach_franchise.connect', 'ach_franchise.disconnect'
- **STRIPE**: 'stripe_franchise.view_connect',  'stripe_franchise.connect', 'stripe_franchise.disconnect',
- **Frios Flavors**: `frios_flavors.view` (view availability)
- **Inventory**: `inventory.view`, `inventory.create`, `inventory.edit`, `inventory.delete`, `inventory.bulk_adjust`, `inventory.bulk_price_adjust`, `inventory.allocate`, `inventory.locations`
- **Franchise Orders** (franchise): `franchise_orders.view`, `franchise_orders.create`, `franchise_orders.edit`, `franchise_orders.delete`, 'franchise_orders.view_discrepancy', 'franchise_orders.add_discrepancy'
<!-- - **Orders** (franchise): `orders.view`, `orders.create`, `orders.edit`, `orders.delete`,  `orders.pops` -->
- **Invoices**: `invoices.view`, `invoices.create`, `invoices.edit`, `invoices.delete`
- **Transactions** (payments?): `transactions.view`, `transactions.create`, `transactions.edit`, `transactions.delete`
- **Staff Management**(Managers & Staff): `staff.view`, `staff.create`, `staff.edit`, `staff.delete`
- **Role Management**(franchise): `permissions.assign`, `permissions.view`
- **Events**(franchise): 'events.view', 'events.create', 'events.edit', 'events.delete'
- **Customers**: `customers.view`, `customers.create`, `customers.edit`, 'customers.delete'
- **POS**: `pos.view`, `pos.create`, `pos.edit`, `pos.delete`, `pos.access`
- **Sales**: `sales.view`, `sales.create`, `sales.edit`, `sales.delete`
- **Expenses** (franchise): `expenses.view`, `expenses.create`, `expenses.edit`, `expenses.delete`, 

### Franchise Manager Modules
- **Franchises**: `franchises.view`
- **Owners**: 'owners.view'
- **Frios Flavors**: `frios_flavors.view` (view availability)
- **Inventory**: `inventory.view`, `inventory.create`, `inventory.edit`, `inventory.delete`, `inventory.bulk_adjust`, `inventory.bulk_price_adjust`, `inventory.allocate`, `inventory.locations`
- **Franchise Orders** (franchise): `franchise_orders.view`, `franchise_orders.create`, `franchise_orders.edit`, `franchise_orders.delete`, 'franchise_orders.view_discrepancy', 'franchise_orders.add_discrepancy'
<!-- - **Orders**: `orders.view`, `orders.create`, `orders.edit`, `orders.delete`,  `orders.pops` -->
- **Invoices**: `invoices.view`, `invoices.create`, `invoices.edit`, `invoices.delete`
- **Transactions** (payments?): `transactions.view`, `transactions.create`, `transactions.edit`, `transactions.delete`
- **Staff Management**(Managers & Staff): `staff.view`, `staff.create`, `staff.edit`, `staff.delete`
- **Role Management**(franchise staff): `permissions.assign`, `permissions.view`
- **Events**(franchise): 'events.view', 'events.create', 'events.edit', 'events.delete'
- **Customers**: `customers.view`, `customers.create`, `customers.edit`, 'customers.delete'
- **POS**: `pos.view`, `pos.create`, `pos.edit`, `pos.delete`, `pos.access`
- **Sales**: `sales.view`, `sales.create`, `sales.edit`, `sales.delete`
- **Expenses** (franchise): `expenses.view`, `expenses.create`, `expenses.edit`, `expenses.delete`, 

### Franchise Staff Modules
- **POS**: `pos.view`, `pos.create`, `pos.edit`, `pos.delete`, `pos.access`
- **Sales**: `sales.view`, `sales.create`, `sales.edit`, `sales.delete`
- **Flavors**: `flavors.view`
- **Customers**: `customers.view`, `customers.create`, `customers.edit`
- **Frios Flavors**: `frios_flavors.view`
- **Inventory**: `inventory.view`,
- **Events**(franchise): 'events.view',

<!-- ### Shared Modules (All Roles) 
- **Dashboard**: `dashboard.view`
- **Customers**: `customers.view`, `customers.create`, `customers.edit`, `customers.delete`,  `customers.by_franchisee`
- **Events**: `events.view`, `events.create`, `events.edit`, `events.delete`, `events.calendar`, `events.report`
- **Expenses**: `expenses.view`, `expenses.create`, `expenses.edit`, `expenses.delete`, `expenses.by_category`, `expenses.by_franchisee`, `expenses.categories` -->

## Permission Assignment Logic

### Corporate Admin
- **Full Access**: Gets ALL permissions in the system
- **Permission Management**: Can assign/revoke permissions to other roles
- **System-wide Access**: Can access all franchises and corporate-level features

### Franchise Admin
- **Franchise Management**: Full access to franchise-level operations
- **Staff Management**: Can create, edit, and delete franchise managers and staff
- **Permission Management**: Can assign/revoke permissions to managers/staff
- **Financial Access**: Can manage invoices, transactions, and expenses
- **No Corporate Access**: Cannot access franchise creation, owner management, or system-wide settings

### Franchise Manager
- **Operational Management**: Can manage day-to-day operations
- **Staff Access**: Can manage franchise staff but with restrictions
- **Financial Access**: Can access invoices or transactions; Cannot setup/delete bank or payments services
- **Location Management**: Can manage franchise locations and inventory allocation

### Franchise Staff
- **Point of Sale**: Full access to POS system for sales
- **Customer Service**: Can view and create customer records
- **Sales Reporting**: Can view sales data and reports
- **Limited Access**: Cannot access management features or sensitive data

## Running the Seeders

### Fresh Installation
```bash
# Run all seeders (includes roles and permissions)
php artisan db:seed

# Or run specific seeder
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### Adding New Modules
```bash
# Run system module seeder to add new permissions
php artisan db:seed --class=SystemModulePermissionsSeeder
```

### Maintenance and Updates
```bash
# Run permission management seeder to ensure consistency
php artisan db:seed --class=PermissionManagementSeeder
```

## Customization

### Adding New Roles
To add new roles, modify the `RolesAndPermissionsSeeder.php`:

```php
// Create new role
$newRole = Role::create(['name' => 'new_role_name']);

// Define permissions for the new role
$newRolePermissions = [
    'dashboard.view',
    'module.view',
    // ... more permissions
];

// Assign permissions
$newRole->givePermissionTo($newRolePermissions);
```

### Adding New Modules
To add new modules, modify the `SystemModulePermissionsSeeder.php`:

```php
'new_module' => [
    'view', 'create', 'edit', 'delete', 'list', 'custom_action'
],
```

### Updating Role Permissions
Use the `PermissionManagementSeeder` utility methods:

```php
// Add new module permissions
$this->addModulePermissions('new_module', ['view', 'create'], [
    'franchise_admin' => ['view', 'create'],
    'franchise_manager' => ['view']
]);

// Update role permissions for specific module
$this->updateRoleModulePermissions('franchise_staff', 'customers', ['view', 'create']);
```

## Security Considerations

1. **Corporate Admin**: Always has ALL permissions - this is by design for system administration
2. **Permission Hierarchy**: Lower roles cannot access higher-level features
3. **Franchise Isolation**: Franchise users can only access their assigned franchise data
4. **Permission Caching**: System uses permission caching for performance - cache is cleared during seeding

## Troubleshooting

### Common Issues

1. **Permission Cache**: If permissions don't seem to update, clear the cache:
   ```bash
   php artisan permission:cache-reset
   ```

2. **Foreign Key Constraints**: If seeding fails due to foreign keys:
   ```bash
   php artisan db:seed --force
   ```

3. **Duplicate Permissions**: The seeders check for existing permissions to prevent duplicates

4. **Role Not Found**: Ensure roles are created before assigning permissions

### Verification Commands

Check current permissions:
```bash
# In tinker
php artisan tinker
>>> Role::with('permissions')->get()->each(function($role) { echo "{$role->name}: {$role->permissions->count()} permissions\n"; });
```

Check specific user permissions:
```bash
# In tinker
>>> $user = User::find(1);
>>> $user->getAllPermissions()->pluck('name');
``` 
