@extends('layouts.app')
@section('content')
    <style>
        .status-tentative {
            background-color: #f87171;
            color: white;
            padding: 2px;
            border-radius: 5px;
        }

        .status-scheduled {
            background-color: #fde68a;
            color: black;
            padding: 2px;
            border-radius: 5px;
        }

        .status-staffed {
            background-color: #4ade80;
            color: white;
            padding: 2px;
            border-radius: 5px;
        }
    </style>
    <div class="content-body default-height p-5 mt-5">
        <div class="container-fluid rounded border p-5 bg-white">
            <div class="d-flex justify-content-between">
                <h1 style="font-size: 28px; font-weight: bold">
                    Event View : {{ $event->event_name }}
                </h1>

                <a href="javascript:history.back()" class="btn btn-primary" style="margin-right: 50px;">
                    Back
                </a>

            </div>


            <div class="row mt-5">
                <label class="mt-1"><b>Start Date: </b>
                    @if (!empty($event->start_date))
                    {{ date('d M Y h:i A', strtotime($event->start_date)) }}
                    @else
                        -
                    @endif
                </label>

                <label class="mt-1"><b>End Date: </b>
                    @if (!empty($event->end_date))
                    {{ date('d M Y h:i A', strtotime($event->end_date)) }}
                    @else
                        -
                    @endif
                </label>

                <label class="mt-1"><b>Type: </b>
                    @if (!empty($event->event_type))
                        {{ ucfirst($event->event_type) }}
                    @else
                        -
                    @endif
                </label>

                <label class="mt-1"><b>Status: </b>
                    @if (!empty($event->event_status))
                        <span class="{{
                            $event->event_status == 'tentative' ? 'status-tentative' :
                            ($event->event_status == 'scheduled' ? 'status-scheduled' :
                            ($event->event_status == 'staffed' ? 'status-staffed' : '')) }}">
                            {{ ucfirst($event->event_status) }}
                        </span>
                    @else
                        -
                    @endif
                </label>

                <label class="mt-1"><b>Planned payment: </b>
                    @if (!empty($event->planned_payment))
                        {{ ucfirst($event->planned_payment) }}
                    @else
                        -
                    @endif
                </label>

                @php
                    $resources = json_decode($event->resources_selection, true);
                @endphp
                <label class="mt-1"><b>Resources Needed: </b>
                    @if (!empty($resources))
                        {{ implode(', ', array_map(fn($key) => ucfirst(str_replace('_', ' ', $key)), array_keys($resources))) }}
                    @else
                        -
                    @endif
                </label>

                @php
                    $staffIds = json_decode($event->staff_assigned);
                @endphp
                <label class="mt-1"><b>Staff Assigned: </b>
                    @if (is_array($staffIds) && !empty($staffIds))
                        {{ \App\Models\User::whereIn('user_id', $staffIds)->pluck('name')->implode(', ') }}
                    @else
                        -
                    @endif
                </label>

                <label class="mt-1"><b>Customer: </b>
                    @if (!empty($event->customer?->name))
                        {{ $event->customer->name }}
                    @else
                        -
                    @endif
                </label>

                <label class="mt-1"><b>Expected Sales: </b>
                    @if (!empty($event->expected_sales))
                        ${{ number_format($event->expected_sales) }}
                    @else
                        -
                    @endif
                </label>

                <label class="mt-1"><b>Actual Sales: </b>
                    @if (!empty($event->actual_sales))
                        ${{ number_format($event->actual_sales) }}
                    @else
                        -
                    @endif
                </label>

                <label class="mt-1"><b>Costs: </b>
                    @if (!empty($event->costs))
                        ${{ number_format($event->costs) }}
                    @else
                        -
                    @endif
                </label>

                <label class="mt-1"><b>Note: </b>
                    @if (!empty($event->event_notes))
                        {{ $event->event_notes }}
                    @else
                        -
                    @endif
                </label>
            </div>

        </div>
    </div>


    <div class="content-body default-height p-5">
        <div class="container-fluid rounded border p-5 bg-white">
            <div class="d-flex justify-content-between">
                <h1 style="font-size: 28px; font-weight: bold">
                    Case Flavors Allocated
                </h1>
            </div>

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
                                {{ $eventItem->orderableItem->name ?? '-' }}
                            </td>
                            <td>
                                {{ isset($orderDetail->unit_number) ? $orderDetail->unit_number : '-' }}
                            </td>
                            <td>
                                {{ isset($orderDetail->unit_number, $eventItem->quantity) ? $orderDetail->unit_number - $eventItem->quantity : '' }}
                            </td>
                            <td>
                                @if($pop && $pop->created_at)
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
