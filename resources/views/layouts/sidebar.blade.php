<!--**********************************
            Sidebar start
        ***********************************-->
@php
    use Illuminate\Support\Facades\Auth;
    $franchiseeId = request()->route('franchise') ?? session('franchise_id');
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

            @if($user->hasRole('super_admin'))
                <li class="{{ Request::routeIs('users.*') ? 'mm-active' : '' }}">
                    <a class="has-arrow ai-icon {{ Request::routeIs('users.*') ? 'active' : '' }}" href="javascript:void()"
                        aria-expanded="{{ Request::routeIs('users.*') ? 'true' : 'false' }}">
                        <i class="bi bi-person-gear"></i>
                        <span class="nav-text">Users</span>
                    </a>
                    <ul aria-expanded="{{ Request::routeIs('users.*') ? 'true' : 'false' }}"
                        class="{{ Request::routeIs('users.*') ? 'mm-collapse mm-show' : '' }}">
                        <li><a href="{{ route('users.index') }}">Users List</a></li>
                        <li><a href="{{ route('roles.index') }}">Roles & Permissions</a></li>
                    </ul>
                </li>
            @endif
            @endcanay
            @canany(['franchises.view', 'franchises.create', 'franchises.edit', 'franchises.delete', 'owners.view', 'owners.create', 'owners.edit', 'owners.delete'])

            <li class="{{ Request::routeIs('franchise.*', 'owner.*') ? 'mm-active' : '' }}">
                <a class="has-arrow ai-icon {{ Request::routeIs('franchise.*', 'owner.*') ? 'active' : '' }}"
                    href="javascript:void(0)"
                    aria-expanded="{{ Request::routeIs('franchise.*', 'owner.*') ? 'true' : 'false' }}">
                    <i class="bi bi-buildings-fill"></i>
                    <span class="nav-text">Franchises</span>
                </a>
                <ul aria-expanded="{{ Request::routeIs('franchise.*', 'owner.*') ? 'true' : 'false' }}"
                    class="{{ Request::routeIs('franchise.*', 'owner.*') ? 'mm-collapse mm-show' : '' }}">
                    <li><a href="{{ route('franchise.index') }}">Franchise List </a></li>
                    <li><a href="{{ route('owner.index', ['franchise' => $franchiseeId]) }}">Franchise (Owners)</a></li>
                </ul>
            </li>

            @endcanany
            @canany(['frios_flavors.view', 'frios_flavors.create', 'frios_flavors.edit', 'frios_flavors.delete', 'frios_flavors.categories'])
            <li class="{{ Request::routeIs('franchise.fgpitem.*', 'franchise.fgpcategory.*') ? 'mm-active' : '' }}">
                <a class="has-arrow ai-icon {{ Request::routeIs('franchise.fgpitem.*', 'franchise.fgpcategory.*') ? 'active' : '' }}"
                    href="javascript:void()"
                    aria-expanded="{{ Request::routeIs('franchise.fgpitem.*', 'franchise.fgpcategory.*') ? 'true' : 'false' }}">
                    <i class="bi bi-basket3-fill"></i>
                    <span class="nav-text">Frios Flavors</span>
                </a>
                <ul aria-expanded="{{ Request::routeIs('franchise.fgpitem.*', 'franchise.fgpcategory.*') ? 'true' : 'false' }}"
                    class="{{ Request::routeIs('franchise.fgpitem.*', 'franchise.fgpcategory.*') ? 'mm-collapse mm-show' : '' }}">
                    <li><a
                            href="{{ route('franchise.fgpitem.index', ['franchise' => $franchiseeId]) }}">Flavor
                            List</a></li>
                    <li><a
                            href="{{ route('franchise.fgpitem.availability', ['franchise' => $franchiseeId]) }}">Availability</a>
                    </li>
                    <li><a
                            href="{{ route('franchise.fgpcategory.index', ['franchise' => $franchiseeId]) }}">Edit
                            Flavor Categories</a></li>
                </ul>
            </li>
            @endcanany
            @canany(['franchise_orders.view', 'franchise_orders.create', 'franchise_orders.edit', 'franchise_orders.delete', 'franchise_orders.edit_charges'])
            <li class="{{ Request::routeIs('vieworders.*', 'orderposps', 'additionalcharges.*') ? 'mm-active' : '' }}">
                <a class="has-arrow ai-icon {{ Request::routeIs('vieworders.*', 'orderposps', 'additionalcharges.*') ? 'active' : '' }}"
                    href="javascript:void()"
                    aria-expanded="{{ Request::routeIs('vieworders.*', 'orderposps', 'additionalcharges.*') ? 'true' : 'false' }}">
                    <i class="bi bi-cart-plus-fill"></i>
                    <span class="nav-text">Franchise Orders</span>
                </a>
                <ul aria-expanded="{{ Request::routeIs('vieworders.*', 'orderposps', 'additionalcharges.*') ? 'true' : 'false' }}"
                    class="{{ Request::routeIs('vieworders.*', 'orderposps', 'additionalcharges.*') ? 'mm-collapse mm-show' : '' }}">
                    <li><a href="{{ route('vieworders.index', ['franchise' => $franchiseeId]) }}">View Orders</a></li>
                    <!-- <li><a href="{{ route('orderposps', ['franchise' => $franchiseeId]) }}">Edit/Delete Orders</a></li> -->
                    <li><a href="{{ route('additionalcharges.index', ['franchise' => $franchiseeId]) }}">Edit Charges</a></li>
                </ul>
            </li>
            @endcanany
            @canany(['transactions.view'])
            <li class="{{ Request::routeIs('transaction', 'franchise.transaction') ? 'mm-active' : '' }}">
                <a class="has-arrow ai-icon {{ Request::routeIs('transaction', 'franchise.transaction') ? 'active' : '' }}"
                    href="javascript:void()" aria-expanded="{{ Request::routeIs('transaction', 'franchise.transaction') ? 'true' : 'false' }}">
                    <i class="bi bi-credit-card-2-back-fill"></i>
                    <span class="nav-text">Payments</span>
                </a>
                <ul aria-expanded="{{ Request::routeIs('transaction', 'franchise.transaction') ? 'true' : 'false' }}"
                    class="{{ Request::routeIs('transaction', 'franchise.transaction') ? 'mm-collapse mm-show' : '' }}">
                    <li><a href="{{ route('transaction', ['franchise' => $franchiseeId]) }}">Payments by franchise</a></li>
                </ul>
            </li>
            @endcanany
            @canany(['expenses.categories', 'expenses.by_franchisee'])
            <li class="{{ Request::routeIs('expense-category', 'expense.franchise') ? 'mm-active' : '' }}">
                <a class="has-arrow ai-icon {{ Request::routeIs('expense-category', 'expense.franchise') ? 'active' : '' }}"
                    href="javascript:void()"
                    aria-expanded="{{ Request::routeIs('expense-category', 'expense.franchise') ? 'true' : 'false' }}">
                    <i class="bi bi-cup-hot-fill"></i>
                    <span class="nav-text">Expenses</span>
                </a>
                <ul aria-expanded="{{ Request::routeIs('expense-category', 'expense.franchise') ? 'true' : 'false' }}"
                    class="{{ Request::routeIs('expense-category', 'expense.franchise') ? 'mm-collapse mm-show' : '' }}">
                    <li><a href="{{ route('expense-category', ['franchise' => $franchiseeId]) }}">Expenses by Category</a></li>
                    <li><a href="{{ route('expense.franchise', ['franchise' => $franchiseeId]) }}">Expenses by franchise</a></li>
                    {{-- <li><a href="edit_expense_categories.html">Edit Expense Categories</a></li> --}}
                </ul>
            </li>
            @endcanany

            <!-- @canany(['events.view', 'events.create', 'events.edit', 'events.delete'])
            <li class="{{ Request::routeIs('events.*') ? 'mm-active' : '' }}">
                <a class="has-arrow ai-icon {{ Request::routeIs('events.*') ? 'active' : '' }}"
                    href="javascript:void()"
                    aria-expanded="{{ Request::routeIs('events.*') ? 'true' : 'false' }}">
                    <i class="bi bi-calendar-week-fill"></i>
                    <span class="nav-text">Events</span>
                </a>
                <ul aria-expanded="{{ Request::routeIs('events.*') ? 'true' : 'false' }}"
                    class="{{ Request::routeIs('events.*') ? 'mm-collapse mm-show' : '' }}">
                    <li><a href="{{ route('events.calender') }}">Calender</a></li>
                    <li><a href="{{ route('events.report') }}">Report</a></li>
                </ul>
            </li>
            @endcanany>
            @canany(['inventory.view', 'inventory.create', 'inventory.edit', 'inventory.delete'])
            @if(auth()->user()->role !== 'corporate_admin' && $franchiseeId)
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
                                href="{{ route('franchise.inventory.index', ['franchise' => $franchiseeId]) }}">Inventory
                                List</a></li>
                        <li><a
                                href="{{ route('franchise.inventory.adjust.form', ['franchise' => $franchiseeId]) }}">Bulk
                                Stock Adjust</a></li>
                        <li><a
                                href="{{ route('franchise.inventory.bulk_price.form', ['franchise' => $franchiseeId]) }}">Bulk
                                Prices Adjust</a></li>
                        <li><a
                                href="{{ route('franchise.inventory.locations', ['franchise' => $franchiseeId]) }}">Allocate
                                Inventory</a></li>
                        <li><a
                                href="{{ route('franchise.locations.index', ['franchise' => $franchiseeId]) }}">Allocation
                                Locations</a></li>
                    </ul>
                </li>
            @endif
            @endcanany
            <!-- @canany(['orders.view', 'orders.create', 'orders.edit', 'orders.delete'])
            @if(auth()->user()->role !== 'corporate_admin' && $franchiseeId)
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
                                href="{{ route('orderposps', ['franchise' => $franchiseeId]) }}">Order
                                Pops</a></li>
                        <li><a
                                href="{{ route('franchise.orderpops.view', ['franchise' => $franchiseeId]) }}">View
                                Orders</a></li>
                                <li><a href="{{ route('vieworders.index', ['franchise' => $franchiseeId]) }}">View Orders</a></li>
                    </ul>
                </li>
            @endif
            @endcanany>
            @canany(['invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete'])
            @if(auth()->user()->role !== 'corporate_admin' && $franchiseeId)
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
                                href="{{ route('franchise.invoice.index', ['franchise' => $franchiseeId]) }}">Invoices</a>
                        </li>
                        {{-- <li><a href="sales.html">Sales</a></li> --}}
                        <li><a
                                href="{{ route('transaction', ['franchise' => $franchiseeId]) }}">Transactions</a>
                        </li>
                    </ul>
                </li>
            @endif
            @endcanany
            @canany(['expenses.view', 'expenses.create', 'expenses.edit', 'expenses.delete'])
            @if(auth()->user()->role !== 'corporate_admin' && $franchiseeId)
                <li class="{{ Request::routeIs('franchise.expense', 'franchise.expense-category') ? 'mm-active' : '' }}">
                    <a class="has-arrow ai-icon {{ Request::routeIs('franchise.expense', 'franchise.expense-category') ? 'active' : '' }}"
                        href="javascript:void()"
                        aria-expanded="{{ Request::routeIs('franchise.expense', 'franchise.expense-category') ? 'true' : 'false' }}">
                        <i class="bi bi-cup-hot-fill"></i>
                        <span class="nav-text">Expenses</span>
                    </a>
                    <ul aria-expanded="{{ Request::routeIs('expense', 'expense-category') ? 'true' : 'false' }}"
                        class="{{ Request::routeIs('expense', 'expense-category') ? 'mm-collapse mm-show' : '' }}">
                        <li><a href="{{ route('expense.franchise', ['franchise' => $franchiseeId]) }}">Expenses
                                List</a></li>
                        <li><a
                                href="{{ route('expense-category', ['franchise' => $franchiseeId]) }}">Expense
                                Categories</a></li>
                    </ul>
                </li>
            @endif
            @endcanany

            @canany(['events.view', 'events.create', 'events.edit', 'events.delete'])
            @if(auth()->user()->role !== 'corporate_admin' && $franchiseeId)
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
                                href="{{ route('franchise.events.index', ['franchise' => $franchiseeId]) }}">Events
                                List</a></li>
                        <li><a
                                href="{{ route('franchise.events.calender', ['franchise' => $franchiseeId]) }}">Calender</a>
                        </li>
                        <li><a
                                href="{{ route('franchise.events.report', ['franchise' => $franchiseeId]) }}">Report</a>
                        </li>
                    </ul>
                </li>
            @endif
            @endcanany
            @canany(['pos.view'])
            <li class="{{ Request::routeIs('franchise_staff.pos') ? 'mm-active' : '' }}">
                <a class="ai-icon {{ Request::routeIs('franchise_staff.pos') ? 'active' : '' }}"
                    href="{{ route('franchise_staff.pos', ['franchise' => $franchiseeId]) }}"
                    aria-expanded="false">
                    <i class="bi bi-cart-check-fill"></i>
                    <span class="nav-text">POS</span>
                </a>
            </li>
            @endcanany
            @can('flavors.view')
                <li class="{{ Request::routeIs('franchise.flavors') ? 'mm-active' : '' }}">
                    <a class="ai-icon {{ Request::routeIs('franchise.flavors') ? 'active' : '' }}"
                        href="{{ route('franchise.flavors', ['franchise' => $franchiseeId]) }}">
                        <i class="bi bi-basket3-fill"></i>
                        <span class="nav-text">Flavors</span>
                    </a>
                </li>
            @endcan



            @canany('customers.by_franchisee')
                <li class="{{ Request::routeIs('franchise.franchise_customer') ? 'mm-active' : '' }}">
                    <a class="has-arrow ai-icon {{ Request::routeIs('franchise.franchise_customer') ? 'active' : '' }}"
                        href="javascript:void()"
                        aria-expanded="{{ Request::routeIs('franchise.franchise_customer') ? 'true' : 'false' }}">
                        <i class="bi bi-person-fill-add"></i>
                        <span class="nav-text">Customers</span>
                    </a>
                    <ul aria-expanded="{{ Request::routeIs('franchise.franchise_customer') ? 'true' : 'false' }}"
                        class="{{ Request::routeIs('franchise.franchise_customer') ? 'mm-collapse mm-show' : '' }}">
                        <li>

                            <a
                                href="{{ route('franchise.franchise_customer', ['franchise' => $franchiseeId]) }}">Customers
                            (Franchise) </a>

                    </li>
                </ul>
            </li>
            @endcanany

            @if(!$user->hasRole('corporate_admin'))
            @canany(['customers.view', 'customers.create', 'customers.edit', 'customers.delete'])
            <li class="{{ Request::routeIs('franchise_staff.customer.*') ? 'mm-active' : '' }}">
                <a class="has-arrow ai-icon {{ Request::routeIs('franchise_staff.customer.*') ? 'active' : '' }}"
                    href="javascript:void()"
                    aria-expanded="{{ Request::routeIs(patterns: 'franchise_staff.customer.*') ? 'true' : 'false' }}">
                    <i class="bi bi-person-fill-add"></i>
                    <span class="nav-text">Customers</span>
                </a>
                <ul aria-expanded="{{ Request::routeIs('franchise_staff.customer.*') ? 'true' : 'false' }}"
                    class="{{ Request::routeIs('franchise_staff.customer.*') ? 'mm-collapse mm-show' : '' }}">
                    <li><a
                            href="{{ route('franchise_staff.customer', ['franchise' => $franchiseeId]) }}">Customers
                            List</a></li>
                    <li><a
                            href="{{ route('franchise_staff.customer.create', ['franchise' => $franchiseeId]) }}">Add
                            Customer</a></li>
                </ul>
            </li>
            @endif
            @endcanany
            @canany(['sales.view', 'sales.create', 'sales.edit', 'sales.delete'])
            <li class="{{ Request::routeIs('franchise_staff.sales.*') ? 'mm-active' : '' }}">
                <a class="ai-icon {{ Request::routeIs('franchise_staff.sales.*') ? 'active' : '' }}"
                    href="{{ route('franchise_staff.sales.index', ['franchise' => $franchiseeId]) }}"
                    aria-expanded="false">
                    <i class="bi bi-coin"></i>
                    <span class="nav-text">Sales</span>
                </a>
            </li>
            @endcanany
            <!-- @canany(['events.view', 'events.create', 'events.edit', 'events.delete'])
            <li class="{{ Request::routeIs('franchise_staff.events.*') ? 'mm-active' : '' }}">
                <a class="has-arrow ai-icon {{ Request::routeIs('franchise_staff.events.*') ? 'active' : '' }}"
                    href="javascript:void()"
                    aria-expanded="{{ Request::routeIs('franchise_staff.events.*') ? 'true' : 'false' }}">
                    <i class="bi bi-calendar-week-fill"></i>
                    <span class="nav-text">Events</span>
                </a>
                <ul aria-expanded="{{ Request::routeIs('franchise_staff.events.*') ? 'true' : 'false' }}"
                    class="{{ Request::routeIs('franchise_staff.events.*') ? 'mm-collapse mm-show' : '' }}">
                    <li><a href="{{ route('franchise_staff.events.calendar', ['franchise' => $franchiseeId]) }}">Calender</a></li>
                    <li><a
                            href="{{ route('franchise_staff.events.report', ['franchise' => $franchiseeId]) }}">Report</a>
                    </li>
                </ul>
            </li>
            @endcanany>
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
