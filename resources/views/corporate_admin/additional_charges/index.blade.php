@extends('layouts.app')
@section('content')

    @push('styles')
        <style>
            .dataTables_paginate.paging_simple_numbers {
                margin: 15px 0;
            }

            .paginate_button {
                padding: 8px 12px;
                margin: 0 4px;
                border-radius: 4px;
                cursor: pointer;
                text-decoration: none;
                color: #555;
            }

            .paginate_button.current {
                background-color: #007bff;
                color: white;
            }

            .paginate_button.disabled {
                color: #ccc;
                cursor: not-allowed;
            }

            .paginate_button:not(.disabled):hover {
                background-color: #f0f0f0;
            }

            /* Custom SweetAlert2 button styles */
            .swal2-confirm {
                background-color: #00ABC7 !important;
            }

            .swal2-cancel {
                background-color: #FF3131 !important;
            }

            .toggle-switch {
                position: relative;
                display: inline-block;
                width: 40px;
                height: 20px;
                padding-left: 0px !important;
            }

            .toggle-switch {
                padding-left: 50px;
                line-height: 1.25;
            }

            label {
                margin-bottom: 0.5rem;
            }

            .toggle-switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            input:checked+.slider {
                background-color: #00abc7;
            }

            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                transition: 0.4s;
                border-radius: 20px;
            }

            .slider:before {
                position: absolute;
                content: "";
                height: 14px;
                width: 14px;
                left: 3px;
                bottom: 3px;
                background-color: white;
                transition: 0.4s;
                border-radius: 50%;
            }

            input:checked+.slider:before {
                transform: translateX(20px);
            }
        </style>
    @endpush

    <div class="content-body default-height">
        <div class="container-fluid">
            <div class="form-head mb-4 d-flex flex-wrap align-items-center">
                <div class="me-auto">
                    <h2 class="font-w600 mb-0">Dashboard \</h2>
                    <p>Additional Charges List</p>
                </div>
            </div>

            <div class="row mb-4 align-items-center">

                @can('additional_charges.create')
                    <div class="col-xl-3 col-lg-4 mb-4 mb-lg-0">
                        @if (Auth::check() && Auth::user()->can('additional_charges.create'))
                            <a href="{{ route('additional-charges.create') }}"
                                class="btn btn-secondary btn-lg btn-block rounded text-white">+ New Charges</a>
                        @else
                            <button class="btn btn-secondary btn-lg btn-block rounded text-white" disabled>+ New Charges</button>
                        @endif
                    </div>
                    <div class="col-xl-9 col-lg-8">
                @else
                        <div class="col-xl-12"> 
                @endcan

               
                    <div class="card m-0">
                        <div class="card-body py-3 py-md-2">
                            <div class="d-sm-flex d-block align-items-center">
                                <div class="d-flex mb-sm-0 mb-3 me-auto align-items-center">
                                    <svg class="me-2 user-ico mb-1" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0)">
                                            <path
                                                d="M21 24H3C2.73478 24 2.48043 23.8946 2.29289 23.7071C2.10536 23.5196 2 23.2652 2 23V22.008C2.00287 20.4622 2.52021 18.9613 3.47044 17.742C4.42066 16.5227 5.74971 15.6544 7.248 15.274C7.46045 15.2219 7.64959 15.1008 7.78571 14.9296C7.92182 14.7583 7.9972 14.5467 8 14.328V13.322L6.883 12.206C6.6032 11.9313 6.38099 11.6036 6.22937 11.2419C6.07776 10.8803 5.99978 10.4921 6 10.1V5.96201C6.01833 4.41693 6.62821 2.93765 7.70414 1.82861C8.78007 0.719572 10.2402 0.0651427 11.784 5.16174e-06C12.5992 -0.00104609 13.4067 0.158488 14.1603 0.469498C14.9139 0.780509 15.5989 1.2369 16.1761 1.81263C16.7533 2.38835 17.2114 3.07213 17.5244 3.82491C17.8373 4.5777 17.999 5.38476 18 6.20001V10.1C17.9997 10.4949 17.9204 10.8857 17.7666 11.2495C17.6129 11.6132 17.388 11.9426 17.105 12.218L16 13.322V14.328C16.0029 14.5469 16.0784 14.7586 16.2147 14.9298C16.351 15.1011 16.5404 15.2221 16.753 15.274C18.251 15.6548 19.5797 16.5232 20.5298 17.7424C21.4798 18.9617 21.997 20.4624 22 22.008V23C22 23.2652 21.8946 23.5196 21.7071 23.7071C21.5196 23.8946 21.2652 24 21 24ZM4 22H20C19.9954 20.8996 19.6249 19.8319 18.9469 18.9651C18.2689 18.0983 17.3219 17.4816 16.255 17.212C15.6125 17.0494 15.0423 16.6779 14.6341 16.1558C14.2259 15.6337 14.0028 14.9907 14 14.328V12.908C14.0001 12.6428 14.1055 12.3885 14.293 12.201L15.703 10.792C15.7965 10.7026 15.8711 10.5952 15.9221 10.4763C15.9731 10.3574 15.9996 10.2294 16 10.1V6.20001C16.0017 5.09492 15.5671 4.03383 14.7907 3.24737C14.0144 2.46092 12.959 2.01265 11.854 2.00001C10.8264 2.04117 9.85379 2.47507 9.1367 3.21225C8.41962 3.94943 8.01275 4.93367 8 5.96201V10.1C7.99979 10.2266 8.0249 10.352 8.07384 10.4688C8.12278 10.5856 8.19458 10.6914 8.285 10.78L9.707 12.2C9.89455 12.3875 9.99994 12.6418 10 12.907V14.327C9.99724 14.9896 9.77432 15.6325 9.3663 16.1545C8.95827 16.6766 8.3883 17.0482 7.746 17.211C6.67872 17.4804 5.73137 18.0972 5.05318 18.9642C4.37498 19.8313 4.00447 20.8993 4 22Z"
                                                fill="#000" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0">
                                                <rect width="24" height="24" fill="white" />
                                            </clipPath>
                                        </defs>
                                    </svg>
                                    <div class="media-body">
                                        <p class="mb-1 fs-12">Total Charges</p>
                                        <h3 class="mb-0 font-w600 fs-22">{{ $totalCharges }} Charges</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive rounded">
                        <table id="charges-table" class="table customer-table display mb-4 fs-14 card-table">
                            <thead>
                                <tr>
                                    <th>Charges Name</th>
                                    <th>Charges Amount</th>
                                    <th>Charges Type</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                var table = $('#charges-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('additional-charges.index') }}",
                    columns: [
                        { data: 'charge_name', name: 'charge_name' },
                        { data: 'charge_amount', name: 'charge_price' },
                        { data: 'charge_type', name: 'charge_type' },
                        { data: 'status', name: 'status' },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                        { data: 'created_at', name: 'created_at', visible: false }
                    ],
                    order: [[5, 'desc']],
                    language: {
                        paginate: {
                            next: '<i class="fa fa-angle-double-right"></i>',
                            previous: '<i class="fa fa-angle-double-left"></i>'
                        }
                    },
                    drawCallback: function (settings) {
                        $('.dataTables_paginate').addClass('paging_simple_numbers');
                        $('.paginate_button').each(function () {
                            if ($(this).hasClass('current')) {
                                $(this).attr('aria-current', 'page');
                            }
                        });

                        window.initSwalConfirm({
                            triggerSelector: '.delete-charge',
                            title: 'Delete Additional Charge',
                            text: 'Are you sure you want to delete this additional charge? This action cannot be undone.',
                            confirmButtonText: 'Yes, delete charge'
                        });

                        $('.toggle-input').change(function (e) {
                          
                            e.preventDefault();

                            let $toggle = $(this);
                            let newStatus = $toggle.is(':checked');
                            let oldStatus = !newStatus; // The previous status
                            let id = $toggle.attr('data-id');

                            // Immediately revert the toggle - it will be set properly after confirmation
                            $toggle.prop('checked', oldStatus);

                            // Show confirmation dialog
                            Swal.fire({
                                title: 'Confirm Status Change',
                                text: `Are you sure you want to ${newStatus ? 'activate' : 'deactivate'} this additional charge?`,
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, ' + (newStatus ? 'activate' : 'deactivate') + ' it!',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // User confirmed - proceed with status update
                                    updateChargeStatus($toggle, newStatus, id);
                                }
                                // If cancelled, toggle stays in original position (already reverted above)
                            });
                        });

                        function updateChargeStatus($toggle, status, id) {
                            // Disable the toggle while processing
                            $toggle.prop('disabled', true);

                            $.ajax({
                                type: "PUT",
                                url: "{{ route('additional-charges.status') }}",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data: {
                                    chargesId: id,
                                    status: status,
                                },
                                success: function (response) {
                                    console.log('Status update response:', response);
                                    
                                    if (response.error) {
                                        // Show error message
                                        Swal.fire({
                                            title: 'Error!',
                                            text: response.message,
                                            icon: 'error',
                                            confirmButtonText: 'OK'
                                        });
                                    } else {
                                        // Update the toggle to the new status
                                        $toggle.prop('checked', response.new_status);
                                        
                                        // Show success message
                                        Swal.fire({
                                            title: 'Success!',
                                            text: `Additional charge has been ${response.new_status ? 'activated' : 'deactivated'} successfully.`,
                                            icon: 'success',
                                            timer: 2000,
                                            showConfirmButton: false
                                        });
                                        
                                        console.log('Status updated successfully to:', response.new_status);
                                    }
                                },
                                error: function (xhr, status, error) {
                                    console.error('AJAX Error:', {xhr, status, error});
                                    console.error('Response Text:', xhr.responseText);
                                    
                                    let errorMessage = 'An error occurred while updating the status.';
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                    
                                    // Show error message
                                    Swal.fire({
                                        title: 'Error!',
                                        text: errorMessage,
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                },
                                complete: function() {
                                    // Re-enable the toggle
                                    $toggle.prop('disabled', false);
                                }
                            });
                        }
                    }
                });
            });
        </script>
    @endpush

@endsection