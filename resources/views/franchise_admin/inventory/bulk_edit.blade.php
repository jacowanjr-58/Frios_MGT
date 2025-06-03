
@extends('layouts.app')

@section('content')
<script>
    function checkMismatch(row) {
        const total = parseInt(row.querySelector('[name$="[total_quantity]"]').value) || 0;
        const split = parseInt(row.querySelector('[name$="[split_total_quantity]"]').value) || 0;
        const factor = parseInt(row.getAttribute('data-factor')) || 1;
        const expected = total * factor;
        const splitCell = row.querySelector('[name$="[split_total_quantity]"]');

        row.classList.toggle('bg-yellow-100', expected !== split);
        splitCell.classList.toggle('bg-red-200', expected !== split);
        row.querySelector('.expected-value').textContent = expected;
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.inventory-row').forEach(row => {
            row.querySelectorAll('input').forEach(input => {
                input.addEventListener('input', () => checkMismatch(row));
            });
            checkMismatch(row);
        });

        document.getElementById('locationFilter').addEventListener('change', function() {
            const value = this.value.toLowerCase();
            document.querySelectorAll('.inventory-row').forEach(row => {
                const loc = row.getAttribute('data-location').toLowerCase();
                row.style.display = value === '' || loc === value ? '' : 'none';
            });
        });
    });
</script>

<form method="POST" action="{{ route('corp_admin.inventory.bulk_update') }}">
    @csrf

    <label for="locationFilter" class="block mb-2 text-sm">Filter by Location:</label>
    <select id="locationFilter" class="mb-4 p-1 border rounded">
        <option value="">All Locations</option>
        @foreach($inventoryItems->pluck('location')->unique()->filter()->sort() as $loc)
            <option value="{{ $loc }}">{{ $loc }}</option>
        @endforeach
    </select>

    <table class="min-w-full text-sm text-left">
        <thead>
            <tr>
                <th>SKU</th>
                <th>Name</th>
                <th>Location</th>
                <th>Total Qty</th>
                <th>Split Qty</th>
                <th>Expected</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventoryItems as $item)
                @php
                    $expected = $item->total_quantity * ($item->item->split_factor ?? 1);
                @endphp
                <tr class="inventory-row" data-factor="{{ $item->item->split_factor ?? 1 }}" data-location="{{ $item->location }}">
                    <td>{{ $item->sku }}</td>
                    <td>{{ $item->item->name ?? 'Unknown' }}</td>
                    <td>{{ $item->location }}</td>
                    <td>
                        <input name="inventory[{{ $item->id }}][total_quantity]" type="number"
                               value="{{ $item->total_quantity }}"
                               class="border p-1 rounded w-24">
                    </td>
                    <td>
                        <input name="inventory[{{ $item->id }}][split_total_quantity]" type="number"
                               value="{{ $item->split_total_quantity }}"
                               class="border p-1 rounded w-24">
                    </td>
                    <td class="expected-value">{{ $expected }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <button type="submit" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">Update Inventory</button>
</form>
@endsection
