# Franchise to Franchise Refactoring Guide

## Overview
This document outlines the comprehensive refactoring from "franchisee" to "franchise" across the entire Frios_MGT application.

## Migration Files Created

### 1. `2025_12_21_000001_rename_franchisees_to_franchises.php`
- Renames `franchisees` table to `franchises`
- Updates all foreign key columns from `franchisee_id` to `franchise_id`
- Handles inconsistent naming like `franchisee_ID` in `order_invoices`
- Updates foreign key constraints to reference new table/columns

### 2. `2025_12_21_000002_rename_user_franchisees_to_user_franchises.php`
- Renames `user_franchisees` pivot table to `user_franchises`
- Updates foreign key references

## Model Changes

### Models Updated:
1. **Created: `app/Models/Franchise.php`**
   - New model replacing `Franchise`
   - Updated table name to `franchises`
   - Updated all relationship method references
   - Added comprehensive relationships

2. **Updated: `app/Models/User.php`**
   - Changed `franchisee_id` to `franchise_id` in fillable
   - Updated relationship methods: `franchisee()` → `franchise()`
   - Updated pivot table references

3. **Updated: `app/Models/Customer.php`**
   - Updated relationship: `franchisee()` → `franchise()`

4. **Updated: `app/Models/FgpOrder.php`**
   - Updated session references: `franchisee_id` → `franchise_id`
   - Updated relationship: `franchisee()` → `franchise()`

5. **Updated: `app/Models/FgpItem.php`**
   - Updated session references: `franchisee_id` → `franchise_id`
   - Updated query references in `availableQuantity()`

6. **Updated: `app/Models/FgpCategory.php`**
   - Updated session references: `franchisee_id` → `franchise_id`

7. **Updated: `app/Models/InventoryMaster.php`**
   - Updated fillable: `franchisee_id` → `franchise_id`
   - Updated relationship: `franchisee()` → `franchise()`

## Controllers to Update

### Key Controllers Requiring Updates:

1. **`app/Http/Controllers/CorporateAdminControllers/ViewOrdersController.php`**
   - Import: `Franchise` → `Franchise`
   - Method parameters: `$franchiseeId` → `$franchiseId`
   - Variable references throughout
   - Route references
   - Database query column references

2. **`app/Http/Controllers/FranchiseAdminControllers/OrderPopsController.php`**
   - Method parameters: `$franchisee` → `$franchise`
   - Variable references
   - Session references
   - Database queries

3. **`app/Http/Controllers/FranchiseStaffController/SaleController.php`**
   - Session references: `franchisee_id` → `franchise_id`
   - Database query column references

4. **All other controllers with franchise-related functionality**

## Route Files to Update

### Routes requiring parameter updates:
1. **`routes/web.php`** - Route parameters `{franchisee}` → `{franchise}`
2. **`routes/modules/corp_admin_routes.php`** - Route parameters and names
3. **`routes/modules/staff_routes.php`** - Route parameters
4. **`routes/modules/inventory_routes.php`** - Route parameters

## View Files to Update

### Key view references:
1. **Session variables**: `franchisee_id` → `franchise_id`
2. **Route parameters**: `franchisee` → `franchise`
3. **Variable names**: `$franchisee` → `$franchise`
4. **Route helper calls**: Update all route() calls with new parameter names

## Database Tables Affected

| Original Table | New Table | Column Changes |
|----------------|-----------|----------------|
| `franchisees` | `franchises` | Table rename |
| `user_franchisees` | `user_franchises` | Table rename |
| `users` | `users` | `franchisee_id` → `franchise_id` |
| `customers` | `customers` | `franchisee_id` → `franchise_id` |
| `fgp_orders` | `fgp_orders` | `franchisee_id` → `franchise_id` |
| `fgp_items` | `fgp_items` | `franchisee_id` → `franchise_id` |
| `fgp_categories` | `fgp_categories` | `franchisee_id` → `franchise_id` |
| `inventory_master` | `inventory_master` | `franchisee_id` → `franchise_id` |
| `locations` | `locations` | `franchisee_id` → `franchise_id` |
| `inventories` | `inventories` | `franchisee_id` → `franchise_id` |
| `order_invoices` | `order_invoices` | `franchisee_ID` → `franchise_ID` |
| `events` | `events` | `franchisee_id` → `franchise_id` |
| `sales` | `sales` | `franchisee_id` → `franchise_id` |
| `additional_charges` | `additional_charges` | `franchisee_id` → `franchise_id` |

## Session Variable Changes

- `session('franchisee_id')` → `session('franchise_id')`
- Update all session references in controllers and middleware

## Permission Changes

Permissions referencing "franchisee" should be updated to "franchise":
- `expenses.by_franchisee` → `expenses.by_franchise`
- `customers.by_franchisee` → `customers.by_franchise`

## Testing Checklist

### After Migration:
- [ ] Run migrations successfully
- [ ] Verify all foreign key relationships work
- [ ] Test user authentication and franchise assignment
- [ ] Test order creation and management
- [ ] Test inventory management
- [ ] Test customer management
- [ ] Test reporting functionality
- [ ] Test all CRUD operations

### Critical Areas to Test:
1. User login and franchise selection
2. Order creation process
3. Inventory tracking
4. Customer management
5. Reporting dashboards
6. Permission-based access control

## Rollback Plan

If issues arise:
1. Run migration rollbacks in reverse order
2. Restore original model files
3. Restore original controller files
4. Clear application cache

## Implementation Notes

- **Phase 1**: Run database migrations
- **Phase 2**: Update models and test basic functionality
- **Phase 3**: Update controllers systematically
- **Phase 4**: Update routes and views
- **Phase 5**: Update tests and documentation

## Post-Implementation

1. Update API documentation
2. Update user manuals
3. Clear all application caches
4. Update deployment scripts if needed
5. Notify stakeholders of changes 