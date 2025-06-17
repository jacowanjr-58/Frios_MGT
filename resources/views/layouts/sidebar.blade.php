<!--**********************************
            Sidebar start
        ***********************************-->
@php
    use Illuminate\Support\Facades\Auth;
@endphp
<div class="deznav" style="margin-top:-8px;">
    <div class="deznav-scroll">
        <ul class="metismenu" id="menu">

            <li class="{{ Request::routeIs('dashboard') ? 'mm-active' : '' }}">
                <a href="{{ route('dashboard') }}" class="{{ Request::routeIs('dashboard') ? 'active' : '' }}">
                    <i class="flaticon-025-dashboard"></i>
                    <span class="nav-text">Dashboard</span>

                </a>
            </li>
            <?php $user = \Auth::user() ?>
            @canany(['users.view', 'users.create', 'users.edit', 'users.delete'])
            
                <li class="{{ Request::routeIs('corporate_admin.users.*') ? 'mm-active' : '' }}">
                    <a class="has-arrow ai-icon {{ Request::routeIs('corporate_admin.users.*') ? 'active' : '' }}"
                        href="javascript:void()"
                        aria-expanded="{{ Request::routeIs('corporate_admin.users.*') ? 'true' : 'false' }}">
                        <i class="bi bi-person-gear"></i>
                        <span class="nav-text">Users</span>
                    </a>
                    <ul aria-expanded="{{ Request::routeIs('corporate_admin.users.*') ? 'true' : 'false' }}"
                        class="{{ Request::routeIs('corporate_admin.users.*') ? 'mm-collapse mm-show' : '' }}">
                        <li><a href="{{ route('corporate_admin.users.index') }}">Users List</a></li>
                        <li><a href="{{ route('corporate_admin.roles.index') }}">Roles & Permissions</a></li>
                    </ul>
                </li>
            @endcan
            @canany(['franchises.view', 'franchises.create', 'franchises.edit', 'franchises.delete', 'owners.view', 'owners.create', 'owners.edit', 'owners.delete'])
                <li
                    class="{{ Request::routeIs('corporate_admin.franchise.*', 'corporate_admin.owner.*') ? 'mm-active' : '' }}">
                    <a class="has-arrow ai-icon {{ Request::routeIs('corporate_admin.franchise.*', 'corporate_admin.owner.*') ? 'active' : '' }}"
                        href="javascript:void(0)"
                        aria-expanded="{{ Request::routeIs('corporate_admin.franchise.*', 'corporate_admin.owner.*') ? 'true' : 'false' }}">
                        <i class="bi bi-buildings-fill"></i>
                        <span class="nav-text">Franchises</span>
                    </a>
                    <ul aria-expanded="{{ Request::routeIs('corporate_admin.franchise.*', 'corporate_admin.owner.*') ? 'true' : 'false' }}"
                        class="{{ Request::routeIs('corporate_admin.franchise.*', 'corporate_admin.owner.*') ? 'mm-collapse mm-show' : '' }}">
                        <li><a href="{{ route('corporate_admin.franchise.index') }}">Franchise List</a></li>
                        <li><a href="{{ route('corporate_admin.owner.index') }}">Franchisee List (Owners)</a></li>
                    </ul>
                </li>
            @endcan
            @canany(['frios_flavors.view', 'frios_flavors.create', 'frios_flavors.edit', 'frios_flavors.delete', 'frios_flavors.availability', 'frios_flavors.categories'])
                <li
                    class="{{ Request::routeIs('corporate_admin.fgpitem.*', 'corporate_admin.fgpcategory.*') ? 'mm-active' : '' }}">
                    <a class="has-arrow ai-icon {{ Request::routeIs('corporate_admin.fgpitem.*', 'corporate_admin.fgpcategory.*') ? 'active' : '' }}"
                        href="javascript:void()"
                        aria-expanded="{{ Request::routeIs('corporate_admin.fgpitem.*', 'corporate_admin.fgpcategory.*') ? 'true' : 'false' }}">
                        <i class="bi bi-basket3-fill"></i>
                        <span class="nav-text">Frios Flavors</span>
                    </a>
                    <ul aria-expanded="{{ Request::routeIs('corporate_admin.fgpitem.*', 'corporate_admin.fgpcategory.*') ? 'true' : 'false' }}"
                        class="{{ Request::routeIs('corporate_admin.fgpitem.*', 'corporate_admin.fgpcategory.*') ? 'mm-collapse mm-show' : '' }}">
                        <li><a href="{{ route('corporate_admin.fgpitem.index') }}">Flavor List</a></li>
                        <li><a href="{{ route('corporate_admin.fgpitem.availability') }}">Availability</a></li>
                        <li><a href="{{ route('corporate_admin.fgpcategory.index') }}">Edit Flavor Categories</a></li>
                    </ul>
                </li>
            @endcan
            @canany(['franchise_orders.view', 'franchise_orders.create', 'franchise_orders.edit', 'franchise_orders.delete', 'franchise_orders.edit_charges'])
                <li
                    class="{{ Request::routeIs('corporate_admin.vieworders.*', 'corporate_admin.orderposps', 'corporate_admin.additionalcharges.*') ? 'mm-active' : '' }}">
                    <a class="has-arrow ai-icon {{ Request::routeIs('corporate_admin.vieworders.*', 'corporate_admin.orderposps', 'corporate_admin.additionalcharges.*') ? 'active' : '' }}"
                        href="javascript:void()"
                        aria-expanded="{{ Request::routeIs('corporate_admin.vieworders.*', 'corporate_admin.orderposps', 'corporate_admin.additionalcharges.*') ? 'true' : 'false' }}">
                        <i class="bi bi-cart-plus-fill"></i>
                        <span class="nav-text">Franchise Orders</span>
                    </a>
                    <ul aria-expanded="{{ Request::routeIs('corporate_admin.vieworders.*', 'corporate_admin.orderposps', 'corporate_admin.additionalcharges.*') ? 'true' : 'false' }}"
                        class="{{ Request::routeIs('corporate_admin.vieworders.*', 'corporate_admin.orderposps', 'corporate_admin.additionalcharges.*') ? 'mm-collapse mm-show' : '' }}">
                        <li><a href="{{ route('corporate_admin.vieworders.index') }}">View Orders</a></li>
                        <li><a href="{{ route('corporate_admin.orderposps') }}">Edit/Delete Orders</a></li>
                        <li><a href="{{ route('corporate_admin.additionalcharges.index') }}">Edit Charges</a></li>
                    </ul>
                </li>
            @endcan
            @canany(['payments.view', 'pos.expense', 'pos.order', 'pos.event'])
                <li class="{{ Request::routeIs('corporate_admin.transaction') ? 'mm-active' : '' }}">
                    <a class="has-arrow ai-icon {{ Request::routeIs('corporate_admin.transaction') ? 'active' : '' }}"
                        href="javascript:void()"
                        aria-expanded="{{ Request::routeIs('corporate_admin.transaction') ? 'true' : 'false' }}">
                        <i class="bi bi-credit-card-2-back-fill"></i>
                        <span class="nav-text">Payments</span>
                    </a>
                    <ul aria-expanded="{{ Request::routeIs('corporate_admin.transaction') ? 'true' : 'false' }}"
                        class="{{ Request::routeIs('corporate_admin.transaction') ? 'mm-collapse mm-show' : '' }}">
                        <li><a href="{{ route('corporate_admin.transaction') }}">Payments by Franchisee</a></li>
                    </ul>
                </li>
            @endcan
            @canany(['expenses.categories', 'expenses.by_franchisee'])
                <li
                    class="{{ Request::routeIs('corporate_admin.expense-category', 'corporate_admin.expense.franchisee') ? 'mm-active' : '' }}">
                    <a class="has-arrow ai-icon {{ Request::routeIs('corporate_admin.expense-category', 'corporate_admin.expense.franchisee') ? 'active' : '' }}"
                        href="javascript:void()"
                        aria-expanded="{{ Request::routeIs('corporate_admin.expense-category', 'corporate_admin.expense.franchisee') ? 'true' : 'false' }}">
                        <i class="bi bi-cup-hot-fill"></i>
                        <span class="nav-text">Expenses</span>
                    </a>
                    <ul aria-expanded="{{ Request::routeIs('corporate_admin.expense-category', 'corporate_admin.expense.franchisee') ? 'true' : 'false' }}"
                        class="{{ Request::routeIs('corporate_admin.expense-category', 'corporate_admin.expense.franchisee') ? 'mm-collapse mm-show' : '' }}">
                        <li><a href="{{ route('corporate_admin.expense-category') }}">Expenses by Category</a></li>
                        <li><a href="{{ route('corporate_admin.expense.franchisee') }}">Expenses by Franchisee</a></li>
                        {{-- <li><a href="edit_expense_categories.html">Edit Expense Categories</a></li> --}}
                    </ul>
                </li>
            @endcan
            @canany(['customers.by_franchisee'])
                <li class="{{ Request::routeIs('corporate_admin.customer') ? 'mm-active' : '' }}">
                    <a class="has-arrow ai-icon {{ Request::routeIs('corporate_admin.customer') ? 'active' : '' }}"
                        href="javascript:void()"
                        aria-expanded="{{ Request::routeIs('corporate_admin.customer') ? 'true' : 'false' }}">
                        <i class="bi bi-person-fill-add"></i>
                        <span class="nav-text">Customers</span>
                    </a>
                    <ul aria-expanded="{{ Request::routeIs('corporate_admin.customer') ? 'true' : 'false' }}"
                        class="{{ Request::routeIs('corporate_admin.customer') ? 'mm-collapse mm-show' : '' }}">
                        <li><a href="{{ route('corporate_admin.customer') }}">Customers by Franchisee</a></li>
                    </ul>
                </li>
            @endcan
            @canany(['events.view'])
                <li class="{{ Request::routeIs('corporate_admin.events.*') ? 'mm-active' : '' }}">
                    <a class="has-arrow ai-icon {{ Request::routeIs('corporate_admin.events.*') ? 'active' : '' }}"
                        href="javascript:void()"
                        aria-expanded="{{ Request::routeIs('corporate_admin.events.*') ? 'true' : 'false' }}">
                        <i class="bi bi-calendar-week-fill"></i>
                        <span class="nav-text">Events</span>
                    </a>
                    <ul aria-expanded="{{ Request::routeIs('corporate_admin.events.*') ? 'true' : 'false' }}"
                        class="{{ Request::routeIs('corporate_admin.events.*') ? 'mm-collapse mm-show' : '' }}">
                        <li><a href="{{ route('corporate_admin.events.calender') }}">Calender</a></li>
                        <li><a href="{{ route('corporate_admin.events.report') }}">Report</a></li>
                    </ul>
                </li>
            @endcan
            @canany(['inventory.view', 'inventory.create', 'inventory.edit', 'inventory.delete'])
                @if(auth()->user()->role !== 'corporate_admin' && request()->route('franchisee'))
                    <li class="{{ Request::routeIs('franchise.inventory.*', 'franchise.locations.*') ? 'mm-active' : '' }}">
                        <a class="has-arrow ai-icon {{ Request::routeIs('franchise.inventory.*', 'franchise.locations.*') ? 'active' : '' }}"
                            href="javascript:void()"
                            aria-expanded="{{ Request::routeIs('franchise.inventory.*', 'franchise.locations.*') ? 'true' : 'false' }}">
                            <i class="bi bi-shop-window"></i>
                            <span class="nav-text">Inventory</span>
                        </a>
                        <ul aria-expanded="{{ Request::routeIs('franchise.inventory.*', 'franchise.locations.*') ? 'true' : 'false' }}"
                            class="{{ Request::routeIs('franchise.inventory.*', 'franchise.locations.*') ? 'mm-collapse mm-show' : '' }}">
                            <li><a
                                    href="{{ route('franchise.inventory.index', ['franchisee' => request()->route('franchisee')]) }}">Inventory
                                    List</a></li>
                            <li><a
                                    href="{{ route('franchise.inventory.adjust.form', ['franchisee' => request()->route('franchisee')]) }}">Bulk
                                    Stock Adjust</a></li>
                            <li><a
                                    href="{{ route('franchise.inventory.bulk_price.form', ['franchisee' => request()->route('franchisee')]) }}">Bulk
                                    Prices Adjust</a></li>
                            <li><a
                                    href="{{ route('franchise.inventory.locations', ['franchisee' => request()->route('franchisee')]) }}">Allocate
                                    Inventory</a></li>
                            <li><a
                                    href="{{ route('franchise.locations.index', ['franchisee' => request()->route('franchisee')]) }}">Allocation
                                    Locations</a></li>
                        </ul>
                    </li>
                @endif
            @endcan
            @canany(['orders.view', 'orders.create', 'orders.edit', 'orders.delete'])
                @if(auth()->user()->role !== 'corporate_admin' && request()->route('franchisee'))
                    <li class="{{ Request::routeIs('franchise.orderpops.*') ? 'mm-active' : '' }}">
                        <a class="has-arrow ai-icon {{ Request::routeIs('franchise.orderpops.*') ? 'active' : '' }}"
                            href="javascript:void()"
                            aria-expanded="{{ Request::routeIs('franchise.orderpops.*') ? 'true' : 'false' }}">
                            <i class="bi bi-cart-plus-fill"></i>
                            <span class="nav-text">Orders</span>
                        </a>
                        <ul aria-expanded="{{ Request::routeIs('franchise.orderpops.*') ? 'true' : 'false' }}"
                            class="{{ Request::routeIs('franchise.orderpops.*') ? 'mm-collapse mm-show' : '' }}">
                            <li><a
                                    href="{{ route('franchise.orderpops.index', ['franchisee' => request()->route('franchisee')]) }}">Order
                                    Pops</a></li>
                            <li><a
                                    href="{{ route('franchise.orderpops.view', ['franchisee' => request()->route('franchisee')]) }}">View
                                    Orders</a></li>
                        </ul>
                    </li>
                @endif
            @endcan
            @canany(['invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete'])
                @if(auth()->user()->role !== 'corporate_admin' && request()->route('franchisee'))
                    <li class="{{ Request::routeIs('franchise.invoice.*', 'franchise.transaction') ? 'mm-active' : '' }}">
                        <a class="has-arrow ai-icon {{ Request::routeIs('franchise.invoice.*', 'franchise.transaction') ? 'active' : '' }}"
                            href="javascript:void()"
                            aria-expanded="{{ Request::routeIs('franchise.invoice.*', 'franchise.transaction') ? 'true' : 'false' }}">
                            <i class="bi bi-cash-coin"></i>
                            <span class="nav-text">Get Paid</span>
                        </a>
                        <ul aria-expanded="{{ Request::routeIs('franchise.invoice.*', 'franchise.transaction') ? 'true' : 'false' }}"
                            class="{{ Request::routeIs('franchise.invoice.*', 'franchise.transaction') ? 'mm-collapse mm-show' : '' }}">
                            {{-- <li><a href="{{ route('franchise.account.index') }}">Accounts</a></li> --}}
                            <li><a
                                    href="{{ route('franchise.invoice.index', ['franchisee' => request()->route('franchisee')]) }}">Invoices</a>
                            </li>
                            {{-- <li><a href="sales.html">Sales</a></li> --}}
                            <li><a
                                    href="{{ route('franchise.transaction', ['franchisee' => request()->route('franchisee')]) }}">Transactions</a>
                            </li>
                        </ul>
                    </li>
                @endif
            @endcan
            @canany(['expenses.view', 'expenses.create', 'expenses.edit', 'expenses.delete'])
                @if(auth()->user()->role !== 'corporate_admin' && request()->route('franchisee'))
                    <li class="{{ Request::routeIs('franchise.expense', 'franchise.expense-category') ? 'mm-active' : '' }}">
                        <a class="has-arrow ai-icon {{ Request::routeIs('franchise.expense', 'franchise.expense-category') ? 'active' : '' }}"
                            href="javascript:void()"
                            aria-expanded="{{ Request::routeIs('franchise.expense', 'franchise.expense-category') ? 'true' : 'false' }}">
                            <i class="bi bi-cup-hot-fill"></i>
                            <span class="nav-text">Expenses</span>
                        </a>
                        <ul aria-expanded="{{ Request::routeIs('franchise.expense', 'franchise.expense-category') ? 'true' : 'false' }}"
                            class="{{ Request::routeIs('franchise.expense', 'franchise.expense-category') ? 'mm-collapse mm-show' : '' }}">
                            <li><a href="{{ route('franchise.expense', ['franchisee' => request()->route('franchisee')]) }}">Expenses
                                    List</a></li>
                            <li><a
                                    href="{{ route('franchise.expense-category', ['franchisee' => request()->route('franchisee')]) }}">Expense
                                    Categories</a></li>
                        </ul>
                    </li>
                @endif
            @endcan
            @can('customers.view')
                @if(auth()->user()->role !== 'corporate_admin' && request()->route('franchisee'))
                    <li class="{{ Request::routeIs('franchise.customer') ? 'mm-active' : '' }}">
                        <a class="ai-icon {{ Request::routeIs('franchise.customer') ? 'active' : '' }}"
                            href="{{ route('franchise.customer', ['franchisee' => request()->route('franchisee')]) }}"
                            aria-expanded="false">
                            <i class="bi bi-person-fill-add"></i>
                            <span class="nav-text">Customers</span>
                        </a>
                    </li>
                @endif
            @endcan
            @can('events.view')
                @if(auth()->user()->role !== 'corporate_admin' && request()->route('franchisee'))
                    <li class="{{ Request::routeIs('franchise.events.*') ? 'mm-active' : '' }}">
                        <a class="has-arrow ai-icon {{ Request::routeIs('franchise.events.*') ? 'active' : '' }}"
                            href="javascript:void()"
                            aria-expanded="{{ Request::routeIs('franchise.events.*') ? 'true' : 'false' }}">
                            <i class="bi bi-calendar-week-fill"></i>
                            <span class="nav-text"><span>Event</span></span>
                        </a>
                        <ul aria-expanded="{{ Request::routeIs('franchise.events.*') ? 'true' : 'false' }}"
                            class="{{ Request::routeIs('franchise.events.*') ? 'mm-collapse mm-show' : '' }}">
                            <li><a
                                    href="{{ route('franchise.events.index', ['franchisee' => request()->route('franchisee')]) }}">Events
                                    List</a></li>
                            <li><a
                                    href="{{ route('franchise.events.calender', ['franchisee' => request()->route('franchisee')]) }}">Calender</a>
                            </li>
                            <li><a
                                    href="{{ route('franchise.events.report', ['franchisee' => request()->route('franchisee')]) }}">Report</a>
                            </li>
                        </ul>
                    </li>
                @endif
            @endcan
            @canany(['pos.view'])
                <li class="{{ Request::routeIs('franchise_staff.pos') ? 'mm-active' : '' }}">
                    <a class="ai-icon {{ Request::routeIs('franchise_staff.pos') ? 'active' : '' }}"
                        href="{{ route('franchise_staff.pos') }}" aria-expanded="false">
                        <i class="bi bi-cart-check-fill"></i>
                        <span class="nav-text">POS</span>
                    </a>
                </li>
            @endcan
            @canany(['flavors.view'])
                <li class="{{ Request::routeIs('franchise_staff.flavors') ? 'mm-active' : '' }}">
                    <a class="ai-icon {{ Request::routeIs('franchise_staff.flavors') ? 'active' : '' }}"
                        href="{{ route('franchise_staff.flavors') }}" aria-expanded="false">
                        <i class="bi bi-basket3-fill"></i>
                        <span class="nav-text">Flavors</span>
                    </a>
                </li>
            @endcan
            @canany(['customers.view', 'customers.create', 'customers.edit', 'customers.delete'])
                <li class="{{ Request::routeIs('franchise_staff.customer.*') ? 'mm-active' : '' }}">
                    <a class="has-arrow ai-icon {{ Request::routeIs('franchise_staff.customer.*') ? 'active' : '' }}"
                        href="javascript:void()"
                        aria-expanded="{{ Request::routeIs('franchise_staff.customer.*') ? 'true' : 'false' }}">
                        <i class="bi bi-person-fill-add"></i>
                        <span class="nav-text">Customers</span>
                    </a>
                    <ul aria-expanded="{{ Request::routeIs('franchise_staff.customer.*') ? 'true' : 'false' }}"
                        class="{{ Request::routeIs('franchise_staff.customer.*') ? 'mm-collapse mm-show' : '' }}">
                        <li><a href="{{ route('franchise_staff.customer') }}">Customers List</a></li>
                        <li><a href="{{ route('franchise_staff.customer.create') }}">Add Customer</a></li>
                    </ul>
                </li>
            @endcan
            @canany(['sales.view', 'sales.create', 'sales.edit', 'sales.delete'])
                <li class="{{ Request::routeIs('franchise_staff.sales.*') ? 'mm-active' : '' }}">
                    <a class="ai-icon {{ Request::routeIs('franchise_staff.sales.*') ? 'active' : '' }}"
                        href="{{ route('franchise_staff.sales.index') }}" aria-expanded="false">
                        <i class="bi bi-coin"></i>
                        <span class="nav-text">Sales</span>
                    </a>
                </li>
            @endcan
            @canany(['events.view', 'events.create', 'events.edit', 'events.delete'])
                <li class="{{ Request::routeIs('franchise_staff.events.*') ? 'mm-active' : '' }}">
                    <a class="has-arrow ai-icon {{ Request::routeIs('franchise_staff.events.*') ? 'active' : '' }}"
                        href="javascript:void()"
                        aria-expanded="{{ Request::routeIs('franchise_staff.events.*') ? 'true' : 'false' }}">
                        <i class="bi bi-calendar-week-fill"></i>
                        <span class="nav-text">Events</span>
                    </a>
                    <ul aria-expanded="{{ Request::routeIs('franchise_staff.events.*') ? 'true' : 'false' }}"
                        class="{{ Request::routeIs('franchise_staff.events.*') ? 'mm-collapse mm-show' : '' }}">
                        <li><a href="{{ route('franchise_staff.events.calendar') }}">Calender</a></li>
                        <li><a href="{{ route('franchise_staff.events.report') }}">Report</a></li>
                    </ul>
                </li>
            @endcan
        </ul>
        <div class="copyright">
            <p><strong>Frios Management System</strong> © <span class="current-year">2023</span> All Rights Reserved
            </p>
            <!-- <p class="fs-12">Made with <span class="heart"></span> by DexignZone</p> -->
        </div>
    </div>
</div>
<!--**********************************
            Sidebar end
        ***********************************-->