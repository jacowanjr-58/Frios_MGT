@forelse ($events as $index=>$event)
    <div class="media mb-4 align-items-center event-list">
        <div class="p-3 text-center me-3 date-bx bgl-primary rounded">
            <h2 class="mb-0 text-primary fs-24 font-w600">
                {{ \Carbon\Carbon::parse($event->start_date)->format('d') }}
            </h2>
            <h6 class="mb-0 text-muted">{{ \Carbon\Carbon::parse($event->start_date)->format('M') }}</h6>
        </div>
        <div class="media-body px-0">
            <h6 class="mt-0 mb-2 fs-14 font-w600">
               @if (auth()->user()->role == 'franchise_admin' || auth()->user()->role == 'franchise_manager')
                   <a href="{{ route('franchise.events.view', ['franchise' => request()->route('franchise'), 'id' => $event->id]) }}" class="text-black text-decoration-none">
                       {{ $event->event_name }}
                   </a>
               @elseif(auth()->user()->role == 'franchise_staff')
                   <a href="{{ route('franchise_staff.events.view', $event->id) }}" class="text-black text-decoration-none">
                       {{ $event->event_name }}
                   </a>
               @elseif(auth()->user()->role == 'corporate_admin')
                   @if(request()->route('franchisee'))
                       <a href="{{ route('franchise.events.view', ['franchise' => request()->route('franchise'), 'id' => $event->id]) }}" class="text-black text-decoration-none">
                           {{ $event->event_name }}
                       </a>
                   @else
                       <a class="text-black text-decoration-none" href="{{ route('events.view', $event->id) }}">{{ $event->event_name }}</a>
                   @endif
               @else
                   <span class="text-black">{{ $event->event_name }}</span>
               @endif
            </h6>
            <div class="d-flex flex-column">
                <small class="text-muted">
                    <i class="fa fa-clock me-1"></i>
                    {{ \Carbon\Carbon::parse($event->start_date)->format('g:i A') }} - 
                    {{ \Carbon\Carbon::parse($event->end_date)->format('g:i A') }}
                </small>
                <small class="text-muted mt-1">
                    <i class="fa fa-calendar me-1"></i>
                    {{ \Carbon\Carbon::parse($event->start_date)->format('l, M j, Y') }}
                </small>
                @if($event->event_status)
                    <span class="badge badge-{{ $event->event_status == 'scheduled' ? 'success' : ($event->event_status == 'tentative' ? 'warning' : 'info') }} badge-sm mt-2">
                        {{ ucfirst($event->event_status) }}
                    </span>
                @endif
            </div>
        </div>
    </div>
@empty
    <div class="text-center py-4">
        <div class="text-muted">
            <i class="fa fa-calendar fa-3x mb-3"></i>
            <h6>No upcoming events</h6>
            <p class="mb-0">There are no scheduled events at this time.</p>
        </div>
    </div>
@endforelse
