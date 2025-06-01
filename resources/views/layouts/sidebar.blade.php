<!--**********************************
            Sidebar start
        ***********************************-->
<div class="deznav" style="margin-top:-8px;">
    <div class="deznav-scroll">
        <ul class="metismenu" id="menu">

            <li>
                <a href="{{ route('dashboard') }}">
                    <i class="flaticon-025-dashboard"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            @role('corporate_admin')
                {{-- <li><a class="ai-icon" href="javascript:void()" aria-expanded="false">
					<i class="bi bi-house-door-fill"></i>
					<span class="nav-text">Return to Main</span>
				</a></li> --}}
                <li>
                    <a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                        <i class="bi bi-buildings-fill"></i>
                        <span class="nav-text">Franchises</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('corporate_admin.franchise.index') }}">Franchise List</a></li>
                        <li><a href="{{ route('corporate_admin.owner.index') }}">Franchisee List (Owners)</a></li>
                    </ul>
                </li>


                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="bi bi-basket3-fill"></i>
                        <span class="nav-text">Frios Flavors</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('corporate_admin.fgpitem.index') }}">Flavor List</a></li>
                        <li><a href="{{ route('corporate_admin.fgpitem.availability') }}">Availability</a></li>
                        <li><a href="{{ route('corporate_admin.fgpcategory.index') }}">Edit Flavor Categories</a></li>
                    </ul>
                </li>
                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="bi bi-cart-plus-fill"></i>
                        <span class="nav-text">Franchise Orders</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('corporate_admin.vieworders.index') }}">View Orders</a></li>
                        <li><a href="{{ route('corporate_admin.orderposps') }}">Edit/Delete Orders</a></li>
                        <li><a href="{{ route('corporate_admin.additionalcharges.index') }}">Edit Charges</a></li>
                    </ul>
                </li>
                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="bi bi-credit-card-2-back-fill"></i>
                        <span class="nav-text">Payments</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('corporate_admin.transaction') }}">Payments by Franchisee</a></li>
                    </ul>
                </li>
                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="bi bi-cup-hot-fill"></i>
                        <span class="nav-text">Expenses</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('corporate_admin.expense-category') }}">Expenses by Category</a></li>
                        <li><a href="{{ route('corporate_admin.expense.franchisee') }}">Expenses by Franchisee</a></li>
                        <li><a href="edit_expense_categories.html">Edit Expense Categories</a></li>
                    </ul>
                </li>
                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="bi bi-person-fill-add"></i>
                        <span class="nav-text">Customers</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('corporate_admin.customer') }}">Customers by Franchisee</a></li>
                    </ul>
                </li>
                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="bi bi-calendar-week-fill"></i>
                        <span class="nav-text">Events</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('corporate_admin.events.calender') }}">Calender</a></li>
                        <li><a href="{{ route('corporate_admin.events.report') }}">Report</a></li>
                    </ul>
                </li>
            @endrole
            @role('franchise_admin')
                {{-- <li><a class="ai-icon" href="javascript:void()" aria-expanded="false">
					<i class="bi bi-house-door-fill"></i>
					<span class="nav-text">Return to Main</span>
				</a></li> --}}
                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="bi bi-shop-window"></i>
                        <span class="nav-text">Inventory</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('franchise.locations.index') }}">Location</a></li>
                        <li><a href="{{ route('franchise.inventory.index') }}">Inventory List</a></li>
                        <li><a href="{{ route('franchise.inventory.locations') }}">Edit Inventory Locations</a></li>
                    </ul>
                </li>
                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="bi bi-cart-plus-fill"></i>
                        <span class="nav-text">Orders</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('franchise.orderpops.create') }}">Order Pops</a></li>
                        <li><a href="{{ route('franchise.orderpops.view') }}">View Orders</a></li>
                    </ul>
                </li>
                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="bi bi-cash-coin"></i>
                        <span class="nav-text">Get Paid</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('franchise.account.index') }}">Accounts</a></li>
                        <li><a href="{{ route('franchise.invoice.index') }}">Invoices</a></li>
                        <li><a href="sales.html">Sales</a></li>
                        <li><a href="{{ route('franchise.transaction') }}">Transactions</a></li>
                    </ul>
                </li>
                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="bi bi-cup-hot-fill"></i>
                        <span class="nav-text">Expenses</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('franchise.expense') }}">Expenses List</a></li>
                        <li><a href="expense_categories.html">Expense Categories</a></li>
                    </ul>
                </li>
                <li><a class="ai-icon" href="{{ route('franchise.customer') }}" aria-expanded="false">
                        <i class="bi bi-person-fill-add"></i>
                        <span class="nav-text">Customers</span>
                    </a></li>
                    <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
						<!-- <i class="flaticon-033-feather"></i> -->
						<i class="bi bi-calendar-week-fill"></i>
						<span class="nav-text"><span>Event</span></span>
					</a>
					<ul aria-expanded="false">
							<li><a href="{{ route('franchise.events.index') }}">Events List</a></li>
							<li><a href="{{ route('franchise.events.calender') }}">Calender</a></li>
							<li><a href="{{ route('franchise.events.report') }}">Report</a></li>
						</ul>
					</li>
                {{-- <li><a class="ai-icon" href="{{ route('franchise.events.index') }}" aria-expanded="false">
                        <i class="bi bi-calendar-week-fill"></i>
                        <span class="nav-text">Events</span>
                    </a></li> --}}
            @endrole
            @role('franchise_manager')
                {{-- <li><a class="ai-icon" href="javascript:void()" aria-expanded="false">
					<i class="bi bi-house-door-fill"></i>
					<span class="nav-text">Return to Main</span>
				</a></li> --}}
                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="bi bi-shop-window"></i>
                    <span class="nav-text">Inventory</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('franchise.locations.index') }}">Location</a></li>
                    <li><a href="{{ route('franchise.inventory.index') }}">Inventory List</a></li>
                    <li><a href="{{ route('franchise.inventory.locations') }}">Edit Inventory Locations</a></li>
                </ul>
            </li>
            <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="bi bi-cart-plus-fill"></i>
                    <span class="nav-text">Orders</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('franchise.orderpops.index') }}">Order Pops</a></li>
                    <li><a href="{{ route('franchise.orderpops.view') }}">View Orders</a></li>
                </ul>
            </li>
                {{-- <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
					<i class="bi bi-cash-coin"></i>
					<span class="nav-text">Get Paid</span>
				</a>
				<ul aria-expanded="false">
					<li><a href="invoices.html">Invoices</a></li>
					<li><a href="sales.html">Sales</a></li>
					<li><a href="payments.html">Payments</a></li>
				</ul>
			</li> --}}
                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="bi bi-cup-hot-fill"></i>
                        <span class="nav-text">Expenses</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('franchise.expense') }}">Expenses List</a></li>
                        <li><a href="expense_categories.html">Expense Categories</a></li>
                    </ul>
                </li>
                <li><a class="ai-icon" href="{{ route('franchise.customer') }}" aria-expanded="false">
                        <i class="bi bi-person-fill-add"></i>
                        <span class="nav-text">Customers</span>
                    </a></li>
                    <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
						<!-- <i class="flaticon-033-feather"></i> -->
						<i class="bi bi-calendar-week-fill"></i>
						<span class="nav-text"><span>Event</span></span>
					</a>
					<ul aria-expanded="false">
							<li><a href="{{ route('franchise.events.index') }}">Events List</a></li>
							<li><a href="{{ route('franchise.events.calender') }}">Calender</a></li>
							<li><a href="{{ route('franchise.events.report') }}">Report</a></li>
						</ul>
					</li>
            @endrole
            @role('franchise_staff')
                <li><a class="ai-icon" href="pos.html" aria-expanded="false">
                        <i class="bi bi-cart-check-fill"></i>
                        <span class="nav-text">POS</span>
                    </a></li>
                <li><a class="ai-icon" href="{{ route('franchise_staff.flavors') }}" aria-expanded="false">
                        <i class="bi bi-basket3-fill"></i>
                        <span class="nav-text">Flavors</span>
                    </a></li>
                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="bi bi-person-fill-add"></i>
                        <span class="nav-text">Customers</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('franchise_staff.customer') }}">Customers List</a></li>
                        <li><a href="{{ route('franchise_staff.customer.create') }}">Add Customer</a></li>
                    </ul>
                </li>
                <li><a class="ai-icon" href="sales.html" aria-expanded="false">
                        <i class="bi bi-coin"></i>
                        <span class="nav-text">Sales</span>
                    </a></li>
                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="bi bi-calendar-week-fill"></i>
                        <span class="nav-text">Events</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('franchise_staff.events.calendar') }}">Calender</a></li>
                        <li><a href="{{ route('franchise_staff.events.report') }}">Report</a></li>
                    </ul>
                </li>
            @endrole
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
