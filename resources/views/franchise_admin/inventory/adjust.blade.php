@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Adjust / Onboard Inventory</h3>
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Item Name</th>
                <th>Total Quantity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventoryMasters as $master)
                <tr>
                    <td>{{ $master->inventory_id }}</td>
                    <td>
                        @if($master->fgp_item_id)
                            {{ $master->flavor->name }}
                        @else
                            {{ $master->custom_item_name }}
                        @endif
                    </td>
                    <td>{{ $master->total_quantity }}</td>
                    <td>
                        <button class="btn btn-sm btn-success adjust-btn" 
                                data-id="{{ $master->inventory_id }}" 
                                data-type="add">
                            + Adjust
                        </button>
                        <button class="btn btn-sm btn-danger adjust-btn" 
                                data-id="{{ $master->inventory_id }}" 
                                data-type="remove">
                            – Adjust
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="modal fade" id="adjustModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="adjustForm" method="POST" action="{{ route('franchise.inventory.adjust.update') }}">
      @csrf
      <input type="hidden" name="inventory_id" id="modal_inventory_id">
      <input type="hidden" name="type" id="modal_type">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Adjust Inventory</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="modal_quantity" class="form-label">Quantity</label>
            <input type="number" name="quantity" id="modal_quantity" class="form-control" min="1" value="1">
          </div>
          <div class="mb-3">
            <label for="modal_notes" class="form-label">Notes (optional)</label>
            <textarea name="notes" id="modal_notes" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Cancel
          </button>
          <button type="submit" class="btn btn-primary" id="modalSubmitBtn">
            Confirm
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const adjustButtons = document.querySelectorAll(".adjust-btn");
    const adjustModal = new bootstrap.Modal(document.getElementById("adjustModal"));
    const modalTitle = document.getElementById("modalTitle");
    const modalInventoryId = document.getElementById("modal_inventory_id");
    const modalType = document.getElementById("modal_type");
    const modalQuantity = document.getElementById("modal_quantity");

    adjustButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            const invId = btn.dataset.id;
            const type = btn.dataset.type;
            modalInventoryId.value = invId;
            modalType.value = type;
            modalTitle.textContent = type === 'add' ? "Add Quantity" : "Remove Quantity";
            modalQuantity.value = 1;
            adjustModal.show();
        });
    });
});
</script>
@endsection
