# 🔐 Roles & Permissions Management System - Demo Guide

## 📋 **Overview**

Your Frios Management System now includes a comprehensive **Roles & Permissions Management** interface that is **strictly restricted to corporate_admin users only**. This system allows you to:

- ✅ View all system roles and their permissions
- ✅ Create new custom roles with specific permissions
- ✅ Edit existing roles and modify their permissions
- ✅ Delete custom roles (system roles are protected)
- ✅ Assign granular permissions based on system modules

---

## 🚀 **How to Access**

### **Login as Corporate Admin**
1. **Email**: `corporateadmin@friospops.com` or `abdulsamadalvi73@gmail.com`
2. **Password**: `password` or `AbdulSamadPassword`

### **Navigate to Roles Management**
1. After login, look at the **sidebar menu**
2. Find **"Roles & Permissions"** section (with shield icon 🛡️)
3. Click to expand and see:
   - **"Manage Roles"** - View all roles
   - **"Create Role"** - Create new roles

---

## 📊 **Features Overview**

### **1. Roles List Page** (`/corporate_admin/roles`)
- **Table View**: Shows ID, Name, Permissions, and Actions
- **Permission Badges**: First 5 permissions displayed with "+X more" indicator
- **Search & Filter**: Built-in search functionality
- **Actions**: Edit and Delete buttons for each role
- **Permission Modal**: Click eye icon to view all permissions for a role

### **2. Create Role Page** (`/corporate_admin/roles/create`)
- **Role Name Field**: Required field for role name
- **"Give All Permissions" Toggle**: Quick way to assign all permissions
- **Module-based Permissions**: Organized by system modules:
  - Dashboard
  - Franchises Management
  - Frios Flavors
  - Inventory Management
  - POS System
  - Sales Management
  - And many more...
- **Toggle Switches**: Modern switch design for each permission
- **Module Select All**: Each module has a "select all" checkbox

### **3. Edit Role Page** (`/corporate_admin/roles/{role}/edit`)
- **Pre-filled Data**: Role name and existing permissions loaded
- **Same Interface**: Identical to create page but with existing selections
- **Update Functionality**: Modify role name and permissions

---

## 🔧 **System Modules & Permissions**

### **Corporate Admin Exclusive Modules**
- **Franchises Management**: Create, edit, delete franchises
- **Franchise Owners**: Manage franchise owners/admins
- **Frios Flavors**: Manage system-wide flavor catalog
- **Franchise Orders**: View and manage all franchise orders
- **Payments**: Corporate-level payment management
- **Role Management**: Create and assign permissions (THIS FEATURE!)

### **Franchise Admin Modules**
- **Inventory Management**: Full inventory control
- **Orders**: Franchise-level order management
- **Invoices & Transactions**: Financial management
- **Staff Management**: Hire and manage franchise staff
- **Events**: Franchise event management

### **Franchise Manager Modules**
- **Limited Inventory**: View and basic inventory management
- **Orders**: Order processing and management
- **Staff**: Limited staff management
- **Locations**: Manage franchise locations

### **Franchise Staff Modules**
- **POS System**: Point of sale operations
- **Sales**: Sales reporting and management
- **Customer Management**: Basic customer operations
- **Flavors**: View available flavors

---

## 🛡️ **Security Features**

### **Access Restriction**
- ✅ **ONLY** `corporate_admin` role can access this section
- ✅ All other roles (`franchise_admin`, `franchise_manager`, `franchise_staff`) are **completely blocked**
- ✅ Middleware protection at controller level
- ✅ Sidebar menu only shows for corporate admins

### **System Role Protection**
- ✅ Core system roles **cannot be deleted**:
  - `corporate_admin`
  - `franchise_admin` 
  - `franchise_manager`
  - `franchise_staff`
- ✅ These roles show "disabled" delete button with tooltip
- ✅ Custom roles can be safely deleted

### **Permission Hierarchy**
- ✅ **Corporate Admin**: Always gets ALL permissions (automatic)
- ✅ **Franchise Roles**: Limited to their respective module permissions
- ✅ **New Roles**: Can be assigned custom permission combinations

---

## 📝 **Usage Examples**

### **Example 1: Creating a "Regional Manager" Role**
1. Go to **Create Role** page
2. Enter name: `regional_manager`
3. Select permissions:
   - Dashboard: View
   - Franchises: View, List
   - Franchise Orders: View, List
   - Expenses: View, By Category, By Franchisee
   - Events: View, Calendar, Report
4. Click **"Create Role"**

### **Example 2: Creating a "Customer Service" Role**
1. Go to **Create Role** page
2. Enter name: `customer_service`
3. Select permissions:
   - Dashboard: View
   - Customers: View, Create, Edit, List
   - Events: View, Calendar
   - Orders: View, List
4. Click **"Create Role"**

### **Example 3: Editing Franchise Staff Permissions**
1. Go to **Manage Roles**
2. Click **Edit** on "Franchise Staff" role
3. Add/remove permissions as needed
4. Click **"Update Role"**

---

## 🎯 **Testing Instructions**

### **Test Access Control**
1. **Login as Corporate Admin** ✅
   - Can see "Roles & Permissions" in sidebar
   - Can access all role management pages
   
2. **Login as Franchise Admin** ❌
   - Should NOT see "Roles & Permissions" in sidebar
   - Direct URL access should redirect/block

3. **Login as Other Roles** ❌
   - All other roles should be completely blocked

### **Test Functionality**
1. **Create Custom Role**
   - Create role with specific permissions
   - Verify role appears in list
   - Check permission count is correct

2. **Edit Role**
   - Modify existing role permissions
   - Verify changes are saved
   - Check permission badges update

3. **Delete Custom Role**
   - Delete a custom role (not system role)
   - Verify it's removed from list
   - Confirm system roles can't be deleted

---

## 📊 **Database Impact**

### **Tables Used**
- `roles` - Stores role information
- `permissions` - Stores all system permissions
- `role_has_permissions` - Links roles to permissions
- `model_has_roles` - Links users to roles

### **Seeded Data**
- **103 permissions** created across all system modules
- **4 system roles** with appropriate permission assignments
- **Corporate Admin**: Gets all 103 permissions automatically

---

## 🚨 **Important Notes**

1. **Corporate Admin Supremacy**: Corporate admin ALWAYS gets all permissions, even new ones added later
2. **System Role Protection**: Core roles cannot be deleted to maintain system integrity  
3. **Permission Inheritance**: New permissions are automatically given to corporate admin
4. **Middleware Protection**: Multiple layers of security prevent unauthorized access
5. **Database Relationships**: Proper foreign key relationships maintain data integrity

---

## 🎉 **Success Indicators**

When everything is working correctly, you should see:

✅ **Sidebar Menu**: "Roles & Permissions" visible for corporate admin only  
✅ **Role List**: Table showing all 4 system roles + any custom roles  
✅ **Permission Count**: Corporate Admin shows 103+ permissions  
✅ **Create/Edit Forms**: Modern interface with organized permission modules  
✅ **Access Control**: Other roles completely blocked from accessing  
✅ **System Protection**: Core roles cannot be deleted  

---

**🎊 Your Roles & Permissions Management System is now fully operational and secure! 🎊** 