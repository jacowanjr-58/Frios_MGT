@extends('layouts.app')

@section('content')
    <x-notify::notify />

    <!--********************************* Content body start ***********************************-->

    <div class=" content-body default-height">
        <!-- row -->
        <div class="container-fluid">
            <div class="form-head mb-4 d-flex flex-wrap align-items-center justify-content-between">
                <div class="me-auto">
                    <h2 class="font-w600 mb-0">Dashboard</h2>
                    <p class="text-muted mb-0">Welcome back! Here's what's happening with your business today.</p>
                </div>

                <!-- Filter Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="dateFilterDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false"
                        style="background-color: #00ABC7 !important; color: #fff !important;">
                        <i class="fa fa-calendar me-2"></i>
                        <span id="selectedFilter">Current Month</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dateFilterDropdown">
                        <li><a class="dropdown-item filter-option" href="#" data-filter="today">Today</a></li>
                        <li><a class="dropdown-item filter-option" href="#" data-filter="week">Last Week</a></li>
                        <li><a class="dropdown-item filter-option active" href="#" data-filter="month">Current Month</a></li>
                        <li><a class="dropdown-item filter-option" href="#" data-filter="year">Last Year</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item filter-option" href="#" data-filter="custom">Custom Range</a></li>
                    </ul>
                </div>
            </div>

            <!-- Custom Date Range Modal -->
            <div class="modal fade" id="customDateModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Select Custom Date Range</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">From Date</label>
                                    <input type="date" class="form-control" id="fromDate">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">To Date</label>
                                    <input type="date" class="form-control" id="toDate">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="applyCustomRange">Apply Filter</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Main Stats Cards -->
                <div class="col-xl-9">
                    <div class="row">
                        <!-- Total Customers Card -->
                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <div class="card statistics-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h2 class="stat-number mb-0" id="totalCustomersStat">
                                                {{ Number::abbreviate($totalCustomers ?? 0) }}
                                            </h2>
                                            <h6 class="stat-label text-muted mb-0">Total Customers</h6>
                                        </div>
                                        <div class="stat-icon bg-primary-light">
                                            <i class="fa fa-users text-primary"></i>
                                        </div>
                                    </div>
                                   
                                </div>
                            </div>
                        </div>

                        <!-- Total Flavors Card -->
                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <div class="card statistics-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h2 class="stat-number mb-0" id="totalFlavorsStat">
                                                {{ Number::abbreviate($totalFlavors ?? 0) }}
                                            </h2>
                                            <h6 class="stat-label text-muted mb-0">Total Flavors</h6>
                                        </div>
                                        <div class="stat-icon bg-info-light">
                                            <i class="fa fa-ice-cream text-info"></i>
                                        </div>
                                    </div>
                                 
                                </div>
                            </div>
                        </div>
                        <!-- Total Orders Card -->
                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <div class="card statistics-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h2 class="stat-number mb-0" id="totalOrdersStat">
                                                {{ Number::abbreviate($totalOrders ?? 0) }}
                                            </h2>
                                            <h6 class="stat-label text-muted mb-0">Total Orders</h6>
                                        </div>
                                        <div class="stat-icon bg-success-light">
                                            <i class="fa fa-shopping-cart text-success"></i>
                                        </div>
                                    </div>
                                   
                                </div>
                            </div>
                        </div>
                        <!-- Total Pop Orders Card -->
                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <div class="card statistics-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h2 class="stat-number mb-0" id="totalPopOrdersStat">
                                                {{ Number::abbreviate($totalPopOrders ?? 0) }}
                                            </h2>
                                            <h6 class="stat-label text-muted mb-0">Total Pop Orders</h6>
                                        </div>
                                        <div class="stat-icon bg-primary-light">
                                            <i class="fa fa-shopping-bag text-primary"></i>
                                        </div>
                                    </div>
                                   
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Second Row -->
                    <div class="row mt-4">

                        <!-- Events Card -->
                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <div class="card statistics-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h2 class="stat-number mb-0" id="eventCountStat">{{ $eventCount }}</h2>
                                            <h6 class="stat-label text-muted mb-0">Events</h6>
                                        </div>
                                        <div class="stat-icon bg-info-light">
                                            <i class="fa fa-calendar text-info"></i>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>

                        <!-- Revenue Card -->
                        <!-- <div class="col-xl-3 col-lg-6 col-sm-6">
                                <div class="card statistics-card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <h2 class="stat-number mb-0" id="totalInventoryStat">
                                                    {{ Number::abbreviate($inventoryStatus['total_inventory'] ?? 0) }}
                                                </h2>
                                                <h6 class="stat-label text-muted mb-0">Total Inventory</h6>
                                            </div>
                                            <div class="stat-icon bg-info-light">
                                                <i class="fa fa-warehouse text-info"></i>
                                            </div>
                                        </div>
                                        <div class="progress mt-3" style="height: 4px;">
                                            <div class="progress-bar bg-info" style="width: 90%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->

                        <!-- Total Allocated to Events Card -->
                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <div class="card statistics-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h2 class="stat-number mb-0" id="totalAllocatedStat">
                                                {{ Number::abbreviate($inventoryStatus['allocated_to_events'] ?? 0) }}
                                            </h2>
                                            <h6 class="stat-label text-muted mb-0">Allocated to Events</h6>
                                        </div>
                                        <div class="stat-icon bg-warning-light">
                                            <i class="fa fa-calendar-check text-warning"></i>
                                        </div>
                                    </div>
                                   
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <div class="card statistics-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h2 class="stat-number mb-0" id="revenueStat">
                                                ${{ Number::abbreviate($totalAmount['monthly'] ?? 0) }}
                                            </h2>
                                            <h6 class="stat-label text-muted mb-0">Revenue</h6>
                                        </div>
                                        <div class="stat-icon bg-success-light">
                                            <i class="fa fa-dollar-sign text-success"></i>
                                        </div>
                                    </div>
                                   
                                </div>
                            </div>
                        </div>

                        <!-- Sales Card -->
                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <div class="card statistics-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h2 class="stat-number mb-0" id="salesCountStat">{{ $saleCount }}</h2>
                                            <h6 class="stat-label text-muted mb-0">Sales</h6>
                                        </div>
                                        <div class="stat-icon bg-warning-light">
                                            <i class="fa fa-chart-line text-warning"></i>
                                        </div>
                                    </div>
                                   
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- Third Row -->
                    <div class="row mt-4">
                        <!-- Total Inventory Card -->


                        <!-- Total Expenses Card -->
                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <div class="card statistics-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h2 class="stat-number mb-0" id="totalExpensesStat">
                                                ${{ Number::abbreviate($totalExpenses ?? 0, 2) }}</h2>
                                            <h6 class="stat-label text-muted mb-0">Total Expenses</h6>
                                        </div>
                                        <div class="stat-icon bg-danger-light">
                                            <i class="fa fa-credit-card text-danger"></i>
                                        </div>
                                    </div>
                                   
                                </div>
                            </div>
                        </div>


                        <!-- Total Charges Card -->
                        <!-- <div class="col-xl-3 col-lg-6 col-sm-6">
                                        <div class="card statistics-card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div>
                                                        <h2 class="stat-number mb-0" id="totalChargesStat">${{ Number::abbreviate($totalCharges ?? 0, 2) }}</h2>
                                                        <h6 class="stat-label text-muted mb-0">Total Charges</h6>
                                                    </div>
                                                    <div class="stat-icon bg-success-light">
                                                        <i class="fa fa-plus-circle text-success"></i>
                                                    </div>
                                                </div>
                                                <div class="progress mt-3" style="height: 4px;">
                                                    <div class="progress-bar bg-success" style="width: 70%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                    </div>

                    <!-- Chart Section -->
                    <!-- <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="card-title">Sales Overview</h4>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-3 text-muted">Period: <span id="chartPeriod">Last Month</span></span>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div id="salesChart"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->

                    <!-- Enhanced Franchise Insights Section -->
                    <div class="row mt-4">
                        <!-- Top 5 Flavors Ordered -->
                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="fa fa-star text-warning me-2"></i>
                                        Top 5 Flavors Ordered
                                    </h4>
                                </div>
                                <div class="card-body">
                                    @if(isset($topFlavors) && $topFlavors->count() > 0)
                                        @foreach($topFlavors as $index => $flavor)
                                            <div
                                                class="d-flex align-items-center mb-3 {{ $loop->last ? '' : 'border-bottom pb-3' }}">
                                                <div class="rank-badge me-3">
                                                    <span
                                                        class="badge badge-{{ $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'info') }}">
                                                        #{{ $index + 1 }}
                                                    </span>
                                                </div>
                                                <div class="flavor-image me-3">
                                                    <img src="{{ asset('storage/' . $flavor->image1) }}" alt="{{ $flavor->name }}"
                                                        class="rounded-circle"
                                                        style="width: 50px; height: 50px; object-fit: cover;">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $flavor->name }}</h6>
                                                    <small class="text-muted">{{ Number::abbreviate($flavor->total_ordered) }} units
                                                        ordered</small>
                                                </div>
                                                <div class="text-end">
                                                    <div class="progress" style="width: 100px; height: 8px;">
                                                        <div class="progress-bar bg-primary"
                                                            style="width: {{ ($flavor->total_ordered / $topFlavors->max('total_ordered')) * 100 }}%">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fa fa-chart-bar fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No flavor data available</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Total Pop Orders Panel -->
                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="fa fa-shopping-bag text-success me-2"></i>
                                        Pop Orders Overview
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <h3 class="text-success mb-1">{{ Number::abbreviate($totalPopOrders ?? 0) }}</h3>
                                                <p class="text-muted mb-0">Total Orders</p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h3 class="text-primary mb-1">{{ Number::abbreviate($monthlyPopOrders ?? 0) }}</h3>
                                            <p class="text-muted mb-0">This Month</p>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Monthly Progress</span>
                                            <span class="text-muted">
                                                {{ $totalPopOrders > 0 ? round(($monthlyPopOrders / $totalPopOrders) * 100, 1) : 0 }}%
                                            </span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-success"
                                                style="width: {{ $totalPopOrders > 0 ? (($monthlyPopOrders / $totalPopOrders) * 100) : 0 }}%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Sales Panel -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-gradient-primary text-white">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h4 class="text-white mb-1">
                                                <i class="fa fa-dollar-sign me-2"></i>
                                                Total Sales Performance
                                            </h4>
                                            <p class="text-white-50 mb-0">Complete sales overview across all franchises</p>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <div class="d-flex justify-content-end">
                                                <div class="me-4">
                                                    <h5 class="text-white mb-0">
                                                        ${{ Number::abbreviate($totalSales['current_month'] ?? 0, 2) }}</h5>
                                                    <small class="text-white-50">This Month</small>
                                                </div>
                                                <div>
                                                    <h4 class="text-white mb-0">
                                                        ${{ Number::abbreviate($totalSales['all_time'] ?? 0, 2) }}</h4>
                                                    <small class="text-white-50">All Time</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Inventory vs Event Allocation -->
                    <div class="row mt-4" style="display: none;">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="fa fa-warehouse text-info me-2"></i>
                                        Inventory vs Event Allocation (Global View)
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="text-center p-3">
                                                <h4 class="text-info mb-1">
                                                    {{ Number::abbreviate($inventoryStatus['total_inventory'] ?? 0) }}
                                                </h4>
                                                <p class="text-muted mb-0">Total Inventory</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center p-3 border-start">
                                                <h4 class="text-warning mb-1">
                                                    {{ Number::abbreviate($inventoryStatus['allocated_to_events'] ?? 0) }}
                                                </h4>
                                                <p class="text-muted mb-0">Allocated to Events</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center p-3 border-start">
                                                <h4 class="text-success mb-1">
                                                    {{ Number::abbreviate($inventoryStatus['available_inventory'] ?? 0) }}
                                                </h4>
                                                <p class="text-muted mb-0">Available</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center p-3 border-start">
                                                <h4 class="text-primary mb-1">
                                                    {{ $inventoryStatus['allocation_percentage'] ?? 0 }}%
                                                </h4>
                                                <p class="text-muted mb-0">Allocation Rate</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Inventory Allocation Status</span>
                                            <span>{{ $inventoryStatus['allocation_percentage'] ?? 0 }}%</span>
                                        </div>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-warning"
                                                style="width: {{ $inventoryStatus['allocation_percentage'] ?? 0 }}%">
                                            </div>
                                        </div>
                                        @if(($inventoryStatus['allocation_percentage'] ?? 0) > 80)
                                            <small class="text-warning mt-2 d-block">
                                                <i class="fa fa-exclamation-triangle me-1"></i>
                                                High allocation rate - consider restocking
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Event Progress Card -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h4 class="mb-1">Events Progress</h4>
                                            <p class="text-muted mb-0">Track your event completion rate</p>
                                        </div>
                                        <div class="text-end">
                                            @php
                                                $totalEvents = 10;
                                                $percentage = ($eventCount / $totalEvents) * 100;
                                            @endphp
                                            <div class="d-inline-block position-relative donut-chart-sale">
                                                <span class="donut1"
                                                    data-peity='{ "fill": ["var(--primary)", "rgba(240, 240, 240)"],   "innerRadius": 35, "radius": 10}'>{{ $eventCount }}/{{ $totalEvents }}</span>
                                                <small class="text-black">{{ round($percentage) }}%</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Franchise Events Calendar & Upcoming Events Sidebar -->
                <div class="col-xl-3">
                    <!-- Upcoming Events -->
                    <div class="card h-auto mb-4">
                        <div class="card-body text-center event-calender pb-2 px-2 pt-2">
                            <input type='text' class="form-control d-none" id='datetimepicker1'>
                        </div>
                        <div class="card-header py-0 border-0">
                            <h4 class="text-black fs-20">Upcoming Events</h4>
                            <p class="text-muted mb-0">Next scheduled events</p>
                        </div>
                        <div class="card-body pb-0 loadmore-content height300 dz-scroll" id="UpcomingEventContent">
                            @include('franchise_admin.event.upcoming')
                        </div>
                        <div class="card-footer pt-0 border-0">
                            <a href="javascript:void(0);" class="btn btn-primary btn-block text-white dz-load-more w-100"
                                id="UpcomingEvent" rel="page-error-404.html">Load More Events</a>
                        </div>
                    </div>

                    <!-- Franchise Events Calendar (Read-Only) -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="fa fa-calendar-alt text-primary me-2"></i>
                                Events Calendar
                            </h4>
                        </div>
                        <div class="card-body">
                            <div id="franchiseEventsCalendar"></div>
                        </div>
                    </div>

                    <!-- Event Allocation Summary -->
                    @if(isset($eventAllocations) && $eventAllocations->count() > 0)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <i class="fa fa-chart-pie text-success me-2"></i>
                                    Event Allocations
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="allocation-list" style="max-height: 300px; overflow-y: auto;">
                                    @foreach($eventAllocations->take(5) as $allocation)
                                        <div class="allocation-item mb-3 p-2 border rounded">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $allocation->event_name }}</h6>
                                                    <small
                                                        class="text-muted">{{ \Carbon\Carbon::parse($allocation->start_date)->format('M d, Y') }}</small>
                                                </div>
                                                <span class="badge bg-info">{{ $allocation->total_allocated }}</span>
                                            </div>
                                            <div class="mt-2">
                                                <small class="text-primary">{{ $allocation->flavor_name }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if($eventAllocations->count() > 5)
                                    <div class="text-center mt-3">
                                        <small class="text-muted">And {{ $eventAllocations->count() - 5 }} more
                                            allocations...</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        .statistics-card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .statistics-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
        }

        .stat-label {
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-primary-light {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
        }

        .bg-success-light {
            background-color: rgba(var(--bs-success-rgb), 0.1);
        }

        .bg-info-light {
            background-color: rgba(var(--bs-info-rgb), 0.1);
        }

        .bg-warning-light {
            background-color: rgba(var(--bs-warning-rgb), 0.1);
        }

        .bg-danger-light {
            background-color: rgba(var(--bs-danger-rgb), 0.1);
        }

        .dropdown-item.active {
            background-color: var(--bs-primary);
            color: white;
        }

        .filter-loading {
            opacity: 0.6;
            pointer-events: none;
            position: relative;
        }

        .filter-loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            z-index: 10;
        }

        .filter-loading::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid var(--bs-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 11;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .card {
            border-radius: 12px;
        }

        .btn {
            border-radius: 8px;
        }

        /* Enhanced Dashboard Styles */
        .rank-badge .badge {
            font-size: 0.75rem;
            padding: 0.5rem 0.75rem;
        }

        .button#dateFilterDropdown {
            background-color: #00ABC7 !important;
            color: #fff !important;
        }

        .badge-warning {
            background-color: #ffc107 !important;
            color: #fff !important;
        }

        .badge-secondary {
            background-color: #6c757d !important;
        }

        .badge-info {
            background-color: #0dcaf0 !important;
            color: #fff !important;
        }

        .flavor-image img {
            border: 2px solid #e9ecef;
        }

        .allocation-item {
            transition: all 0.2s ease;
            background-color: #f8f9fa;
        }

        .allocation-item:hover {
            background-color: #e9ecef;
            transform: translateY(-1px);
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--bs-primary) 0%, #0056b3 100%);
        }

        .text-white-50 {
            color: rgba(255, 255, 255, 0.75) !important;
        }

        .height300 {
            height: 300px;
        }

        /* Calendar Event Styles */
        .fc-event-scheduled {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
        }

        .fc-event-tentative {
            background-color: #ffc107 !important;
            border-color: #ffc107 !important;
            color: #212529 !important;
        }

        .fc-event-staffed {
            background-color: #17a2b8 !important;
            border-color: #17a2b8 !important;
        }

        .fc-event-title {
            font-size: 0.8rem;
            font-weight: 500;
        }
    </style>

    <script>
        let page = 1;
        let currentFilter = 'month';
        let customDateRange = null;

        // Load More Events functionality
        $('#UpcomingEvent').click(function () {
            page++;
            $(this).html('<i class="fa fa-spinner fa-spin me-2"></i>Loading...');

            $.ajax({
                url: "{{ route('loadMoreEvents') }}",
                type: "GET",
                data: {
                    page: page
                },
                success: function (response) {
                    $('#UpcomingEventContent').append(response.html);
                    $('#UpcomingEvent').html('Load More Events');
                    if (!response.next_page) {
                        $('#UpcomingEvent').hide();
                    }
                },
                error: function () {
                    $('#UpcomingEvent').html('Load More Events');
                }
            });
        });

        // Filter functionality
        $('.filter-option').click(function (e) {
            e.preventDefault();

            const filter = $(this).data('filter');
            const filterText = $(this).text();

            if (filter === 'custom') {
                $('#customDateModal').modal('show');
                return;
            }

            // Update active state
            $('.filter-option').removeClass('active');
            $(this).addClass('active');
            $('#selectedFilter').text(filterText);

            currentFilter = filter;
            applyFilter(filter);
        });

        // Custom date range functionality
        $('#applyCustomRange').click(function () {
            const fromDate = $('#fromDate').val();
            const toDate = $('#toDate').val();

            if (!fromDate || !toDate) {
                alert('Please select both from and to dates');
                return;
            }

            if (fromDate > toDate) {
                alert('From date cannot be greater than to date');
                return;
            }

            customDateRange = { from: fromDate, to: toDate };
            $('.filter-option').removeClass('active');
            $('#selectedFilter').text(`${fromDate} to ${toDate}`);
            $('#customDateModal').modal('hide');

            applyFilter('custom', customDateRange);
        });

        function applyFilter(filter, dateRange = null) {
            // Show loading state for ALL statistics cards
            $('.statistics-card').addClass('filter-loading');

            // Show filter indicator
            $('#selectedFilter').html('<i class="fa fa-spinner fa-spin me-2"></i>Filtering...');

            const data = {
                filter: filter,
                _token: '{{ csrf_token() }}'
            };

            if (dateRange) {
                data.from_date = dateRange.from;
                data.to_date = dateRange.to;
            }

            const filterUrl = @if(request()->route('franchisee'))
                "{{ route('franchise.dashboard.filter', ['franchisee' => request()->route('franchisee')]) }}"
            @else
                "{{ route('dashboard.filter') }}"
            @endif;

            $.ajax({
                url: filterUrl,
                type: "POST",
                data: data,
                success: function (response) {
                    // Update main statistics row
                    $('#totalCustomersStat').text(numberFormat(response.totalCustomers || 0));
                    $('#totalFlavorsStat').text(numberFormat(response.totalFlavors || 0));
                    $('#totalFlavorCategoriesStat').text(numberFormat(response.totalFlavorCategories || 0));
                    $('#totalOrdersStat').text(numberFormat(response.totalOrders || 0));

                    // Update second row statistics
                    $('#totalPopOrdersStat').text(numberFormat(response.totalPopOrders || 0));
                    $('#eventCountStat').text(numberFormat(response.eventCount));
                    $('#revenueStat').text('$' + numberFormat(response.totalAmount, 2));
                    $('#salesCountStat').text(numberFormat(response.saleCount));

                    // Update third row statistics
                    $('#totalInventoryStat').text(numberFormat(response.totalInventory || 0));
                    $('#totalAllocatedStat').text(numberFormat(response.totalAllocated || 0));
                    $('#totalExpensesStat').text('$' + numberFormat(response.totalExpenses || 0, 2));
                    $('#totalChargesStat').text('$' + numberFormat(response.totalCharges || 0, 2));

                    // Update chart period
                    $('#chartPeriod').text($('#selectedFilter').text());

                    // Update chart with new data
                    if (response.salesData) {
                        updateSalesChart(response.salesData);
                    }

                    // Remove loading state from ALL cards
                    $('.statistics-card').removeClass('filter-loading');

                    // Show success indicator
                    showFilterSuccess();
                },
                error: function () {
                    $('.statistics-card').removeClass('filter-loading');
                    $('#selectedFilter').text('Filter Error');
                    alert('Error applying filter. Please try again.');
                }
            });
        }

        function showFilterSuccess() {
            // Temporarily show a success indicator
            const originalText = $('#selectedFilter').text();
            $('#selectedFilter').html('<i class="fa fa-check text-success me-2"></i>' + originalText);

            setTimeout(function () {
                $('#selectedFilter').text(originalText);
            }, 2000);
        }

        function numberFormat(number, decimals = 0) {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            }).format(number);
        }

        function updateSalesChart(salesData) {
            if (window.salesChartInstance) {
                window.salesChartInstance.updateOptions({
                    series: [{
                        name: 'Sales',
                        data: Object.values(salesData),
                    }],
                    xaxis: {
                        categories: Object.keys(salesData),
                    }
                });
            }
        }

        $(document).ready(function () {
            salesChart();
            initializeEventsCalendar();
        });

        var salesChart = function () {
            // Safely embed Laravel JSON data
            var salesData = @json($salesData);

            console.log(salesData);

            var options = {
                series: [{
                    name: 'Sales',
                    data: Object.values(salesData),
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                colors: ['var(--bs-primary)'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.3,
                    }
                },
                stroke: {
                    show: true,
                    width: 3,
                    curve: 'smooth',
                },
                grid: {
                    borderColor: '#e0e0e0',
                    strokeDashArray: 5,
                },
                xaxis: {
                    categories: Object.keys(salesData),
                    labels: {
                        style: {
                            fontSize: '12px',
                            colors: '#666'
                        }
                    },
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return value + "k";
                        },
                        style: {
                            colors: '#666',
                            fontSize: '12px'
                        },
                    },
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                }
            };

            window.salesChartInstance = new ApexCharts(document.querySelector("#salesChart"), options);
            window.salesChartInstance.render();
        };

        function initializeEventsCalendar() {
            var calendarEl = document.getElementById('franchiseEventsCalendar');
            if (calendarEl) {
                var calendarEvents = {!! isset($calendarEvents) ? json_encode($calendarEvents) : '[]' !!};
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    height: 350,
                    headerToolbar: {
                        left: 'prev,next',
                        center: 'title',
                        right: 'dayGridMonth,listWeek'
                    },
                    events: calendarEvents,
                    eventContent: function (arg) {
                        return {
                            html: '<div class="fc-event-title text-truncate" title="' + arg.event.title + '">' +
                                arg.event.title + '</div>'
                        };
                    },
                    eventDidMount: function (info) {
                        var status = info.event.extendedProps.status;
                        var className = 'fc-event-' + status;
                        info.el.classList.add(className);

                        // Add tooltip
                        info.el.setAttribute('title',
                            info.event.title + '\n' +
                            'Status: ' + status + '\n' +
                            'Customer: ' + (info.event.extendedProps.customer || 'N/A') + '\n' +
                            'Expected Sales: $' + (info.event.extendedProps.expected_sales || '0')
                        );
                    },
                    eventClassNames: function (arg) {
                        switch (arg.event.extendedProps.status) {
                            case 'scheduled': return ['bg-success'];
                            case 'tentative': return ['bg-warning'];
                            case 'staffed': return ['bg-info'];
                            default: return ['bg-secondary'];
                        }
                    }
                });
                calendar.render();
            }
        }
    </script>
@endsection