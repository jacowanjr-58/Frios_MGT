
@extends('layouts.app')

@section('content')

{{-- May want to even loop over multiple orders --}}
{{-- Example: $shipments = \App\Models\UpsShipment::where('fgp_ordersID', $order->fgp_ordersID)->get(); --}}


@foreach($shipments as $shipment)
    <div class="card mb-4">
        <div class="card-header">
            <strong>Box {{ $loop->iteration }}</strong>
            <span class="float-end">Tracking #: {{ $shipment->tracking_number }}</span>
        </div>
        <div class="card-body">
            <h6>Contents:</h6>
            <ul>
                @php
                    $contents = is_string($shipment->box_contents)
                        ? json_decode($shipment->box_contents, true)
                        : $shipment->box_contents;
                @endphp
                @foreach($contents as $item)
                    <li>
                        Flavor: <strong>{{ $item['flavor'] }}</strong>
                        @if(!empty($item['sku']))
                            (SKU: {{ $item['sku'] }})
                        @endif
                        {{-- Optionally show detail_id --}}
                        {{-- <span class="text-muted">[Detail ID: {{ $item['detail_id'] }}]</span> --}}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endforeach
@endsection
