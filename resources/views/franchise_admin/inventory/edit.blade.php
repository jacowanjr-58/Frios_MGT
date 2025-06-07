@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h3>Edit Inventory Record</h3>
        </div>
        <div class="col text-end">
            <a href="{{ route('franchise.inventory.index') }}" class="btn btn-secondary">
                ← Back to List
            </a>
        </div>
    </div>
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <div class="card">
        <div class="card-body">
            <form action="{{ route('franchise.inventory.update', $inventoryMaster->inventory_id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Item Label (read-only) -->
                <div class="mb-3">
                    <label class="form-label">Item</label>
                    <input type="hidden" name="fgp_item_id" value="{{ $inventoryMaster->fgp_item_id }}">
                    <p class="form-control-plaintext">{{ $inventoryMaster->item_name }}</p>
                </div>

                <!-- Stock Count Date -->
                <div class="mb-3">
                    <label class="form-label">Stock Count Date <span class="text-danger">*</span></label>
                    <input type="date"
                           name="stock_count_date"
                           class="form-control @error('stock_count_date') is-invalid @enderror"
                           value="{{ old('stock_count_date', \Carbon\Carbon::parse($inventoryMaster->stock_count_date)->format('Y-m-d')) }}">
                    @error('stock_count_date') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <!-- Pops On Hand -->
                <div class="mb-3">
                    <label class="form-label">Pops On Hand</label>
                    <input type="number"
                           name="pops_on_hand"
                           class="form-control @error('pops_on_hand') is-invalid @enderror"
                           value="{{ old('pops_on_hand', $inventoryMaster->pops_on_hand) }}"
                           placeholder="Enter number of pops">
                    @error('pops_on_hand') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <!-- Wholesale Price Case -->
                <div class="mb-3">
                    <label class="form-label">Wholesale Price (Case)</label>
                    <input type="text"
                           name="whole_sale_price_case"
                           class="form-control @error('whole_sale_price_case') is-invalid @enderror"
                           value="{{ old('whole_sale_price_case', $inventoryMaster->whole_sale_price_case) }}"
                           placeholder="0.00">
                    @error('whole_sale_price_case') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <!-- Retail Price Pop -->
                <div class="mb-3">
                    <label class="form-label">Retail Price (Pop)</label>
                    <input type="text"
                           name="retail_price_pop"
                           class="form-control @error('retail_price_pop') is-invalid @enderror"
                           value="{{ old('retail_price_pop', $inventoryMaster->retail_price_pop) }}"
                           placeholder="0.00">
                    @error('retail_price_pop') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <!-- Allocation Grid -->
                <h4 class="mt-4">Allocate Quantities by Location</h4>
                <div class="mb-3">
                    <label class="form-label">Total Quantity <span class="text-danger">*</span></label>
                    <input type="number"
                           id="total_quantity"
                           name="total_quantity"
                           class="form-control @error('total_quantity') is-invalid @enderror"
                           value="{{ old('total_quantity', $inventoryMaster->total_quantity) }}">
                    @error('total_quantity') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Location</th>
                            <th class="text-center">Allocated Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locations as $loc)
                            @php
                                $defaultQty = $existingAllocations[$loc->locations_ID] ?? 0;
                            @endphp
                            <tr>
                                <td>{{ $loc->name }}</td>
                                <td class="text-center">
                                    <input type="number"
                                           name="allocations[{{ $loc->locations_ID }}]"
                                           class="form-control allocation-input @error('allocations.' . $loc->locations_ID) is-invalid @enderror"
                                           value="{{ old('allocations.' . $loc->locations_ID, $defaultQty) }}"
                                           min="0">
                                    @error('allocations.' . $loc->locations_ID) <div class="text-danger">{{ $message }}</div> @enderror
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div id="allocation-error" class="text-danger mb-3" style="display:none;">
                    The sum of allocated quantities must equal total quantity.
                </div>


                <button type="submit" class="btn btn-primary" id="submit-btn">Update Inventory</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalInput = document.getElementById('total_quantity');
        const allocInputs = Array.from(document.querySelectorAll('.allocation-input'));
        const errorDiv = document.getElementById('allocation-error');
        const submitBtn = document.getElementById('submit-btn');

        function validateAllocations() {
            const total = parseInt(totalInput.value) || 0;
            const sum = allocInputs.reduce((acc, input) => acc + (parseInt(input.value) || 0), 0);
            if (sum !== total) {
                errorDiv.style.display = 'block';
                submitBtn.disabled = true;
            } else {
                errorDiv.style.display = 'none';
                submitBtn.disabled = false;
            }
        }

        totalInput.addEventListener('input', validateAllocations);
        allocInputs.forEach(i => i.addEventListener('input', validateAllocations));
        validateAllocations();
    });
</script>
@endsection
