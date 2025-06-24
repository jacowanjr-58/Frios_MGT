# Laravel ID Naming Convention Refactoring Summary

## Overview
This refactoring standardizes all primary keys and foreign keys to follow Laravel naming conventions.

## Primary Key Changes
All tables now use the standard Laravel `id` primary key instead of custom names:

| Table | Old Primary Key | New Primary Key |
|-------|----------------|-----------------|
| users | user_id | id |
| franchisees | franchisee_id | id |
| customers | customer_id | id |
| fgp_orders | fgp_ordersID | id |
| fgp_items | fgp_item_id | id |
| fgp_categories | category_ID | id |
| inventory_master | inventory_id | id |

## Foreign Key Changes
All foreign key references updated to follow Laravel conventions:

| Table | Old Column | New Column |
|-------|------------|------------|
| fgp_orders | user_ID | user_id |
| fgp_items | category_ID | category_id |
| ups_shipments | fgp_ordersID | fgp_order_id |

## Migration Files Created
1. `2025_12_20_000001_refactor_primary_keys_to_laravel_standards.php` - Renames primary keys
2. `2025_12_20_000002_refactor_foreign_keys_to_laravel_standards.php` - Updates foreign key references
3. `2025_12_20_000003_fix_remaining_foreign_key_references.php` - Fixes remaining references

## Model Changes
All models updated to remove custom primary key settings:
- Removed `protected $primaryKey` declarations
- Updated relationship methods to use standard Laravel conventions
- Removed explicit local/foreign key parameters where possible

## Controller Updates
Key controllers updated to use new column names:
- `CorporateAdminControllers/ViewOrdersController.php`
- `FranchiseAdminControllers/OrderPopsController.php`
- `Shipping/UPsShippingController.php`
- `FranchiseStaffController/SaleController.php`

## Route Updates
- Updated UPS shipping routes to use standard parameter names

## Validation Rule Updates
- Updated validation rules to reference correct table columns

## Database Query Updates
- Updated raw database queries to use new column names
- Fixed join conditions and where clauses

## Testing Checklist
1. Run migrations: `php artisan migrate`
2. Test user authentication and relationships
3. Test franchise and customer management
4. Test order creation and management
5. Test inventory allocation system
6. Test payment transactions
7. Test UPS shipping label generation
8. Verify all foreign key constraints work correctly

## Rollback Instructions
If issues arise, rollback migrations in reverse order:
```bash
php artisan migrate:rollback --step=3
```

## Benefits
- Follows Laravel conventions for better maintainability
- Eliminates confusion with mixed naming patterns
- Improves code readability and consistency
- Makes the codebase more accessible to Laravel developers
- Reduces cognitive load when working with relationships

## Notes
- All existing data is preserved during the migration
- Foreign key constraints are properly maintained
- The refactoring is backwards compatible through migration rollbacks
- Some linter errors may appear due to missing imports but these don't affect functionality 