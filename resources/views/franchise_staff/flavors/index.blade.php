@extends('layouts.app')
@section('content')
    <!--**********************************
                    Content body start
                ***********************************-->
    <div class="content-body default-height">
        <!-- row -->
        <div class="container-fluid">

            <div class="row">
                <div class="col-xl-3 col-xxl-3 col-sm-6 ">
                    <div class="card chart-bx">
                        <div class="card-header border-0 pb-0">
                            <div class="d-flex align-items-center">
                                <h2 class="chart-num font-w600 mb-0">{{ count($deliveredOrders) }}</h2>
                                <svg class="ms-2 primary-icon" width="19" height="12" viewBox="0 0 19 12"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M2.00401 11.1924C0.222201 11.1924 -0.670134 9.0381 0.589795 7.77817L7.78218 0.585786C8.56323 -0.195262 9.82956 -0.195262 10.6106 0.585786L17.803 7.77817C19.0629 9.0381 18.1706 11.1924 16.3888 11.1924H2.00401Z"
                                        fill="#0E8A74" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-black font-w500 mb-0">Delivered</h5>
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
                                <h2 class="chart-num font-w600 mb-0">{{ $shippedOrders }}</h2>
                                <svg class="ms-2" width="19" height="12" viewBox="0 0 19 12" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M2.00401 -4.76837e-06C0.222201 -4.76837e-06 -0.670134 2.15428 0.589795 3.41421L7.78218 10.6066C8.56323 11.3876 9.82956 11.3876 10.6106 10.6066L17.803 3.41421C19.0629 2.15428 18.1706 -4.76837e-06 16.3888 -4.76837e-06H2.00401Z"
                                        fill="#FF3131" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-black font-w500 mb-0">Shipped</h5>
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
                                <h2 class="chart-num font-w600 mb-0">{{ $paidOrders }}</h2>
                                <svg class="ms-2 primary-icon" width="19" height="12" viewBox="0 0 19 12"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M2.00401 11.1924C0.222201 11.1924 -0.670134 9.0381 0.589795 7.77817L7.78218 0.585786C8.56323 -0.195262 9.82956 -0.195262 10.6106 0.585786L17.803 7.77817C19.0629 9.0381 18.1706 11.1924 16.3888 11.1924H2.00401Z"
                                        fill="#0E8A74" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-black font-w500 mb-0">Paid</h5>
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
                                    <h2 class="chart-num font-w600 mb-0">{{ $pendingOrders }}</h2>
                                    <svg class="ms-2 primary-icon" width="19" height="12" viewBox="0 0 19 12"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M2.00401 11.1924C0.222201 11.1924 -0.670134 9.0381 0.589795 7.77817L7.78218 0.585786C8.56323 -0.195262 9.82956 -0.195262 10.6106 0.585786L17.803 7.77817C19.0629 9.0381 18.1706 11.1924 16.3888 11.1924H2.00401Z"
                                            fill="#0E8A74" />
                                    </svg>
                                </div>
                                <div>
                                    <h5 class="text-black font-w500 mb-3 mt-2">Pending</h5>
                                </div>
                            </div>
                            <div>
                                <div class="d-inline-block position-relative donut-chart-sale">
                                    <span class="donut1"
                                        data-peity='{ "fill": ["var(--primary)", "rgba(240, 240, 240)"],   "innerRadius": 35, "radius": 10}'>5/8</span>
                                    <small class="text-black">66%</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-head mb-4 d-flex flex-wrap align-items-center">
                <div class="me-auto">
                    <h2 class="font-w600 mb-0">Dashboard \ Inventory</h2>
                    <p>Delivered orders</p>
                </div>
                {{-- <div class="input-group search-area2 d-xl-inline-flex mb-2 me-lg-4 me-md-2">
                    <button class="input-group-text"><i class="flaticon-381-search-2 text-primary"></i></button>
                    <input type="text" class="form-control" placeholder="Search here...">
                </div> --}}
                {{-- <div class="dropdown custom-dropdown mb-2 period-btn">
                    <div class="btn btn-sm  d-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false"
                        role="button">
                        <svg class="primary-icon" width="28" height="28" viewBox="0 0 28 28" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M22.167 5.83362H21.0003V3.50028C21.0003 3.19087 20.8774 2.89412 20.6586 2.67533C20.4398 2.45653 20.143 2.33362 19.8336 2.33362C19.5242 2.33362 19.2275 2.45653 19.0087 2.67533C18.7899 2.89412 18.667 3.19087 18.667 3.50028V5.83362H9.33362V3.50028C9.33362 3.19087 9.2107 2.89412 8.99191 2.67533C8.77312 2.45653 8.47637 2.33362 8.16695 2.33362C7.85753 2.33362 7.56079 2.45653 7.34199 2.67533C7.1232 2.89412 7.00028 3.19087 7.00028 3.50028V5.83362H5.83362C4.90536 5.83362 4.01512 6.20237 3.35874 6.85874C2.70237 7.51512 2.33362 8.40536 2.33362 9.33362V10.5003H25.667V9.33362C25.667 8.40536 25.2982 7.51512 24.6418 6.85874C23.9854 6.20237 23.0952 5.83362 22.167 5.83362Z"
                                fill="#0E8A74" />
                            <path
                                d="M2.33362 22.1669C2.33362 23.0952 2.70237 23.9854 3.35874 24.6418C4.01512 25.2982 4.90536 25.6669 5.83362 25.6669H22.167C23.0952 25.6669 23.9854 25.2982 24.6418 24.6418C25.2982 23.9854 25.667 23.0952 25.667 22.1669V12.8336H2.33362V22.1669Z"
                                fill="#0E8A74" />
                        </svg>
                        <div class="text-start ms-3 flex-1">
                            <span class="d-block text-black">Change Periode</span>
                            <small class="d-block text-muted">August 28th - October 28th, 2021</small>
                        </div>
                        <i class="fa fa-caret-down text-light scale5 ms-3"></i>
                    </div>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="javascript:void(0);">October 29th - November 29th, 2021</a>
                        <a class="dropdown-item" href="javascript:void(0);">July 27th - Auguts 27th, 2021</a>
                    </div>
                </div> --}}
            </div>



            <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive rounded">
                        <table id="example5" class="table customer-table display mb-4 fs-14 card-table">
                            <thead>
                                <tr>
                                    <th>User Name</th>
                                    <th>Items Ordered</th>
                                    <th>Status</th>
                                    <th>Total Price</th>
                                    <th>Order Date/Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $index => $order)
                                @php
                                    $totalAmount = \DB::table('fgp_order_details')
                                        ->where('fgp_order_id', $order->fgp_ordersID)
                                        ->selectRaw('SUM(unit_number * unit_cost) as total')
                                        ->value('total');
                                @endphp
                                <tr style="text-wrap: no-wrap;">
                                    @php
                                        $franchisee = App\Models\Franchisee::where('franchisee_id' , $order->franchisee_id  ?? $franchisee_id)->first();
                                    @endphp
                                    <td>{{ $franchisee->business_name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="cursor-pointer text-primary order-detail-trigger" data-id="{{ $order->fgp_ordersID }}">
                                            {{ \DB::table('fgp_order_details')->where('fgp_order_id', $order->fgp_ordersID)->count() }} items
                                        </span>
                                    </td>
                                    <td>{{ $order->status }}</td>
                                    <td>${{ number_format($totalAmount, 2) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($order->date_transaction)->format('M d, Y h:i A') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>


            {{-- <div class="row">
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
                                    <th>Name</th>
                                    <th>Image</th>
                                    <th>Price Per Case</th>
                                    <th>Category</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deliveredOrders as $order)
                                    <tr>
                                        <td>
                                            <div class="form-check checkbox-secondary">
                                                <input class="form-check-input pop-checkbox" type="checkbox"
                                                    value="{{ $order->fgp_item_id }}"
                                                    id="flexCheckDefault{{ $order->fgp_item_id }}">
                                                <label class="form-check-label"
                                                    for="flexCheckDefault{{ $order->fgp_item_id }}"></label>
                                            </div>
                                        </td>
                                        <td class="item-name">{{ $order->item->name }}</td>
                                        <td class="item-image">
                                            @if ($order->item->image1)
                                                <img src="{{ asset('storage/' . $order->item->image1) }}" alt="Image"
                                                    style="width: 50px; height: 50px; object-fit: contain;">
                                            @else
                                                <span>No Image</span>
                                            @endif
                                        </td>
                                        <td class="item-price">${{ number_format($order->item->case_cost, 2) }}</td>
                                        <td class="item-category">
                                            @if ($order->item->categories && $order->item->categories->isNotEmpty())
                                                @php
                                                    $chunks = $order->item->categories->pluck('name')->chunk(5);
                                                @endphp
                                                @foreach ($chunks as $chunk)
                                                    {{ $chunk->join(', ') }} <br>
                                                @endforeach
                                            @else
                                                No Category
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>


                        <script>
                            document.getElementById('orderButton').addEventListener('click', function() {
                                console.log("Confirm Order button clicked");

                                let checkedItems = [];

                                document.querySelectorAll('.pop-checkbox:checked').forEach((checkbox) => {
                                    const row = checkbox.closest('tr');

                                    const itemDetails = {
                                        id: checkbox.value,
                                        name: row.querySelector('.item-name').innerText.trim(),
                                        image: row.querySelector('.item-image img') ? row.querySelector('.item-image img')
                                            .src : 'No Image',
                                        price: row.querySelector('.item-price').innerText.trim(),
                                        category: row.querySelector('.item-category').innerText.trim(),
                                        quantity: 1
                                    };

                                    console.log("Collected item details:", itemDetails);
                                    checkedItems.push(itemDetails);
                                });

                                console.log("Checked Items:", checkedItems);

                                if (checkedItems.length < 3) {
                                    alert("Please select at least three items to order.");
                                    console.log("Less than three items selected, alert displayed.");
                                    return;
                                }


                                console.log("Sending request to server with checked items...");

                                const url = "{{ route('franchise.orderpops.confirm') }}";

                                fetch(url, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}' // Make sure to include CSRF token for Laravel
                                        },
                                        body: JSON.stringify({
                                            ordered_items: checkedItems
                                        })
                                    })
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error(`HTTP error! Status: ${response.status}`);
                                        }
                                        return response.json();
                                    })
                                    .then(data => {
                                        console.log("Parsed Response Data:", data);

                                        if (data.redirect) {
                                            console.log("Redirecting to:", data.redirect);
                                            window.location.href = data.redirect;
                                        } else {
                                            console.error('Invalid response format:', data);
                                        }
                                    })
                                    .catch(error => console.error('Error occurred:', error));
                            });
                        </script>



                    </div>
                </div>
            </div> --}}
        </div>



    </div>

    <div class="modal fade" id="modalId" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Body</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>


    {{-- <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('franchise.orderpops.edit', $order->id) }}" class="edit-user">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M17 3C17.2626 2.73735 17.5744 2.52901 17.9176 2.38687C18.2608 2.24473 18.6286 2.17157 19 2.17157C19.3714 2.17157 19.7392 2.24473 20.0824 2.38687C20.4256 2.52901 20.7374 2.73735 21 3C21.2626 3.26264 21.471 3.57444 21.6131 3.9176C21.7553 4.26077 21.8284 4.62856 21.8284 5C21.8284 5.37143 21.7553 5.73923 21.6131 6.08239C21.471 6.42555 21.2626 6.73735 21 7L7.5 20.5L2 22L3.5 16.5L17 3Z" stroke="#FF7B31" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('franchise.orderpops.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="ms-4 delete-user">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M3 6H5H21" stroke="#FF3131" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="#FF3131" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td> --}}
    <!--**********************************
                    Content body end
                ***********************************-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.orderable-dropdown').change(function() {
                let itemId = $(this).data('id');
                let orderableValue = $(this).val();

                $.ajax({
                    url: "{{ route('franchise.fgpitem.updateOrderable', ['franchisee' => $franchisee_id]) }}",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        item_id: itemId,
                        pop_orderable: orderableValue
                    },
                    success: function(response) {
                        console.log('Success:', response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });
        });
    </script>





<div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Table for displaying order details -->

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded text-secondary custom-hover" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>




@endsection
@push('scripts')
<script>
$(document).ready(function () {
    $('.order-detail-trigger').on('click', function () {
        const orderId = $(this).data('id'); // Get the order ID from the data-id attribute

        $.ajax({
            url: '{{ route('franchise.flavors.detail', ['franchisee' => $franchisee_id]) }}', // Backend route to fetch order details
            method: 'GET',
            data: { id: orderId }, // Pass orderId to backend
            success: function (response) {
    // Assuming response contains the orderDetails array
    let orderDetails = response.orderDetails;

    // Prepare HTML to display the order details inside a table
    let detailsHtml = `
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">Item</th>
                    <th scope="col">Unit Cost</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Transaction Date</th>
                </tr>
            </thead>
            <tbody>
    `;

    // Loop through orderDetails and create table rows
    orderDetails.forEach(function(detail) {
        detailsHtml += `
            <tr>
                <td>${detail.name}</td>
                <td>$${detail.unit_cost}</td>
                <td>${detail.unit_number}</td>
                <td>${detail.formatted_date}</td>
            </tr>
        `;
    });

    // Close the table
    detailsHtml += `</tbody></table>`;

    // Insert the details HTML into the modal body
    $('#orderModal .modal-body').html(detailsHtml);

    // Show the modal
    $('#orderModal').modal('show');
},

            error: function () {
                alert('Error loading order details.');
            }
        });
    });
});



</script>
@endpush
