@extends('layouts.app')
@section('content')
    <div class="content-body default-height p-5 mt-5">
        <div class="container rounded border p-5 bg-white">
            <div class="d-flex justify-content-between">
                <h1 style="font-size: 28px; font-weight: bold">
                    @php
                        $currentDate = \Carbon\Carbon::now()->startOfMonth()->format('F 1, Y');
                    @endphp

                    Event Inventory Report (as of {{ $currentDate }})

                </h1>
            </div>

            <form action="{{ route('franchise.events.report') }}" method="GET">
                <div class="mt-5 mb-3 flex">
                    <!-- Month Input -->
                    <input type="month" name="month_year" class="w-25 form-control"
                        value="{{ request('month_year', \Carbon\Carbon::now()->format('Y-m')) }}">

                    <div style="margin-left: 10px;">
                        <button type="submit" class="btn btn-primary custom-hover text-primary">Generate Report</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">

                <table id="example5" class="table customer-table display mb-4 fs-14 card-table">
                    <thead>
                        <tr>
                            <th>Orderable flover</th>
                            <th>Quantity</th>
                            <th>On hand flover</th>
                            <th>Quantity</th>
                            <th>Shortage / Overage</th>
                            <th>Month Avaliable to Order</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($eventItems as $eventItem)
                            @php
                                $pop = null;

                                if (isset($eventItem->in_stock)) {
                                    $pop = \App\Models\FgpItem::where('fgp_item_id', $eventItem->in_stock)->first();
                                }

                                $orderDetail = null;
                                if (isset($eventItem->orderable)) {
                                    $orderDetail = \DB::table('fgp_order_details')
                                        ->where('fgp_item_id', $eventItem->orderable)
                                        ->first();
                                }

                                $orderable = \DB::table('fgp_order_details')
                                    ->where('id', $eventItem->orderable)
                                    ->first();

                                $fgpItem = isset($orderable->fgp_item_id)
                                    ? \App\Models\FgpItem::where('fgp_item_id', $orderable->fgp_item_id)->first()
                                    : null;

                                // dd($orderDetail);

                            @endphp
                            <tr>
                                <td>
                                    {{ $eventItem->fgpItem->name ?? '-' }}
                                </td>
                                <td>
                                    {{ $eventItem->quantity ?: '-' }}
                                </td>
                                <td>
                                    {{ $fgpItem->name ?? '-' }}
                                </td>
                                <td>
                                    {{ isset($orderable->unit_number) ? $orderable->unit_number : '-' }}
                                </td>
                                <td>
                                    {{ isset($orderable->unit_number, $eventItem->quantity) ? $orderable->unit_number - $eventItem->quantity : '' }}
                                </td>
                                <td>
                                    @if ($pop && $pop->created_at)
                                        {{ \Carbon\Carbon::parse($pop->created_at)->month == now()->month
                                            ? \Carbon\Carbon::parse($pop->created_at)->format('d M Y')
                                            : '-' }}
                                    @else
                                        -
                                    @endif

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
