<div class="d-flex align-items-center">
    <div class="me-3">
        <input type="number" name="total_cases_{{ $master->inventory_id }}" 
            id="total_cases_{{ $master->inventory_id }}"
            value="{{ $master->cases }}" min="0" 
            class="form-control case-input"
            data-id="{{ $master->inventory_id }}"
            style="width: 12ch">
        <div class="small">({{ $master->split_factor }} units/case)</div>
    </div>
    <div>
        <input type="number" name="total_units_{{ $master->inventory_id }}" 
            id="total_units_{{ $master->inventory_id }}"
            value="{{ $master->units }}" min="0" 
            class="form-control unit-input"
            data-id="{{ $master->inventory_id }}"
            style="width: 12ch">
    </div>
    <input type="hidden" name="split_factor_{{ $master->inventory_id }}"
        id="split_factor_{{ $master->inventory_id }}" 
        value="{{ $master->split_factor }}">
    <input type="hidden" name="total_quantity_{{ $master->inventory_id }}"
        id="total_quantity_{{ $master->inventory_id }}" 
        value="{{ $master->total_quantity }}"
        class="form-control" readonly>
</div> 