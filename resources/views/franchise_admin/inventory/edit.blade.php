@extends('layouts.app')

@section('content')
<div class="content-body default-height">
  <div class="container">
    <div class="row mb-4">
      <div class="col"><h3>Edit Inventory Record</h3></div>
      <div class="col text-end">
        <a href="{{ route('franchise.inventory.index') }}" class="btn btn-secondary">← Back to List</a>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        @if($errors->any())
          <div class="alert alert-danger"><ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul></div>
        @endif

        <form id="inventory-form"
              action="{{ route('franchise.inventory.update', $inventoryMaster->inventory_id) }}"
              method="POST">
          @csrf
          @method('PUT')

          {{-- Corporate vs. Custom --}}
            <div class="row mb-3">
                <div class="col-12">
                    <label class="form-label">Item</label>
                    <p class="form-control-plaintext">
                        {{ $inventoryMaster->item_name }}
                    </p>
                </div>
            </div>

          {{-- Stock Date + Master Cases/Units + Split Factor + Computed Total --}}
          @php
            $cases = intdiv($inventoryMaster->total_quantity, $inventoryMaster->split_factor);
            $units = $inventoryMaster->total_quantity % $inventoryMaster->split_factor;
          @endphp
          <div class="row mb-3 gx-3 align-items-end">
            <div class="col-md-3">
              <label class="form-label">Stock Count Date <span class="text-danger">*</span></label>
             <input type="date"
       name="stock_count_date"
       class="form-control @error('stock_count_date') is-invalid @enderror"
       value="{{ old('stock_count_date', optional($inventoryMaster->stock_count_date)->format('Y-m-d')) }}">
              @error('stock_count_date')<div class="text-danger">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-2">
              <label class="form-label">Total Cases</label>
              <input type="number"
                     id="master_cases"
                     name="master_cases"
                     class="form-control"
                     value="{{ old('master_cases', $cases) }}"
                     min="0">
            </div>

            <div class="col-md-2">
              <label class="form-label">Total Units</label>
              <input type="number"
                     id="master_units"
                     name="master_units"
                     class="form-control"
                     value="{{ old('master_units', $units) }}"
                     min="0">
            </div>

            <div class="col-md-2">
              <label class="form-label">Split Factor <small>(units/case)</small></label>
              <input type="number"
                     id="split_factor"
                     name="split_factor"
                     class="form-control @error('split_factor') is-invalid @enderror"
                     value="{{ old('split_factor', $inventoryMaster->split_factor) }}"
                     min="1">
              @error('split_factor')<div class="text-danger">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-3">
              <label class="form-label">Total Quantity</label>
              <input type="text"
                     id="display_total_quantity"
                     class="form-control bg-light" readonly>
              <input type="hidden"
                     id="total_quantity"
                     name="total_quantity"
                     value="{{ old('total_quantity', $inventoryMaster->total_quantity) }}">
            </div>
          </div>

          {{-- Cost Fields --}}
          <div class="row mb-4 gx-3">
            <div class="col-md-4">
              <label class="form-label">COGS Case</label>
              <input type="text"
                     name="cogs_case"
                     class="form-control @error('cogs_case') is-invalid @enderror"
                     value="{{ old('cogs_case', $inventoryMaster->cogs_case) }}">
              @error('cogs_case')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
              <label class="form-label">COGS Unit</label>
              <input type="text"
                     name="cogs_unit"
                     class="form-control @error('cogs_unit') is-invalid @enderror"
                     value="{{ old('cogs_unit', $inventoryMaster->cogs_unit) }}">
              @error('cogs_unit')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
              <label class="form-label">Wholesale Case</label>
              <input type="text"
                     name="wholesale_case"
                     class="form-control @error('wholesale_case') is-invalid @enderror"
                     value="{{ old('wholesale_case', $inventoryMaster->wholesale_case) }}">
              @error('wholesale_case')<div class="text-danger">{{ $message }}</div>@enderror

              <label class="form-label mt-3">Wholesale Unit</label>
              <input type="text"
                     name="wholesale_unit"
                     class="form-control @error('wholesale_unit') is-invalid @enderror"
                     value="{{ old('wholesale_unit', $inventoryMaster->wholesale_unit) }}">
              @error('wholesale_unit')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="row mb-4 gx-3">
            <div class="col-md-6">
              <label class="form-label">Retail Case</label>
              <input type="text"
                     name="retail_case"
                     class="form-control @error('retail_case') is-invalid @enderror"
                     value="{{ old('retail_case', $inventoryMaster->retail_case) }}">
              @error('retail_case')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Retail Unit</label>
              <input type="text"
                     name="retail_unit"
                     class="form-control @error('retail_unit') is-invalid @enderror"
                     value="{{ old('retail_unit', $inventoryMaster->retail_unit) }}">
              @error('retail_unit')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Allocation Grid --}}
          <h4 class="mt-4">Allocate by Location</h4>
          <table class="table table-bordered mb-3">
            <thead>
              <tr>
                <th>Location</th>
                <th class="text-center">Cases</th>
                <th class="text-center">Units</th>
              </tr>
            </thead>
            <tbody>
              @foreach($locations as $loc)
                @php
                  $exist = $existingAllocations[$loc->locations_ID] ?? ['cases'=>0,'units'=>0];
                @endphp
                <tr>
                  <td>{{ $loc->name }}</td>
                  <td class="text-center">
                    <input type="number"
                           name="allocations[{{ $loc->locations_ID }}][cases]"
                           class="form-control alloc-cases"
                           value="{{ old('allocations.' . $loc->locations_ID . '.cases', $exist['cases']) }}"
                           min="0"
                           style="width:4rem;"
                           placeholder="Cases">
                  </td>
                  <td class="text-center">
                    <input type="number"
                           name="allocations[{{ $loc->locations_ID }}][units]"
                           class="form-control alloc-units"
                           value="{{ old('allocations.' . $loc->locations_ID . '.units', $exist['units']) }}"
                           min="0"
                           style="width:4rem;"
                           placeholder="Units">
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
          <div id="allocation-error" class="text-danger mb-3" style="display:none;">
            Sum of (cases × split factor + units) must equal Total Quantity.
          </div>

          <button type="submit" class="btn btn-primary" id="submit-btn">
            Update Inventory
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const casesIn   = document.getElementById('master_cases');
  const unitsIn   = document.getElementById('master_units');
  const splitIn   = document.getElementById('split_factor');
  const hiddenQty = document.getElementById('total_quantity');
  const display   = document.getElementById('display_total_quantity');
  const allocCases= Array.from(document.querySelectorAll('.alloc-cases'));
  const allocUnits= Array.from(document.querySelectorAll('.alloc-units'));
  const errorDiv  = document.getElementById('allocation-error');
  const submitBtn = document.getElementById('submit-btn');

  function recalcAll() {
    const cases  = parseInt(casesIn.value) || 0;
    const units  = parseInt(unitsIn.value) || 0;
    const split  = parseInt(splitIn.value) || 1;
    const total  = cases * split + units;

    hiddenQty.value  = total;
    display.value    = total;

    let sumAlloc = 0;
    allocCases.forEach((el,i) => {
      const c = parseInt(el.value)   || 0;
      const u = parseInt(allocUnits[i].value) || 0;
      sumAlloc += c * split + u;
    });

    if (sumAlloc !== total) {
      errorDiv.style.display = 'block';
      submitBtn.disabled     = true;
    } else {
      errorDiv.style.display = 'none';
      submitBtn.disabled     = false;
    }
  }

  [casesIn, unitsIn, splitIn, ...allocCases, ...allocUnits]
    .forEach(el => el.addEventListener('input', recalcAll));

  recalcAll();
});
</script>
@endsection

