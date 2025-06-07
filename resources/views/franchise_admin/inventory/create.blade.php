@extends('layouts.app')

@section('content')

   <div class="content-body default-height">
    <!-- row -->
    <div class="container">
    <div class="row mb-4">
        <div class="col">
            <h3>Create Inventory Record</h3>
        </div>
        <div class="col text-end">
            <a href="{{ route('franchise.inventory.index') }}" class="btn btn-secondary">
                ← Back to List
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('franchise.inventory.store') }}" method="POST">
                @csrf

                <!-- Corporate vs Custom -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Corporate Item</label>
                        <select name="fgp_item_id" id="fgp_item_id"
                                class="form-control @error('fgp_item_id') is-invalid @enderror">
                            <option value="">-- Select Corporate Item --</option>
                            @foreach($fgpItems as $item)
                                <option value="{{ $item->fgp_item_id }}"
                                    {{ old('fgp_item_id') == $item->fgp_item_id ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('fgp_item_id')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Custom Item Name</label>
                        <input type="text" name="custom_item_name"
                               class="form-control @error('custom_item_name') is-invalid @enderror"
                               value="{{ old('custom_item_name') }}"
                               placeholder="e.g. Bottled Water">
                        @error('custom_item_name')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- Stock Count Date & Total Quantity -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Stock Count Date <span class="text-danger">*</span></label>
                        <input type="date" name="stock_count_date"
                               class="form-control @error('stock_count_date') is-invalid @enderror"
                               value="{{ old('stock_count_date', $today) }}">
                        @error('stock_count_date')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Total Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="total_quantity" min="0"
                               class="form-control @error('total_quantity') is-invalid @enderror"
                               value="{{ old('total_quantity', 0) }}">
                        @error('total_quantity')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- Allocation Grid -->
                <h4 class="mt-4">Allocate Quantities by Location</h4>
                <table class="table table-bordered mb-3">
                    <thead>
                        <tr>
                            <th>Location</th>
                            <th class="text-center">Allocated Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locations as $loc)
                            <tr>
                                <td>{{ $loc->name }}</td>
                                <td class="text-center">
                                    <input type="number"
                                           name="allocations[{{ $loc->locations_ID }}]"
                                           class="form-control allocation-input @error('allocations.' . $loc->locations_ID) is-invalid @enderror"
                                           value="{{ old('allocations.' . $loc->locations_ID, 0) }}"
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

                <button type="submit" class="btn btn-success" id="submit-btn">Create Inventory</button>
            </form>
        </div>
    </div>
</div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalInput = document.getElementById('total_quantity');
    const allocInputs = [...document.querySelectorAll('.allocation-input')];
    const errorDiv = document.getElementById('allocation-error');
    const submitBtn = document.getElementById('submit-btn');

    function validateAllocations() {
        const total = parseInt(totalInput.value) || 0;
        const sum = allocInputs.reduce((a, i) => a + (parseInt(i.value) || 0), 0);
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
