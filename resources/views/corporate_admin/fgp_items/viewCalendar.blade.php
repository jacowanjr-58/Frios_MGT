@extends('layouts.app')

@section('content')

<div class=" content-body default-height">
    <!-- row -->
    <div class="container-fluid">

       <div class="container mx-auto py-6">
            <h2 class="text-2xl mb-4">Flavor Availability Calendar</h2>

            <div class="space-y-4">
                @foreach($months as $num => $label)
                <div x-data="{ open: false }" class="border rounded shadow p-4">
                    <div class="flex justify-between items-center cursor-pointer" @click="open = !open">
                        <h3 class="text-xl font-bold">{{ $label }}</h3>
                        <svg :class="{'rotate-180': open}" class="w-6 h-6 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                    <div x-show="open" x-transition class="mt-4">
                        @if($flavorsByMonth[$num]->count())
                        <ul class="space-y-2">
                            @foreach($flavorsByMonth[$num] as $flavor)
                            <li class="flex items-center space-x-3">
                                @if($flavor->image1)
                                <img src="{{ asset('storage/'.$flavor->image1) }}" alt="{{ $flavor->name }}"
                                    class="w-10 h-10 object-cover rounded">
                                @endif
                                <span>{{ $flavor->name }}</span>
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <p class="text-gray-500">No pops available.</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>  </div>
    </div>
</div>
@endsection
