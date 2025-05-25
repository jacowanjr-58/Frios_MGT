 @forelse ($events as $index=>$event)
     <div class="media mb-5 align-items-center event-list">
         <div class="p-3 text-center me-3 date-bx bgl-primary">
             <h2 class="mb-0 text-secondary fs-28 font-w600">{{ \Carbon\Carbon::parse($event->start_date)->format('d') }}
             </h2>
             <h5 class="mb-1 text-black">{{ \Carbon\Carbon::parse($event->start_date)->format('D') }}</h5>
         </div>
         <div class="media-body px-0">
             <h6 class="mt-0 mb-3 fs-14">
                @if (auth()->user()->role == 'franchise_admin' || auth()->user()->role == 'franchise_manager')
                    <a href="{{ route('franchise.events.view', $event->id) }}" class="text-black">
                        {{ $event->event_name }}
                    </a>
                @elseif(auth()->user()->role == 'franchise_staff')
                    <a href="{{ route('franchise_staff.events.view', $event->id) }}" class="text-black">
                        {{ $event->event_name }}
                    </a>
                @else
                <a class="text-black"
                     href="{{ route('corporate_admin.events.view', $event->id) }}">{{ $event->event_name }}</a>
                @endif
                    </h6>

         </div>
     </div>
 @empty
 @endforelse
