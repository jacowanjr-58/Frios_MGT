@extends('layouts.app')
@section('content')
    <script>
        window.eventsData = @json($events);
        "use strict";
        document.addEventListener('DOMContentLoaded', function() {

            // Check if eventsData is defined and accessible
            if (typeof window.eventsData !== 'undefined' && window.eventsData.length > 0) {
                console.log("Events Data:", window.eventsData); // This should log the events array
            } else {
                console.error("eventsData is not defined or is empty!");
            }

            /* initialize the external events */
            var containerEl = document.getElementById('external-events');
            new FullCalendar.Draggable(containerEl, {
                itemSelector: '.external-event',
                eventData: function(eventEl) {
                    return {
                        title: eventEl.innerText.trim()
                    }
                }
            });

            /* initialize the calendar */
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },

                selectable: true,
                selectMirror: true,
                // select: function(arg) {
                //     var selectedDate = arg.startStr; // 'YYYY-MM-DD' format
                //     window.location.href = 'create?date=' + selectedDate;
                // },

                editable: true,
                droppable: true, // allows things to be dropped onto the calendar
                drop: function(arg) {
                    // is the "remove after drop" checkbox checked?
                    if (document.getElementById('drop-remove').checked) {
                        // if so, remove the element from the "Draggable Events" list
                        arg.draggedEl.parentNode.removeChild(arg.draggedEl);
                    }
                },

                initialDate: '2025-05-01',
                weekNumbers: true,
                navLinks: true,
                nowIndicator: true,

                events: window.eventsData.map(function(event) {
                    return {
                        id: event.id,
                        title: event.event_type ?
                            event.event_name + " - " + event.event_type :
                            event.event_name,
                        start: event.start_date,
                        end: event.end_date,
                        className: event.className || ""
                    };
                }),
                eventClick: function(info) {
                    // Redirect to the correct URL
                    window.location.href = '/events/' + info.event.id + '/view';
                }


            });

            calendar.render();

        });
    </script>
    <div class=" content-body default-height">
        <div class="container-fluid">



            <div class="row">
            <div class="col-md-12">
                <div style="float: right;">
                    <a href="{{ route('events.report') }}" class="mb-3 btn btn-secondary btn-sm">Report</a>
                </div>
            </div>
                <div style="display: none;" class="col-xl-3 col-xxl-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-intro-title">Events</h4>

                            <div class="">
                                <div id="external-events" class="my-3">
                                    <p>Click in the calendar to create events.</p>
                                    @foreach ($uniqueEvents as $index => $badgeEvent)
                                        <div
                                            class="cursor-pointer p-3 mt-3
                                        {{ $badgeEvent->event_status == 'scheduled'
                                            ? 'btn-outline-yellow'
                                            : ($badgeEvent->event_status == 'tentative'
                                                ? 'bg-success'
                                                : ($badgeEvent->event_status == 'staffed'
                                                    ? 'btn-danger'
                                                    : '')) }} light">
                                            <i class="fa fa-move"></i>{{ $badgeEvent->event_name }}
                                        </div>
                                    @endforeach
                                    {{-- <div class="external-event btn-primary light" data-class="bg-primary"><i class="fa fa-move"></i><span>New Theme Release</span></div>
                                <div class="external-event btn-warning light" data-class="bg-warning"><i class="fa fa-move"></i>My Event
                                </div>
                                <div class="external-event btn-danger light" data-class="bg-danger"><i class="fa fa-move"></i>Meet manager</div>
                                <div class="external-event btn-info light" data-class="bg-info"><i class="fa fa-move"></i>Create New theme</div>
                                <div class="external-event btn-dark light" data-class="bg-dark"><i class="fa fa-move"></i>Project Launch</div>
                                <div class="external-event btn-secondary light" data-class="bg-secondary"><i class="fa fa-move"></i>Meeting</div> --}}
                                </div>
                                <!-- checkbox -->
                                {{-- <div class="checkbox form-check checkbox-event custom-checkbox pt-3 pb-5">
                                <input type="checkbox" class="form-check-input" id="drop-remove">
                                <label class="form-check-label" for="drop-remove">Remove After Drop</label>
                            </div> --}}
                                {{-- <a href="javascript:void()" data-bs-toggle="modal" data-bs-target="#add-category" class="btn btn-primary btn-event w-100">
                                <span class="align-middle"><i class="ti-plus"></i></span> Create New
                            </a> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-xxl-12">
                    <div class="card">
                        <div class="card-body">
                            <div id="calendar" class="app-fullcalendar"></div>
                        </div>
                    </div>
                </div>
                <!-- BEGIN MODAL -->
                <div class="modal fade none-border" id="event-modal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title"><strong>Add New Event</strong></h4>
                            </div>
                            <div class="modal-body"></div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-success save-event waves-effect waves-light">Create
                                    event</button>

                                <button type="button" class="btn btn-danger delete-event waves-effect waves-light"
                                    data-bs-toggle="modal">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal Add Category -->
                <div class="modal fade none-border" id="add-category">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title"><strong>Add a category</strong></h4>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="control-label form-label">Category Name</label>
                                            <input class="form-control form-white" placeholder="Enter name" type="text"
                                                name="category-name">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label form-label">Choose Category Color</label>
                                            <select class="form-control default-select wide form-white"
                                                data-placeholder="Choose a color..." name="category-color">
                                                <option value="success">Success</option>
                                                <option value="danger">Danger</option>
                                                <option value="info">Info</option>
                                                <option value="pink">Pink</option>
                                                <option value="primary">Primary</option>
                                                <option value="warning">Warning</option>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn  btn-danger light" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary waves-effect waves-light save-category"
                                    data-bs-toggle="modal">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
