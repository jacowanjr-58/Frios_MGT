@extends('layouts.app')

@section('content')
<x-notify::notify />

<!--**********************************
            Content body start
        ***********************************-->
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
											<h2 class="chart-num font-w600 mb-0">215</h2>
											<svg class="ms-2 primary-icon" width="19" height="12" viewBox="0 0 19 12" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M2.00401 11.1924C0.222201 11.1924 -0.670134 9.0381 0.589795 7.77817L7.78218 0.585786C8.56323 -0.195262 9.82956 -0.195262 10.6106 0.585786L17.803 7.77817C19.0629 9.0381 18.1706 11.1924 16.3888 11.1924H2.00401Z" fill="#0E8A74"/>
											</svg>
										</div>
										<div>
											<h5 class="text-black font-w500 mb-0">Tickets</h5>
										</div>
									</div>
									<div class="card-body pt-0 chart-body-wrapper">
										<div id="widgetChart1" class="chart-primary"></div>
									</div>
								</div>
							</div>
							<div class="col-xl-3 col-xxl-3 col-sm-6 ">
								<div class="card chart-bx">
									<div class="card-header border-0 pb-0">
										<div class="d-flex align-items-center">
											<h2 class="chart-num font-w600 mb-0">$536k</h2>
											<svg class="ms-2" width="19" height="12" viewBox="0 0 19 12" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M2.00401 -4.76837e-06C0.222201 -4.76837e-06 -0.670134 2.15428 0.589795 3.41421L7.78218 10.6066C8.56323 11.3876 9.82956 11.3876 10.6106 10.6066L17.803 3.41421C19.0629 2.15428 18.1706 -4.76837e-06 16.3888 -4.76837e-06H2.00401Z" fill="#FF3131"/>
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
											<h2 class="chart-num font-w600 mb-0">652</h2>
											<svg class="ms-2 primary-icon" width="19" height="12" viewBox="0 0 19 12" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M2.00401 11.1924C0.222201 11.1924 -0.670134 9.0381 0.589795 7.77817L7.78218 0.585786C8.56323 -0.195262 9.82956 -0.195262 10.6106 0.585786L17.803 7.77817C19.0629 9.0381 18.1706 11.1924 16.3888 11.1924H2.00401Z" fill="#0E8A74"/>
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
							<div class="col-xl-3 col-xxl-3 col-sm-6 ">
								<div class="card chart-bx">
									<div class="card-body pt-sm-4 pt-3 d-flex align-items-center justify-content-between">
										<div class="me-3">
											<div class="d-flex align-items-center">
												<h2 class="chart-num font-w600 mb-0">45242</h2>
											</div>
											<div>
												<h5 class="text-black font-w500 mb-3 mt-2">Event Held</h5>
											</div>
											<div>
												<p class="text-primary fs-14 mb-0">
													<svg class="me-2 primary-icon" width="19" height="12" viewBox="0 0 19 12" fill="none" xmlns=	"http://www.w3.org/2000/svg">
														<path d="M2.00401 11.1924C0.222201 11.1924 -0.670134 9.0381 0.589795 7.77817L7.78218 0.585786C8.56323 -0.195262 9.82956 -0.195262 10.6106 0.585786L17.803 7.77817C19.0629 9.0381 18.1706 11.1924 16.3888 11.1924H2.00401Z" fill="#0E8A74"/>
													</svg>
													2.4%
													<span class="op-6 text-muted">than Last Week</span>
												</p>
											</div>
										</div>
										<div>
											<div class="d-inline-block position-relative donut-chart-sale">
												<span class="donut1" data-peity='{ "fill": ["var(--primary)", "rgba(240, 240, 240)"],   "innerRadius": 35, "radius": 10}'>5/8</span>
												<small class="text-black">66%</small>
											</div>
										</div>	
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-9 col-xxl-8">
						<div class="row">
							<div class="col-xl-6 col-xxl-12">
								<div class="card">
									<div class="card-header border-0 flex-wrap pb-0">
										<h4 class="fs-20 font-w500 card-title">Sales Revenue</h4>
										<div class="card-action coin-tabs">
											<ul class="nav nav-tabs" role="tablist">
												<li class="nav-item">
													<a class="nav-link active" data-bs-toggle="tab" href="#Monthly1">
														Monthly
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link " data-bs-toggle="tab" href="#Weekly1">
														Weekly
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link " data-bs-toggle="tab" href="#Daily1">
														Daily
													</a>
												</li>
											</ul>
										</div>
									</div>
									<div class="card-body pb-2">
										<div class="tab-content">
											<div class="tab-pane fade active show" id="Monthly1">	
												<div id="salesChart" class="chart-primary"></div>
											</div>
											<div class="tab-pane fade" id="Weekly1">
												<div id="salesChart1" class="chart-primary"></div>
											
											</div>	
											<div class="tab-pane fade" id="Daily1">
												<div id="salesChart2" class="chart-primary"></div>
											</div>
										</div>	
									</div>
								</div>
							</div>
						</div>	
					</div>
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
									<div class="card-body pb-0 loadmore-content height450 dz-scroll" id="UpcomingEventContent">
										<div class="media mb-5 align-items-center event-list">
											<div class="p-3 text-center me-3 date-bx bgl-primary">
												<h2 class="mb-0 text-secondary fs-28 font-w600">3</h2>
												<h5 class="mb-1 text-black">Wed</h5>
											</div>
											<div class="media-body px-0">
												<h6 class="mt-0 mb-3 fs-14"><a class="text-black" href="event.html">Live Concert Choir Charity Event 2020</a></h6>
												<ul class="fs-14 list-inline mb-2 d-flex justify-content-between">
													<li>Ticket Sold</li>
													<li>561/650</li>
												</ul>
												<div class="progress mb-0" style="height:4px; width:100%;">
													<div class="progress-bar bg-warning progress-animated" style="width:60%;" role="progressbar">
														<span class="sr-only">60% Complete</span>
													</div>
												</div>
											</div>
										</div>
										<div class="media mb-5 align-items-center event-list">
											<div class="p-3 text-center me-3 date-bx bgl-primary">
												<h2 class="mb-0 text-secondary fs-28 font-w600">16</h2>
												<h5 class="mb-1 text-black">Wed</h5>
											</div>
											<div class="media-body px-0">
												<h6 class="mt-0 mb-3 fs-14"><a class="text-black" href="event.html">Live Concert Choir Charity Event 2020</a></h6>
												<ul class="fs-14 list-inline mb-2 d-flex justify-content-between">
													<li>Ticket Sold</li>
													<li>431/650</li>
												</ul>
												<div class="progress mb-0" style="height:4px; width:100%;">
													<div class="progress-bar bg-warning progress-animated" style="width:50%;" role="progressbar">
														<span class="sr-only">60% Complete</span>
													</div>
												</div>
											</div>
										</div>
										<div class="media mb-5 align-items-center event-list">
											<div class="p-3 text-center me-3 date-bx bgl-primary">
												<h2 class="mb-0 text-primary fs-28 font-w600">28</h2>
												<h5 class="mb-1 text-black">Wed</h5>
											</div>
											<div class="media-body px-0">
												<h6 class="mt-0 mb-3 fs-14"><a class="text-black" href="event.html">Live Concert Choir Charity Event 2020</a></h6>
												<ul class="fs-14 list-inline mb-2 d-flex justify-content-between">
													<li>Ticket Sold</li>
													<li>650/650</li>
												</ul>
												<div class="progress mb-0" style="height:4px; width:100%;">
													<div class="progress-bar bg-warning progress-animated" style="width:100%;" role="progressbar">
														<span class="sr-only">60% Complete</span>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="card-footer pt-0 border-0">
										<a href="javascript:void(0);" class="btn btn-secondary btn-block text-white dz-load-more" id="UpcomingEvent" rel="page-error-404.html">Load More</a>
									</div>
								</div>
							</div>
						</div>	
						
					</div>
				</div>
            </div>
        </div>
        <!--**********************************
            Content body end
        ***********************************-->
@endsection
