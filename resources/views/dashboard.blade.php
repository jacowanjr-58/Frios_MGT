@extends('layouts.app')

@section('content')
    <x-notify::notify />
    @php
                        $user = auth()->user();
                        $franchisees = $user->franchisees ?? collect();
                        $selectedFranchiseeId = $franchiseeId ?? null;
                    @endphp
                    @if($user->hasRole('franchise_admin') && $franchisees->count() > 1)
                        <div class="mb-3">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <label for="franchisee_id" class="form-label mb-0 me-2">Select Franchisee:</label>
                                </div>
                                <div class="col-auto">
                                    <select name="franchisee_id" id="franchisee_id" class="form-select form-control" onchange="if(this.value) window.location.href='/franchise/' + this.value + '/dashboard'">
                                        @foreach($franchisees as $franchisee)
                                            <option value="{{ $franchisee->franchisee_id }}" {{ $selectedFranchiseeId == $franchisee->franchisee_id ? 'selected' : '' }}>
                                                {{ $franchisee->business_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif
    <!--**********************************
                                        Content body start
                                    ***********************************-->
    @if (auth()->user()->role == 'corporate_admin' || auth()->user()->role == 'franchise_admin')
        <div class=" content-body default-height">
            <!-- row -->
            <div class="container-fluid">
                <div class="form-head mb-4 d-flex flex-wrap align-items-center">
                    <div class="me-auto">
                        <h2 class="font-w600 mb-0">Dashboard</h2>
                        <!-- <p>Lorem ipsum  dolor sit amet </p> -->
                         
                    </div>
                  
                </div>
                <div class="row">
                    <div class="col-xl-12">
                        <div class="row">
                            <div class="col-xl-3 col-xxl-3 col-sm-6 ">
                                <div class="card chart-bx">
                                    <div class="card-header border-0 pb-0">
                                        <div class="d-flex align-items-center">
                                            <h2 class="chart-num font-w600 mb-0">{{ $eventCount }}</h2>
                                            <svg class="ms-2 primary-icon" width="19" height="12" viewBox="0 0 19 12"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M2.00401 11.1924C0.222201 11.1924 -0.670134 9.0381 0.589795 7.77817L7.78218 0.585786C8.56323 -0.195262 9.82956 -0.195262 10.6106 0.585786L17.803 7.77817C19.0629 9.0381 18.1706 11.1924 16.3888 11.1924H2.00401Z"
                                                    fill="#0E8A74" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h5 class="text-black font-w500 mb-0">Events</h5>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0 chart-body-wrapper">
                                        <div id="widgetChart1" class="chart-primary"></div>
                                    </div>
                                </div>
                            </div>
                            @if (auth()->user()->role == 'franchise_admin')
                                <div class="col-xl-3 col-xxl-3 col-sm-6 ">
                                    <div class="card chart-bx">
                                        <div class="card-header border-0 pb-0">
                                            <div class="d-flex align-items-center">
                                                <h2 class="chart-num font-w600 mb-0">
                                                    ${{ number_format($totalAmount['monthly']) }}
                                                </h2>
                                                <svg class="ms-2" width="19" height="12" viewBox="0 0 19 12"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M2.00401 -4.76837e-06C0.222201 -4.76837e-06 -0.670134 2.15428 0.589795 3.41421L7.78218 10.6066C8.56323 11.3876 9.82956 11.3876 10.6106 10.6066L17.803 3.41421C19.0629 2.15428 18.1706 -4.76837e-06 16.3888 -4.76837e-06H2.00401Z"
                                                        fill="#FF3131" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h5 class="text-black font-w500 mb-0">Revenue</h5>
                                            </div>
                                        </div>
                                        <div class="card-body pt-0 chart-body-wrapper">
                                            <div id="widgetChart2">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-xxl-3 col-sm-6 ">
                                    <div class="card chart-bx">
                                        <div class="card-header border-0 pb-0">
                                            <div class="d-flex align-items-center">
                                                <h2 class="chart-num font-w600 mb-0">{{ $saleCount }}</h2>
                                                <svg class="ms-2 primary-icon" width="19" height="12"
                                                    viewBox="0 0 19 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M2.00401 11.1924C0.222201 11.1924 -0.670134 9.0381 0.589795 7.77817L7.78218 0.585786C8.56323 -0.195262 9.82956 -0.195262 10.6106 0.585786L17.803 7.77817C19.0629 9.0381 18.1706 11.1924 16.3888 11.1924H2.00401Z"
                                                        fill="#0E8A74" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h5 class="text-black font-w500 mb-0">Sales</h5>
                                            </div>
                                        </div>
                                        <div class="card-body pt-0">
                                            <canvas id="widgetChart3" height="60"></canvas>
                                        </div>
                                    </div>
                                </div>
                            @elseif(auth()->user()->role == 'corporate_admin')
                                <div class="col-xl-3 col-xxl-3 col-sm-6 ">
                                    <div class="card chart-bx">
                                        <div class="card-header border-0 pb-0">
                                            <div class="d-flex align-items-center">
                                                <h2 class="chart-num font-w600 mb-0">
                                                    ${{ number_format($totalAmount['monthly']) }}
                                                </h2>
                                                <svg class="ms-2" width="19" height="12" viewBox="0 0 19 12"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M2.00401 -4.76837e-06C0.222201 -4.76837e-06 -0.670134 2.15428 0.589795 3.41421L7.78218 10.6066C8.56323 11.3876 9.82956 11.3876 10.6106 10.6066L17.803 3.41421C19.0629 2.15428 18.1706 -4.76837e-06 16.3888 -4.76837e-06H2.00401Z"
                                                        fill="#FF3131" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h5 class="text-black font-w500 mb-0">Revenue</h5>
                                            </div>
                                        </div>
                                        <div class="card-body pt-0 chart-body-wrapper">
                                            <div id="widgetChart2">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-xxl-3 col-sm-6 ">
                                    <div class="card chart-bx">
                                        <div class="card-header border-0 pb-0">
                                            <div class="d-flex align-items-center">
                                                <h2 class="chart-num font-w600 mb-0">{{ $saleCount }}</h2>
                                                <svg class="ms-2 primary-icon" width="19" height="12"
                                                    viewBox="0 0 19 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M2.00401 11.1924C0.222201 11.1924 -0.670134 9.0381 0.589795 7.77817L7.78218 0.585786C8.56323 -0.195262 9.82956 -0.195262 10.6106 0.585786L17.803 7.77817C19.0629 9.0381 18.1706 11.1924 16.3888 11.1924H2.00401Z"
                                                        fill="#0E8A74" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h5 class="text-black font-w500 mb-0">Sales</h5>
                                            </div>
                                        </div>
                                        <div class="card-body pt-0">
                                            <canvas id="widgetChart3" height="60"></canvas>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-xl-3 col-xxl-3 col-sm-6 ">
                                <div class="card chart-bx">
                                    <div class="card-body pt-sm-4 pt-3 d-flex align-items-center justify-content-between">
                                        <div class="me-3">
                                            <div class="d-flex align-items-center">
                                                <h2 class="chart-num font-w600 mb-0">{{ $eventCount }}</h2>
                                            </div>
                                            <div>
                                                <h5 class="text-black font-w500 mb-3 mt-2">Event Held</h5>
                                            </div>
                                            <div>
                                                <p class="text-primary fs-14 mb-0">
                                                    <svg class="me-2 primary-icon" width="19" height="12"
                                                        viewBox="0 0 19 12" fill="none"
                                                        xmlns=	"http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M2.00401 11.1924C0.222201 11.1924 -0.670134 9.0381 0.589795 7.77817L7.78218 0.585786C8.56323 -0.195262 9.82956 -0.195262 10.6106 0.585786L17.803 7.77817C19.0629 9.0381 18.1706 11.1924 16.3888 11.1924H2.00401Z"
                                                            fill="#0E8A74" />
                                                    </svg>
                                                    2.4%
                                                    <span class="op-6 text-muted">than Last Week</span>
                                                </p>
                                            </div>
                                        </div>
                                        <div>
                                            @php
                                                $totalEvents = 10; // Set your total reference value
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
                    @if (auth()->user()->role == 'corporate_admin')
                        <div class="col-xl-9 col-xxl-8">
                            <div class="row">
                                <div class="col-xl-6 col-xxl-12">
                                    <div class="card">
                                        <div class="card-header border-0 flex-wrap pb-0">
                                            <h4 class="fs-20 font-w500 card-title">Sales Revenue</h4>
                                            <div class="card-action coin-tabs">
                                                <ul class="nav nav-tabs" role="tablist">
                                                    {{-- <li class="nav-item">
													<a class="nav-link active" data-bs-toggle="tab" href="#Monthly1">
														Monthly
													</a>
												</li> --}}
                                                    {{-- <li class="nav-item">
													<a class="nav-link " data-bs-toggle="tab" href="#Weekly1">
														Weekly
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link " data-bs-toggle="tab" href="#Daily1">
														Daily
													</a>
												</li> --}}
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-body pb-2">
                                            <div class="tab-content">
                                                <div class="tab-pane fade active show" id="Monthly1">
                                                    <div id="salesChart" class="chart-primary"></div>
                                                </div>
                                                {{-- <div class="tab-pane fade" id="Weekly1">
												<div id="salesChart1" class="chart-primary"></div>

											</div>
											<div class="tab-pane fade" id="Daily1">
												<div id="salesChart2" class="chart-primary"></div>
											</div> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif(auth()->user()->role == 'franchise_admin')
                        <div class="col-xl-9 col-xxl-8">
                            <div class="row">
                                <div class="col-xl-6 col-xxl-12">
                                    <div class="card">
                                        <div class="card-header border-0 flex-wrap pb-0">
                                            <h4 class="fs-20 font-w500 card-title">Sales Revenue</h4>
                                            <div class="card-action coin-tabs">
                                                <ul class="nav nav-tabs" role="tablist">
                                                    {{-- <li class="nav-item">
													<a class="nav-link active" data-bs-toggle="tab" href="#Monthly1">
														Monthly
													</a>
												</li> --}}
                                                    {{-- <li class="nav-item">
													<a class="nav-link " data-bs-toggle="tab" href="#Weekly1">
														Weekly
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link " data-bs-toggle="tab" href="#Daily1">
														Daily
													</a>
												</li> --}}
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-body pb-2">
                                            <div class="tab-content">
                                                <div class="tab-pane fade active show" id="Monthly1">
                                                    <div id="salesChart" class="chart-primary"></div>
                                                </div>
                                                {{-- <div class="tab-pane fade" id="Weekly1">
												<div id="salesChart1" class="chart-primary"></div>

											</div>
											<div class="tab-pane fade" id="Daily1">
												<div id="salesChart2" class="chart-primary"></div>
											</div> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="col-xl-3 col-xxl-4">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card">
                                    <div class="card-body text-center event-calender pb-2 px-2 pt-2">
                                        <input type='text' class="form-control d-none" id='datetimepicker1'>
                                    </div>
                                    <div class="card-header py-0 border-0">
                                        <h4 class="text-black fs-20">Upcoming Events</h4>
                                    </div>
                                    <div class="card-body pb-0 loadmore-content height450 dz-scroll"
                                        id="UpcomingEventContent">
                                        @include('franchise_admin.event.upcoming')
                                    </div>
                                    <div class="card-footer pt-0 border-0">
                                        <a href="javascript:void(0);"
                                            class="btn btn-secondary btn-block text-white dz-load-more" id="UpcomingEvent"
                                            rel="page-error-404.html">Load More</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endif
    <!--**********************************
                                        Content body end
                                    ***********************************-->

    @if (auth()->user()->role == 'franchise_staff' || auth()->user()->role == 'franchise_manager')
        <div class=" content-body default-height">
            <!-- row -->
            <div class="container-fluid">
                <div class="form-head mb-4 d-flex flex-wrap align-items-center">
                    <div class="me-auto">
                        <h2 class="font-w600 mb-0">Dashboard</h2>
                        <!-- <p>Lorem ipsum  dolor sit amet </p> -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-4 col-xxl-4 col-sm-6 " style="height: 200px;">
                        <div class="card chart-bx">
                            <div class="card-header border-0 pb-0">
                                <div class="d-flex align-items-center">
                                    <h2 class="chart-num font-w600 mb-0">{{ $eventCount }}</h2>
                                    <svg class="ms-2 primary-icon" width="19" height="12" viewBox="0 0 19 12"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M2.00401 11.1924C0.222201 11.1924 -0.670134 9.0381 0.589795 7.77817L7.78218 0.585786C8.56323 -0.195262 9.82956 -0.195262 10.6106 0.585786L17.803 7.77817C19.0629 9.0381 18.1706 11.1924 16.3888 11.1924H2.00401Z"
                                            fill="#0E8A74" />
                                    </svg>
                                </div>
                                <div>
                                    <h5 class="text-black font-w500 mb-0">Events</h5>
                                </div>
                            </div>
                            <div class="card-body pt-0 chart-body-wrapper">
                                <div id="widgetChart1" class="chart-primary"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-xxl-4 col-sm-6 "  style="height: 200px;">
                        <div class="card chart-bx">
                            <div class="card-body pt-sm-4 pt-3 d-flex align-items-center justify-content-between">
                                <div class="me-3">
                                    <div class="d-flex align-items-center">
                                        <h2 class="chart-num font-w600 mb-0">{{ $eventCount }}</h2>
                                    </div>
                                    <div>
                                        <h5 class="text-black font-w500 mb-3 mt-2">Event Held</h5>
                                    </div>
                                    <div>
                                        <p class="text-primary fs-14 mb-0">
                                            <svg class="me-2 primary-icon" width="19" height="12"
                                                viewBox="0 0 19 12" fill="none" xmlns=	"http://www.w3.org/2000/svg">
                                                <path
                                                    d="M2.00401 11.1924C0.222201 11.1924 -0.670134 9.0381 0.589795 7.77817L7.78218 0.585786C8.56323 -0.195262 9.82956 -0.195262 10.6106 0.585786L17.803 7.77817C19.0629 9.0381 18.1706 11.1924 16.3888 11.1924H2.00401Z"
                                                    fill="#0E8A74" />
                                            </svg>
                                            2.4%
                                            <span class="op-6 text-muted">than Last Week</span>
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    @php
                                        $totalEvents = 10; // Set your total reference value
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
                    <div class="col-xl-4 col-xxl-4">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card">
                                    <div class="card-body text-center event-calender pb-2 px-2 pt-2">
                                        <input type='text' class="form-control d-none" id='datetimepicker1'>
                                    </div>
                                    <div class="card-header py-0 border-0">
                                        <h4 class="text-black fs-20">Upcoming Events</h4>
                                    </div>
                                    <div class="card-body pb-0 loadmore-content height450 dz-scroll"
                                        id="UpcomingEventContent">
                                        @include('franchise_admin.event.upcoming')
                                    </div>
                                    <div class="card-footer pt-0 border-0">
                                        <a href="javascript:void(0);"
                                            class="btn btn-secondary btn-block text-white dz-load-more" id="UpcomingEvent"
                                            rel="page-error-404.html">Load More</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endif

   

    <script>
        let page = 1;

        $('#UpcomingEvent').click(function() {
            page++;

            $.ajax({
                url: "{{ route('loadMoreEvents') }}",
                type: "GET",
                data: {
                    page: page
                },
                success: function(response) {
                    $('#UpcomingEventContent').append(response.html);
                    $('#UpcomingEvent').html('Load More');
                    if (!response.next_page) {
                        $('#UpcomingEvent').hide(); // Hide button when no more events
                    }
                }
            });
        });

        $(document).ready(function() {
            salesChart();
        });

        var salesChart = function() {
            // Safely embed Laravel JSON data
            var salesData = {!! json_encode($salesData) !!};

            console.log(salesData);

            var options = {
                series: [{
                    name: 'Yearly Sales',
                    data: Object.values(salesData),
                }],
                chart: {
                    type: 'line',
                    height: 380,
                    toolbar: {
                        show: false
                    },
                },
                colors: ['var(--primary)'],
                stroke: {
                    show: true,
                    width: 6,
                    curve: 'smooth',
                    colors: ['var(--primary)'],
                },
                xaxis: {
                    categories: Object.keys(salesData),
                    labels: {
                        style: {
                            fontSize: '14px',
                            colors: '#a4a7ab'
                        }
                    },
                },
                yaxis: {
                    labels: {
                        formatter: function(value) {
                            return value + "k";
                        },
                        style: {
                            colors: '#a4a7ab',
                            fontSize: '14px'
                        },
                    },
                },
            };

            var chart = new ApexCharts(document.querySelector("#salesChart"), options);
            chart.render();
        };
    </script>
@endsection
