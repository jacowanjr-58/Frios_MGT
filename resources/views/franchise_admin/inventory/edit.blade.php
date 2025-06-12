@extends('layouts.app')

@php use Illuminate\Support\Facades\Storage; @endphp

@section('content')
<form id="editForm" action="{{ route('franchise.inventory.update', ['franchisee' => request()->route('franchisee'), 'inventoryMaster' => $inventoryMaster->inventory_id]) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="content-body default-height">
      <div class="container-fluid">

        {{-- Page Header --}}
        <div class="row mb-4">
          <div class="col"><h3>Edit Inventory Record</h3></div>
          <div class="col text-end">
            <a href="{{ route('franchise.inventory.index', ['franchisee' => request()->route('franchisee')]) }}" class="btn btn-secondary">← Back to List</a>
          </div>
        </div>

        <div class="card">
          <div class="card-body">

            {{-- Images Section --}}
            <div class="row mb-4">
              @foreach([1,2,3] as $n)
                <div class="col-md-4 text-center">
                  <div class="mb-2">
                    @php $img = $inventoryMaster->{'image'.$n}; @endphp
                    @if($img)
                      <img id="preview{{ $n }}" src="{{ Storage::url($img) }}" alt="Image {{ $n }}" class="img-fluid rounded" style="max-height:100px; object-fit:contain;">
                      <div id="noimage{{ $n }}" style="display:none;"></div>
                    @else
                      <img id="preview{{ $n }}" src="" class="img-fluid rounded" style="max-height:100px; display:none;">
                      <div id="noimage{{ $n }}" class="text-muted" style="font-style:italic;">(no image)</div>
                    @endif
                  </div>
                  <label class="form-label">Replace Image {{ $n }}</label>
                  <input type="file" id="image{{ $n }}" name="image{{ $n }}" class="form-control" accept="image/*">
                </div>
              @endforeach
            </div>

            {{-- Inventory Fields Box --}}
            <div class="border rounded bg-light p-3 mb-4">

              {{-- Corporate Item (read-only) --}}
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Corporate Item</label>
                  <input type="text" class="form-control bg-slate-400" value="{{ optional($inventoryMaster->flavor)->name ?? '' }}" disabled>
                </div>
                {{-- Custom Item Name --}}
                <div class="col-md-6">
                 @if($inventoryMaster->flavor &&
                    empty(old('custom_item_name', $inventoryMaster->custom_item_name)))
                <input type="hidden" id="custom_name" name="custom_item_name" value="">
                    @else
                    <label class="form-label">Custom Item Name</label>
                <input type="text" id="custom_name" name="custom_item_name"
                    class="form-control @error('custom_item_name') is-invalid @enderror"
                    value="{{ old('custom_item_name', $inventoryMaster->custom_item_name) }}">
                    @endif

                @error('custom_item_name')
                <div class="text-danger">{{ $message }}</div>
                @enderror
                  <div id="customError" class="text-danger" style="display:none;">Custom Item Name cannot be blank.</div>
                </div>
              </div>

              {{-- Stock / Total / Split --}}
              <div class="row mb-3">
                <div class="col-md-4">
                  <label class="form-label">Stock Count Date</label>
                    <input type="date" id="stock_count_date" name="stock_count_date"
                        class="form-control @error('stock_count_date') is-invalid @enderror"
                        value="{{ old('stock_count_date', now()->toDateString()) }}">
                  @error('stock_count_date')<div class="text-danger">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                  <label class="form-label">Total Quantity</label>
                  <input type="text" id="total_quantity_display" readonly class="form-control bg-secondary text-white" value="0">
                  <input type="hidden" id="total_quantity" name="total_quantity" value="0">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Split Factor <small>(units/case)</small></label>
                  <input type="number" id="split_factor" name="split_factor" class="form-control @error('split_factor') is-invalid @enderror" value="{{ old('split_factor', $inventoryMaster->split_factor) }}" min="1">
                  @error('split_factor')<div class="text-danger">{{ $message }}</div>@enderror
                </div>
              </div>

              {{-- Case / Unit --}}
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Case Quantity</label>
                  <input type="number" id="case_quantity" name="case_quantity" class="form-control @error('case_quantity') is-invalid @enderror" value="{{ old('case_quantity', $inventoryMaster->cases) }}" min="0">
                  @error('case_quantity')<div class="text-danger">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label">Unit Quantity</label>
                  <input type="number" id="unit_quantity" name="unit_quantity" class="form-control @error('unit_quantity') is-invalid @enderror" value="{{ old('unit_quantity', $inventoryMaster->units) }}" min="0">
                  @error('unit_quantity')<div class="text-danger">{{ $message }}</div>@enderror
                </div>
              </div>

              {{-- Cost Rows --}}
              <div class="row mb-3">
                <div class="col-md-4">
                  <label class="form-label">COGS Case $</label>
                  <input type="number" name="cogs_case" id="cogs_case" class="form-control" step="0.01" min="0" value="{{ old('cogs_case', $inventoryMaster->cogs_case) }}">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Wholesale Case $</label>
                  <input type="number" name="wholesale_case" class="form-control" step="0.01" min="0" value="{{ old('wholesale_case', $inventoryMaster->wholesale_case) }}">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Retail Case $</label>
                  <input type="number" name="retail_case" class="form-control" step="0.01" min="0" value="{{ old('retail_case', $inventoryMaster->retail_case) }}">
                </div>
              </div>

              <div class="row mb-4">
                <div class="col-md-4">
                  <label class="form-label">COGS Unit $</label>
                  <input type="number" name="cogs_unit" class="form-control" step="0.01" min="0" value="{{ old('cogs_unit', $inventoryMaster->cogs_unit) }}">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Wholesale Unit $</label>
                  <input type="number" name="wholesale_unit" class="form-control" step="0.01" min="0" value="{{ old('wholesale_unit', $inventoryMaster->wholesale_unit) }}">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Retail Unit $</label>
                  <input type="number" name="retail_unit" class="form-control" step="0.01" min="0" value="{{ old('retail_unit', $inventoryMaster->retail_unit) }}">
                </div>
              </div>

            </div> {{-- end inventory box --}}

            {{-- Allocation Box --}}
            <div class="border rounded bg-light p-3">
              <h4>Allocate Quantities by Location</h4>
              <table class="table table-bordered mb-3">
                <thead><tr><th>Location</th><th class="text-center">Cases</th><th class="text-center">Units</th><th class="text-center">Total</th></tr></thead>
                <tbody>
                  @foreach($locations as $loc)
                    @php $a = $existingAllocations[$loc->locations_ID] ?? ['cases'=>0,'units'=>0]; @endphp
                    <tr>
                      <td>{{ $loc->name }}</td>
                      <td class="text-center"><input type="number" name="allocations[{{ $loc->locations_ID }}][cases]" class="form-control alloc-cases" value="{{ old('allocations.' . $loc->locations_ID . '.cases', $a['cases']) }}" min="0" style="width:4rem;"></td>
                      <td class="text-center"><input type="number" name="allocations[{{ $loc->locations_ID }}][units]" class="form-control alloc-units" value="{{ old('allocations.' . $loc->locations_ID . '.units', $a['units']) }}" min="0" style="width:4rem;"></td>
                      <td class="text-center total-for-{{ $loc->locations_ID }}">0</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
              <div id="allocation-error" class="text-danger" style="display:none;">Sum of allocations must equal total quantity.</div>
            </div>

            <div class="mt-4 text-end">
              <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>

          </div>
        </div>
      </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form         = document.getElementById('editForm');
  const custom       = document.getElementById('custom_name');
  const errorCustom  = document.getElementById('customError');

  // determine if custom_name was present on load
  const initialCustom = custom ? custom.value.trim() : '';
  const customRequired = initialCustom.length > 0;

  // hide error at start
  errorCustom.style.display = 'none';

  // only validate custom_name if it was non‐empty on load
  form.addEventListener('submit', function(e) {
    if (customRequired && custom.value.trim() === '') {
      e.preventDefault();
      errorCustom.style.display = 'block';
      return;
    }
  });

  // clear error as soon as user types
  custom.addEventListener('input', () => {
    if (errorCustom.style.display === 'block') {
      errorCustom.style.display = 'none';
    }
  });

  const splitInput   = document.getElementById('split_factor');
  const caseInput    = document.getElementById('case_quantity');
  const unitInput    = document.getElementById('unit_quantity');
  const totalDisplay = document.getElementById('total_quantity_display');
  const totalInput   = document.getElementById('total_quantity');

  const previews = [
    document.getElementById('preview1'),
    document.getElementById('preview2'),
    document.getElementById('preview3')
  ];
  const placeholders = [
    document.getElementById('noimage1'),
    document.getElementById('noimage2'),
    document.getElementById('noimage3')
  ];

  function updateFields() {
    ['img1','img2','img3'].forEach((key, idx) => {
      const url = previews[idx].src;
      if (url) {
        previews[idx].style.display = '';
        placeholders[idx].style.display = 'none';
      } else {
        previews[idx].style.display = 'none';
        placeholders[idx].style.display = '';
      }
    });
    updateTotal();
  }

  function updateTotal() {
    const c   = parseInt(caseInput.value)   || 0;
    const u   = parseInt(unitInput.value)   || 0;
    const s   = parseInt(splitInput.value)  || 0;
    const tot = c * s + u;
    totalDisplay.value = tot;
    totalInput.value   = tot;
    recalcAllocations();
  }

  function recalcAllocations() {
    const rows   = document.querySelectorAll('table tbody tr');
    let sum      = 0;
    const s      = parseInt(splitInput.value)  || 0;
    const target = parseInt(totalInput.value)  || 0;

    rows.forEach(row => {
      const cv = parseInt(row.querySelector('.alloc-cases').value) || 0;
      const uv = parseInt(row.querySelector('.alloc-units').value) || 0;
      const rv = cv * s + uv;
      row.querySelector('td:last-child').textContent = rv;
      sum += rv;
    });

    document.getElementById('allocation-error')
            .style.display = (sum !== target) ? 'block' : 'none';
  }

  // re‐bind total logic
  caseInput.addEventListener('input',  updateTotal);
  unitInput.addEventListener('input',  updateTotal);
  splitInput.addEventListener('input', updateTotal);
  document.querySelectorAll('.alloc-cases, .alloc-units')
          .forEach(el => el.addEventListener('input', updateTotal));

  // initial render
  updateFields();
});
</script>
@endpush

@endsection
<style scoped>
  .btn-primary {
    background-color: #00abc7 !important;
  
  }
</style>