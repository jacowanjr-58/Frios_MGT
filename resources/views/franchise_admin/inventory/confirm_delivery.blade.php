{{-- /**
 * Confirm Delivery View for Franchise Admin
 *
 * This view allows franchise admins to confirm the delivery of an order,
 * including entering received quantities and any discrepancies.
 *
 * @package FranchiseAdmin
 */ --}}

{{--
    Confirm Delivery View for Franchise Admin
    (resources/views/franchise_admin/inventory/confirm_delivery.blade.php)
--}}

@extends('layouts.app')

@section('content')
<div class="content-body default-height">
    <div class="container-fluid">
        <div class="row">
            {{-- @dd($order); --}}

            <div class="container mx-auto px-4">
                <h1 class="text-2xl font-bold mb-4">Confirm Delivery for Order #FGP-{{ $order->fgp_ordersID }}</h1>

                <form
                    action="{{ route('franchise.inventory.confirm_delivery.store', ['order' => $order->fgp_ordersID]) }}"
                    method="POST"
                    x-data="{}"
                    class="bg-white p-6 rounded shadow"
                >
                    @csrf

                    {{-- Order header info --}}
                    <div class="mb-6 p-4 bg-gray-100 rounded">
                        <p><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y') }}</p>
                        <p><strong>Franchisee:</strong> {{ $order->franchise->name ?? 'N/A' }}</p>
                        {{-- any other summary fields --}}
                    </div>

                    {{-- Table of line items --}}
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="px-4 py-2 border text-left">Item</th>
                                    <th class="px-4 py-2 border text-center">Ordered Qty</th>
                                    <th class="px-4 py-2 border text-center">Received Qty</th>
                                    <th class="px-4 py-2 border text-left">Discrepancy Notes (if any)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderDetails as $detail)
                                    <tr class="@if($loop->even) bg-white @else bg-gray-50 @endif">
                                        {{-- 1) Display the item name --}}
                                        <td class="px-4 py-2 border">
                                            {{ $detail->item->name ?? "Item #{$detail->fgp_item_id}" }}
                                        </td>

                                        {{-- 2) Show how many were ordered --}}
                                        <td class="px-4 py-2 border text-center">
                                            {{ $detail->unit_number }}
                                        </td>

                                        {{-- 3) Input: how many actually arrived --}}
                                        <td class="px-4 py-2 border text-center">
                                            <input
                                                type="number"
                                                name="received_qty[{{ $detail->id }}]"
                                                id="received_qty_{{ $detail->id }}"
                                                value="{{ old("received_qty.{$detail->id}", $detail->unit_number) }}"
                                                min="0"
                                                class="w-20 px-2 py-1 border rounded text-center"
                                                required
                                            >
                                            @error("received_qty.{$detail->id}")
                                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </td>

                                        {{-- 4) Textarea: any discrepancy note --}}
                                        <td class="px-4 py-2 border">
                                            <textarea
                                                name="notes[{{ $detail->id }}]"
                                                id="notes_{{ $detail->id }}"
                                                rows="2"
                                                class="w-full px-2 py-1 border rounded"
                                                placeholder="If qty differs, explain here…"
                                            >{{ old("notes.{$detail->id}") }}</textarea>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- [Optional] Overall comments (not currently consumed by controller, but left here for future) --}}
                    {{--
                    <div class="mt-6">
                        <label for="overall_notes" class="block font-medium">Overall Comments:</label>
                        <textarea
                            name="overall_notes"
                            id="overall_notes"
                            rows="3"
                            class="w-full px-2 py-1 border rounded"
                            placeholder="E.g. Driver left one box at front door…"
                        >{{ old('overall_notes') }}</textarea>
                    </div>
                    --}}

                    {{-- Submit button --}}
                    <div class="mt-6 flex items-center justify-end">
                        <a href="{{ route('franchise.orderpops.view') }}"
                           class="px-4 py-2 mr-4 text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                            Cancel
                        </a>
                        <button
                            type="submit"
                            class="px-6 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                        >
                            Confirm &amp; Add to Inventory
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
