<div style="max-height: 500px; overflow-y: auto; margin-bottom: 20px;">
    <table class="table table-bordered rounded mt-5" id="dynamicTable">
        <thead>
            <tr>
                <th>Orderable</th>
                <th>In-Stock</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    @if(!empty($pops) && count($pops) > 0)
                    <select name="in_stock[]" class="form-control status-select">
                        @foreach ($pops as $item)
                            <option value="{{ $item->fgp_item_id }}"{{
                                old("in_stock") == $item->fgp_item_id ? 'selected' : '' }}>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                    @error("in_stock")
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                    @else
                    No Orderable available
                    @endif
                </td>
                <td>
                    <select name="orderable[]" class="form-control status-select">
                        @foreach ($orderDetails as $item)
                            <option value="{{ $item->id }}"{{
                                old("orderable") == $item->id ? 'selected' : '' }}>
                                {{ $item->item_name }} - (x{{ $item->unit_number }})
                            </option>
                        @endforeach
                    </select>
                    @error("orderable")
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </td>
                <td>
                    <input type="number" name="quantity[]" placeholder="0" class="form-control"
                        value="{{ old("quantity") }}">
                    @error("quantity")
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </td>
                <td>
                    <span class="btn btn-primary action-btn" onclick="addRow(this)">+</span>
                    <span class="btn btn-danger action-btn" onclick="removeRow(this)">−</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>


<script>
$(document).ready(function() {
    // Dynamically populate options for in_stock and orderable via an AJAX request
    let orderOptions = '';

    @foreach($orderDetails as $item)
        orderOptions += `<option value="{{ $item->id }}">{{ $item->item_name }} - (x{{ $item->unit_number }})</option>`;
    @endforeach

    // Add a new row with values
    window.addRow = function(button) {
        let tableBody = document.getElementById("dynamicTable").getElementsByTagName("tbody")[0];

        // Create a new row
        let newRow = document.createElement('tr');

        // Insert the first (in_stock) column with options
        newRow.innerHTML = `
            <td>
                @if(!empty($pops) && count($pops) > 0)
                    <select name="in_stock[]" class="form-control status-select">
                        @foreach ($pops as $item)
                            <option value="{{ $item->fgp_item_id }}">
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                @else
                    No Orderable available
                @endif
            </td>

            <!-- Insert the second (orderable) column with options -->
            <td>
                <select name="orderable[]" class="form-control status-select">
                    ${orderOptions}  <!-- Use the dynamic orderOptions here -->
                </select>
            </td>

            <!-- Insert the quantity column -->
            <td>
                <input type="number" name="quantity[]" placeholder="0" class="form-control" value="{{ old('quantity') }}">
            </td>

            <!-- Insert the action buttons -->
            <td>
                <span class="btn btn-primary action-btn" onclick="addRow(this)">+</span>
                <span class="btn btn-danger action-btn" onclick="removeRow(this)">−</span>
            </td>
        `;

        // Append the new row to the table body
        tableBody.appendChild(newRow);

        // Re-initialize selectpicker for the newly added row
        initializeSelectpicker();
    };

    // Re-initialize selectpicker for all selects
    function initializeSelectpicker() {
        $('.status-select').selectpicker('refresh');
    }

    // Remove a row
    window.removeRow = function(button) {
        let row = button.closest("tr"); // Get the row containing the clicked button
        if (row) {
            row.remove(); // Remove the row
        }
    };
});

</script>
