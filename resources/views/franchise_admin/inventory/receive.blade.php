@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Receive New Stock</h3>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form action="{{ route('franchise.inventory.receive.store') }}" method="POST">
        @csrf

        <!-- Real Flavor Dropdown -->
        <div class="mb-3">
            <label class="form-label">Select Flavor</label>
            <select name="fgp_item_id" id="fgp_item_id" class="form-select">
                <option value="">-- Choose Existing Pop Flavor --</option>
                @foreach($deliveredPopFlavors as $flavor)
                    <option value="{{ $flavor->fgp_item_id }}">
                        {{ $flavor->name }}
                    </option>
                @endforeach
            </select>
            <div class="form-text">* Leave blank if adding a custom item below.</div>
        </div>

        <!-- “New Custom Item” -->
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="is_custom" name="is_custom"
                   value="1" {{ old('is_custom') ? 'checked' : '' }}>
            <label class="form-check-label" for="is_custom">
                This is a custom item (not in delivered flavors)
            </label>
        </div>

        <!-- Custom Item Name -->
        <div class="mb-3" id="custom-name-wrapper" style="display: none;">
            <label class="form-label">Custom Item Name</label>
            <input type="text" name="custom_item_name" class="form-control"
                   value="{{ old('custom_item_name') }}" placeholder="e.g. ‘Strawberry Ice Cream Cone’">
            <div class="form-text">* Provide a name for this new custom inventory line.</div>
        </div>

        <!-- Quantity Received -->
        <div class="mb-3">
            <label class="form-label">Quantity Received</label>
            <input type="number" name="quantity" class="form-control"
                   value="{{ old('quantity', 1) }}" min="1">
        </div>

        <!-- Reference (PO #) -->
        <div class="mb-3">
            <label class="form-label">Reference / Notes</label>
            <input type="text" name="reference" class="form-control"
                   value="{{ old('reference') }}" placeholder="e.g. PO #12345">
        </div>

        <button type="submit" class="btn btn-primary">Add to Inventory</button>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const isCustomCheckbox = document.getElementById("is_custom");
        const customNameWrapper = document.getElementById("custom-name-wrapper");
        const flavorSelect = document.getElementById("fgp_item_id");

        function toggleCustomField() {
            if (isCustomCheckbox.checked) {
                customNameWrapper.style.display = "block";
                flavorSelect.disabled = true;
            } else {
                customNameWrapper.style.display = "none";
                flavorSelect.disabled = false;
            }
        }
        isCustomCheckbox.addEventListener("change", toggleCustomField);

        toggleCustomField();
    });
</script>
@endsection
