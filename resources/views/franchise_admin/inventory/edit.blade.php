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

                <!-- Stock On Hand -->
                <div class="mb-3">
                    <label class="form-label">Stock On Hand <span class="text-danger">*</span></label>
                    <input type="number"
                           name="total_quantity"
                           class="form-control @error('total_quantity') is-invalid @enderror"
                           value="{{ old('total_quantity', $inventoryMaster->total_quantity) }}"
                           placeholder="Enter quantity">
                    @error('total_quantity') <div class="text-danger">{{ $message }}</div> @enderror
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

                <!-- Location Dropdown -->
                <div class="mb-3">
                    <label class="form-label">Location (Optional)</label>
                    <select name="locations_ID"
                            class="form-control @error('locations_ID') is-invalid @enderror">
                        <option value="">None</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->locations_ID }}"
                                {{ old('locations_ID', $inventoryMaster->locations_ID) == $loc->locations_ID ? 'selected' : '' }}>
                                {{ $loc->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('locations_ID') <div class="text-danger">{{ $message }}</div> @enderror
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

                <button type="submit" class="btn btn-primary">Update Inventory</button>
            </form>
        </div>
    </div>
</div>
@endsection
