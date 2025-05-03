@extends('layouts.app')
@section('content')
<style>
.status-wrapper.status-tentative {
    background-color: #f87171;
    color: white;
    border-radius: 10px;
}

.status-wrapper.status-scheduled {
    background-color: #fde68a;
    color: black;
    border-radius: 10px;
}

.status-wrapper.status-staffed {
    background-color: #4ade80;
    color: white;
    border-radius: 10px;
}

.status-wrapper select {
    border-radius: 6px;
    padding: 6px;
    width: 100%;
    transition: background-color 0.3s ease, color 0.3s ease;
}


</style>
    <div class="content-body default-height p-5 mt-5">
        <div class="container-fluid rounded border p-5 bg-white">
            <div class="d-flex justify-content-between">
                <h1 style="font-size: 28px; font-weight: bold">
                    Events
                </h1>

                {{-- <a href="{{ route('franchise.events.create') }}" class="btn btn-primary">
                    Create Event
                </a> --}}
            </div>
            <table id="example5" class="table customer-table display mb-4 fs-14 card-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Staffed By</th>
                        {{-- <th>All location</th> --}}
                        <th>Customer</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($events as $key => $event)
                        <tr>
                            <td class="status-cell">
                                <div class="status-wrapper {{
                                    $event->event_status == 'tentative' ? 'status-tentative' :
                                    ($event->event_status == 'scheduled' ? 'status-scheduled' :
                                    ($event->event_status == 'staffed' ? 'status-staffed' : ''))
                                }}">
                                    <select
                                        onchange="updateStatusColor(this)"
                                        class="status-select"
                                        data-event-id="{{ $event->id }}"
                                    >
                                        <option value="tentative" {{ $event->event_status == 'tentative' ? 'selected' : '' }}>Tentative</option>
                                        <option value="scheduled" {{ $event->event_status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                        <option value="staffed" {{ $event->event_status == 'staffed' ? 'selected' : '' }}>Staffed</option>
                                    </select>
                                </div>
                            </td>



                            <td>
                                <a href="{{ route('franchise.events.view',$event->id) }}" style="color: inherit; text-decoration: none;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='inherit'">
                                    {{ $event->event_name }}
                                </a>

                            </td>
                            <td>
                                {{ date('d M Y', strtotime($event->start_date)) }}
                            </td>
                            <td>
                                {{ $event->event_type ?: '-' }}
                            </td>
                            <td>
                                @php
                                    $staffIds = json_decode($event->staff_assigned);
                                @endphp

                                @if (is_array($staffIds))
                                    {{ \App\Models\User::whereIn('user_id', $staffIds)->pluck('name')->implode(', ') }}
                                @else
                                    -
                                @endif


                            </td>

                            <td>
                                {{ $event->customer->name ?? '-' }}
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        <!-- Modal trigger button -->
        {{-- <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalId">
            Launch
        </button> --}}

        <!-- Modal Body -->
        <!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
        {{-- <div class="modal fade" id="modalId" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Event required items
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table customer-table display mb-4 fs-14 card-table dataTable no-footer" id="dynamicTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Required Quantity</th>
                                    <th>Available Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($events as $key => $event)
                                    <tr>
                                        <td>
                                            {{ $key + 1 }}
                                        </td>
                                        <td>
                                            {{ $event->name }}
                                        </td>
                                        <td>
                                            {{ date('d M Y', strtotime($event->start_date)) }}
                                        </td>
                                        <td>
                                            {{ date('d M Y', strtotime($event->end_date)) }}
                                        </td>
                                        <td class="d-flex justify-content-around">
                                            <a href="{{ route('franchise.events.compare', ['event' => $event]) }}" class="text-success">
                                                <i class="fas fa-exchange-alt"></i>
                                            </a>
                                            <button class="text-info">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <button class="text-warning">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                            <button class="text-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary text-black text-dark" data-bs-dismiss="modal">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div> --}}

    </div>

    <script>
function updateStatusColor(select) {
    const parentDiv = select.closest('.status-wrapper');
    parentDiv.classList.remove('status-tentative', 'status-scheduled', 'status-staffed');

    const newStatus = select.value;
    const eventId = select.getAttribute('data-event-id');

    if (newStatus === 'tentative') {
        parentDiv.classList.add('status-tentative');
    } else if (newStatus === 'scheduled') {
        parentDiv.classList.add('status-scheduled');
    } else if (newStatus === 'staffed') {
        parentDiv.classList.add('status-staffed');
    }

    fetch("{{ route('franchise.updateStatus') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            event_id: eventId,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Status updated successfully');
        } else {
            console.error('Failed to update status');
        }
    })
    .catch(error => {
        console.error('AJAX error:', error);
    });
}

    </script>
@endsection
