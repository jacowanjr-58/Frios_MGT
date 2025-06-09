@extends('layouts.app')

@php use Illuminate\Support\Facades\Storage; @endphp

@section('content')
<form id="inventoryForm" action="{{ route('franchise.inventory.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="content-body default-height">
      <div class="container">

        {{-- Page Header --}}
        <div class="row mb-4">
          <div class="col"><h3>Create Inventory Record</h3></div>
          <div class="col text-end">
            <a href="{{ route('franchise.inventory.index') }}" class="btn btn-secondary">← Back to List</a>
          </div>
        </div>

        <div class="card">
          <div class="card-body">

            {{-- Images Section --}}
            <div class="row mb-4">
              @foreach([1,2,3] as $n)
                <div class="col-md-4 text-center">
                  <div class="mb-2">
                    <img id="preview{{ $n }}" src="" alt="Image {{ $n }} Preview" class="img-fluid rounded" style="max-height:100px; object-fit:contain; display:none;">
                    <div id="noimage{{ $n }}" class="text-muted" style="font-style:italic;">(no image)</div>
                  </div>
                  <label class="form-label">Upload Image {{ $n }}</label>
                  <input type="file" id="image{{ $n }}" name="image{{ $n }}" class="form-control @error('image'.$n) is-invalid @enderror" accept="image/*">
                  @error('image'.$n)<div class="text-danger">{{ $message }}</div>@enderror
                </div>
              @endforeach
            </div>

            {{-- Inventory Box --}}
            <div class="border rounded bg-light p-3 mb-4">

              {{-- Corporate vs Custom --}}
              <div class="row mb-2">
                <div class="col-md-6">
                  <label class="form-label">Corporate Item</label>
                  <select id="item_select" name="fgp_item_id" class="form-control @error('fgp_item_id') is-invalid @enderror">
                    <option value="" >-- Manually Add Frios Pop --</option>
                    @foreach($fgpItems as $item)
                      <option value="{{ $item->fgp_item_id }}"
                              data-split="{{ $item->split_factor }}"
                              data-cost="{{ $item->case_cost }}"
                              data-img1="{{ $item->image1 ? Storage::url($item->image1) : '' }}"
                              data-img2="{{ $item->image2 ? Storage::url($item->image2) : '' }}"
                              data-img3="{{ $item->image3 ? Storage::url($item->image3) : '' }}"
                          {{ old('fgp_item_id') == $item->fgp_item_id ? 'selected' : '' }}>
                        {{ $item->name }}
                      </option>
                    @endforeach
                  </select>
                  @error('fgp_item_id')<div class="text-danger">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label">Custom Item Name (or copy name)</label>
                  <input type="text" id="custom_name" name="custom_item_name" class="form-control @error('custom_item_name') is-invalid @enderror" value="{{ old('custom_item_name') }}" placeholder="Enter custom name">
                  @error('custom_item_name')<div class="text-danger">{{ $message }}</div>@enderror
                </div>
              </div>
              <div id="corporateCustomError" class="text-danger mb-3" style="display:none;">
                Please either select a corporate item or enter a custom name.
              </div>

              {{-- Stock / Total / Split --}}
              <div class="row mb-3">
                <div class="col-md-4">
                  <label class="form-label">Stock Count Date</label>
                  <input type="date" name="stock_count_date" class="form-control @error('stock_count_date') is-invalid @enderror" value="{{ old('stock_count_date', now()->toDateString()) }}">
                  @error('stock_count_date')<div class="text-danger">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                  <label class="form-label">Total Quantity</label>
                  <input type="text" id="total_quantity_display" readonly class="form-control bg-secondary text-white" value="0">
                  <input type="hidden" id="total_quantity" name="total_quantity" value="0">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Split Factor <small>(units/case)</small></label>
                  <input type="number" id="split_factor" name="split_factor" class="form-control @error('split_factor') is-invalid @enderror" value="{{ old('split_factor', 1) }}" min="1">
                  @error('split_factor')<div class="text-danger">{{ $message }}</div>@enderror
                </div>
              </div>

              {{-- Case / Unit --}}
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Case Quantity</label>
                  <input type="number" id="case_quantity" name="case_quantity" class="form-control @error('case_quantity') is-invalid @enderror" value="{{ old('case_quantity',0) }}" min="0">
                  @error('case_quantity')<div class="text-danger">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label">Unit Quantity</label>
                  <input type="number" id="unit_quantity" name="unit_quantity" class="form-control @error('unit_quantity') is-invalid @enderror" value="{{ old('unit_quantity',0) }}" min="0">
                  @error('unit_quantity')<div class="text-danger">{{ $message }}</div>@enderror
                </div>
              </div>

              {{-- Cost Rows --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">COGS Case $</label>
                    <input type="number" name="cogs_case" id="cogs_case" class="form-control" step="0.01" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Wholesale Case $</label>
                    <input type="number" name="wholesale_case" class="form-control" step="0.01" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Retail Case $</label>
                    <input type="number" name="retail_case" class="form-control" step="0.01" min="0">
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label">COGS Unit $</label>
                    <input type="number" name="cogs_unit" class="form-control" step="0.01" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Wholesale Unit $</label>
                    <input type="number" name="wholesale_unit" class="form-control" step="0.01" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Retail Unit $</label>
                    <input type="number" name="retail_unit" class="form-control" step="0.01" min="0">
                </div>
            </div>
            </div>
            {{-- Allocation Box --}}
            <div class="border rounded bg-light p-3">
              <h4>Allocate Quantities by Location</h4>
              <div id="allocation-error" class="text-danger" style="display:none;">Sum of allocations must equal total quantity.</div>
              <table class="table table-bordered mb-3">
                <thead><tr><th>Location</th><th class="text-center">Cases</th><th class="text-center">Units</th><th class="text-center">Total</th></tr></thead>
                <tbody>
                  @foreach($locations as $loc)
                    <tr>
                      <td>{{ $loc->name }}</td>
                      <td class="text-center"><input type="number" class="form-control alloc-cases" name="allocations[{{ $loc->locations_ID }}][cases]" value="0" min="0" style="width:4rem;"></td>
                      <td class="text-center"><input type="number" class="form-control alloc-units" name="allocations[{{ $loc->locations_ID }}][units]" value="0" min="0" style="width:4rem;"></td>
                      <td class="text-center total-for-{{ $loc->locations_ID }}">0</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>

            </div>

            <div class="mt-4 text-end">
              <button type="submit" class="btn btn-primary">Confirm & Add to Inventory</button>
            </div>

          </div>
        </div>
      </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form         = document.getElementById('inventoryForm');
  const itemSelect   = document.getElementById('item_select');
  const customInput  = document.getElementById('custom_name');
  const errorDiv     = document.getElementById('corporateCustomError');

  const splitInput   = document.getElementById('split_factor');
  const costInput    = document.getElementById('cogs_case');
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

  // Block submit if neither corporate nor custom is filled
  form.addEventListener('submit', function(e) {
    const hasCorp   = Boolean(itemSelect.value);
    const hasCustom = Boolean(customInput.value.trim());
    if (!hasCorp && !hasCustom) {
      e.preventDefault();
      errorDiv.style.display = 'block';
      return;
    }
  });
  itemSelect.addEventListener('change', () => errorDiv.style.display = 'none');
  customInput.addEventListener('input',   () => errorDiv.style.display = 'none');

  // When selecting a corporate item, load its split/cost/images
  itemSelect.addEventListener('change', updateFields);

  // When case, unit or split changes, recalc total & allocations
  caseInput.addEventListener('input',   updateTotal);
  unitInput.addEventListener('input',   updateTotal);
  splitInput.addEventListener('input',  updateTotal);

  // Re-calc allocations whenever those fields change
  document.querySelectorAll('.alloc-cases, .alloc-units')
          .forEach(el => el.addEventListener('input', updateTotal));

  function updateFields() {
    const opt = itemSelect.options[itemSelect.selectedIndex] || {};
    splitInput.value = opt.dataset.split || '1';
    costInput.value  = opt.dataset.cost  || '';
    ['img1','img2','img3'].forEach((key, idx) => {
      const url = opt.dataset[key] || '';
      const base = window.location.origin + '/';
      if (url && url !== base) {
        previews[idx].src = url;
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
    const c   = parseInt(caseInput.value)    || 0;
    const u   = parseInt(unitInput.value)    || 0;
    const s   = parseInt(splitInput.value)   || 0;
    const tot = c * s + u;
    totalDisplay.value = tot;
    totalInput.value   = tot;
    recalcAllocations();
  }

  function recalcAllocations() {
    const rows   = document.querySelectorAll('table tbody tr');
    let sum      = 0;
    const s      = parseInt(splitInput.value)       || 0;
    const target = parseInt(totalInput.value)       || 0;

    rows.forEach(row => {
      const cv    = parseInt(row.querySelector('.alloc-cases').value) || 0;
      const uv    = parseInt(row.querySelector('.alloc-units').value) || 0;
      const rv    = cv * s + uv;
      row.querySelector('td:last-child').textContent = rv;
      sum += rv;
    });

    document.getElementById('allocation-error')
            .style.display = (sum !== target) ? 'block' : 'none';
  }

  // Initial load
  updateFields();
});
</script>
@endpush

@endsection
