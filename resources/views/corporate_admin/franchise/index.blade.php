@extends('layouts.app')
@section('content')

{{-- <div class="container">
    <h1>Franchise List</h1>
    <a href="{{ route('corporate_admin.franchise.create') }}" class="btn btn-primary">Add Franchise</a>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>Business Name</th>
                <th>Address</th>
                <th>State</th>
                <th>Zip Code</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($franchises as $franchise)
            <tr>
                <td>{{ $franchise->business_name }}</td>
                <td>{{ $franchise->address1 }} {{ $franchise->address2 }}</td>
                <td>{{ $franchise->state }}</td>
                <td>{{ $franchise->zip_code }}</td>
                <td>
                    <a href="{{ route('corporate_admin.franchise.edit', $franchise->franchisee_id) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('corporate_admin.franchise.destroy', $franchise->franchisee_id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div> --}}
<!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body default-height">
            <!-- row -->
			<div class="container-fluid">

				<div class="form-head mb-4 d-flex flex-wrap align-items-center">
					<div class="me-auto">
						<h2 class="font-w600 mb-0">Dashboard \</h2>
						<p>Franchise List</p>
					</div>
					<div class="input-group search-area2 d-xl-inline-flex mb-2 me-lg-4 me-md-2">
						<button class="input-group-text"><i class="flaticon-381-search-2 text-primary"></i></button>
						<input type="text" class="form-control" placeholder="Search here...">
					</div>
					<div class="dropdown custom-dropdown mb-2 period-btn">
						<div class="btn btn-sm  d-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false" role="button">
							<svg class="primary-icon" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M22.167 5.83362H21.0003V3.50028C21.0003 3.19087 20.8774 2.89412 20.6586 2.67533C20.4398 2.45653 20.143 2.33362 19.8336 2.33362C19.5242 2.33362 19.2275 2.45653 19.0087 2.67533C18.7899 2.89412 18.667 3.19087 18.667 3.50028V5.83362H9.33362V3.50028C9.33362 3.19087 9.2107 2.89412 8.99191 2.67533C8.77312 2.45653 8.47637 2.33362 8.16695 2.33362C7.85753 2.33362 7.56079 2.45653 7.34199 2.67533C7.1232 2.89412 7.00028 3.19087 7.00028 3.50028V5.83362H5.83362C4.90536 5.83362 4.01512 6.20237 3.35874 6.85874C2.70237 7.51512 2.33362 8.40536 2.33362 9.33362V10.5003H25.667V9.33362C25.667 8.40536 25.2982 7.51512 24.6418 6.85874C23.9854 6.20237 23.0952 5.83362 22.167 5.83362Z" fill="#0E8A74"/>
								<path d="M2.33362 22.1669C2.33362 23.0952 2.70237 23.9854 3.35874 24.6418C4.01512 25.2982 4.90536 25.6669 5.83362 25.6669H22.167C23.0952 25.6669 23.9854 25.2982 24.6418 24.6418C25.2982 23.9854 25.667 23.0952 25.667 22.1669V12.8336H2.33362V22.1669Z" fill="#0E8A74"/>
							</svg>
							<div class="text-start ms-3 flex-1">
								<span class="d-block text-black">Change Period</span>
								<small class="d-block text-muted">August 28th - October 28th, 2021</small>
							</div>
							<i class="fa fa-caret-down text-light scale5 ms-3"></i>
						</div>
						<div class="dropdown-menu dropdown-menu-end">
							<a class="dropdown-item" href="javascript:void(0);">October 29th - November 29th, 2021</a>
							<a class="dropdown-item" href="javascript:void(0);">July 27th - Auguts 27th, 2021</a>
						</div>
					</div>
				</div>
                <div class="row mb-4 align-items-center">
                    <div class="col-xl-3 col-lg-4 mb-4 mb-lg-0">
                        <a href="{{ route('corporate_admin.franchise.create') }}" class="btn btn-secondary btn-lg btn-block rounded text-white">+ New Franchise</a>
                    </div>
                    <div class="col-xl-9 col-lg-8">
                        <div class="card m-0">
                            <div class="card-body py-3 py-md-2">
                                <div class="d-sm-flex d-block align-items-center">
                                    <div class="d-flex mb-sm-0 mb-3 me-auto align-items-center">
                                        <div class="p-2 fs-3"><i class="bi bi-buildings-fill"></i></div>
                                        <div class="media-body">
                                            <p class="mb-1 fs-12">Total Franchises</p>
                                            <h3 class="mb-0 font-w600 fs-22">{{ $totalFranchises }} Franchises</h3>
                                        </div>
                                    </div>
                                    <div>
                                        <a href="javascript:void(0);" class="btn btn-outline-primary rounded">
                                            <i class="fa fa-check-square me-2 scale4" aria-hidden="true"></i>Active
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-outline-warning rounded ms-2">Edit</a>
                                        <a href="javascript:void(0);" class="btn btn-danger rounded ms-2">Delete</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

				<div class="row">
					<div class="col-lg-12">
						<div class="table-responsive rounded">
							<table id="example5" class="table customer-table display mb-4 fs-14 card-table">
								<thead>
                                    <tr>
                                        <th>
                                            <div class="form-check checkbox-secondary">
                                                <input class="form-check-input" type="checkbox" value="" id="checkAll">
                                                <label class="form-check-label" for="checkAll"></label>
                                            </div>
                                        </th>
                                        <th>Franchisee ID</th>
                                        <th>Business Name</th>
                                        <th>City</th>
                                        <th>State</th>
                                        <th>Zip Code</th>
                                        <th>Territory Zip codes</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
								<tbody>
                                    @foreach ($franchisees as $franchisee)
                                        <tr>
                                            <td>
                                                <div class="form-check checkbox-secondary">
                                                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault{{ $franchisee->franchisee_id }}">
                                                    <label class="form-check-label" for="flexCheckDefault{{ $franchisee->franchisee_id }}"></label>
                                                </div>
                                            </td>
                                            <td>#{{ str_pad($franchisee->franchisee_id, 7, '0', STR_PAD_LEFT) }}</td>
                                            <td>{{ $franchisee->business_name }}</td>
                                            <td>{{ $franchisee->city }}</td>
                                            {{-- <td>{{ $franchisee->created_at->format('d/m/Y') }}</td> --}}
                                            <td>{{ $franchisee->state }}</td> <!-- No data available for 'Ticket Ordered' -->
                                            <td>{{ $franchisee->zip_code }}</td>
                                            <td>
                                                @php
                                                    $zipCodes = explode(',', $franchisee->location_zip); // Convert ZIP codes into an array
                                                    $chunks = array_chunk($zipCodes, 5); // Split array into chunks of 5
                                                @endphp

                                                @foreach($chunks as $chunk)
                                                    {{ implode(', ', $chunk) }}<br>
                                                @endforeach
                                            </td>
                                            <!-- No data available for 'Last Order' -->
                                            {{-- <td class="text-secondary font-w500">$0</td> <!-- No data available for 'Total Spent' --> --}}
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('corporate_admin.franchise.edit', $franchisee->franchisee_id) }}" class="edit-franchisee">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M17 3C17.2626 2.73735 17.5744 2.52901 17.9176 2.38687C18.2608 2.24473 18.6286 2.17157 19 2.17157C19.3714 2.17157 19.7392 2.24473 20.0824 2.38687C20.4256 2.52901 20.7374 2.73735 21 3C21.2626 3.26264 21.471 3.57444 21.6131 3.9176C21.7553 4.26077 21.8284 4.62856 21.8284 5C21.8284 5.37143 21.7553 5.73923 21.6131 6.08239C21.471 6.42555 21.2626 6.73735 21 7L7.5 20.5L2 22L3.5 16.5L17 3Z" stroke="#FF7B31" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </a>

                                                    <form action="{{ route('corporate_admin.franchise.destroy', $franchisee->franchisee_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this franchisee?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="ms-4 delete-franchisee">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M3 6H5H21" stroke="#FF3131" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="#FF3131" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>

							</table>
						</div>
					</div>
				</div>
            </div>

        </div>
        <!--**********************************
            Content body end
        ***********************************-->

		<script>
            document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".edit-franchisee").forEach(button => {
        button.addEventListener("click", function () {
            let franchiseeId = this.getAttribute("data-id");
            window.location.href = `/franchisee/${franchiseeId}/edit`;
        });
    });
});

        </script>
@endsection
