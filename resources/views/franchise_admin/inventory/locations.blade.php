@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Allocate Inventory</h3>

    <div class="row">
        <div class="col-md-5">
            <div class="mb-4">
                <label for="location-select" class="form-label"><strong>Select Location</strong></label>
                <select id="location-select" class="form-select">
                    @foreach($locations as $loc)
                        <option value="{{ $loc->name }}">{{ $loc->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <strong>Delivered Pop Flavors</strong>
                <p class="small text-muted">Click a flavor to allocate one case.</p>
                <button id="allocate-all-btn" class="btn btn-primary btn-sm mb-2">
                    Allocate All
                </button>
                <div id="flavor-container"></div>
            </div>

            <div class="mb-4">
                <strong>Custom Inventory Items</strong>
                <p class="small text-muted">Click an item to allocate one case.</p>
                <div id="custom-container"></div>
            </div>
        </div>

        <div class="col-md-7">
            <div id="success-msg" class="alert alert-success" style="display: none;">
                Allocation saved successfully.
            </div>

            <table class="table table-bordered" id="allocation-table">
                <thead class="table-light">
                    <tr>
                        <th>Item</th>
                        <th>Location</th>
                        <th>Cases</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <div class="d-flex justify-content-end">
                <button id="submit-allocations-btn" class="btn btn-success" disabled>
                    Submit Allocations
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const initialPopFlavors   = @json($initialPopFlavors);
    const inventoryMastersForPop = @json($inventoryMastersForPop);
    const initialCustomItems  = @json($customItems);
    let allocatedInventory    = @json($existingAllocations);

    const locationSelect    = document.getElementById("location-select");
    const flavorContainer   = document.getElementById("flavor-container");
    const customContainer   = document.getElementById("custom-container");
    const allocateAllBtn    = document.getElementById("allocate-all-btn");
    const submitBtn         = document.getElementById("submit-allocations-btn");
    const successMsg        = document.getElementById("success-msg");

    initialPopFlavors.forEach(flavor => {
        const inv = inventoryMastersForPop.find(i => i.fgp_item_id === flavor.fgp_item_id);
        if (!inv) return;

        const btn = document.createElement("button");
        btn.type = "button";
        btn.className = "btn btn-outline-secondary btn-sm m-1 flavor-btn";
        btn.textContent = flavor.name;
        btn.dataset.id = inv.inventory_id;
        flavorContainer.appendChild(btn);

        btn.addEventListener("click", () => {
            const loc = locationSelect.value;
            const idx = allocatedInventory.findIndex(e =>
                e.inventory_id === inv.inventory_id && e.location === loc
            );
            if (idx > -1) {
                allocatedInventory[idx].cases += 1;
            } else {
                allocatedInventory.push({
                    inventory_id: inv.inventory_id,
                    fgp_item_id: inv.fgp_item_id,
                    custom_item_name: inv.custom_item_name,
                    location: loc,
                    cases: 1
                });
            }
            updateAllocationTable();
            submitBtn.disabled = (allocatedInventory.length === 0);
        });
    });

    initialCustomItems.forEach(item => {
        const btn = document.createElement("button");
        btn.type = "button";
        btn.className = "btn btn-outline-info btn-sm m-1 custom-btn";
        btn.textContent = item.custom_item_name;
        btn.dataset.id = item.inventory_id;
        customContainer.appendChild(btn);

        btn.addEventListener("click", () => {
            const loc = locationSelect.value;
            const idx = allocatedInventory.findIndex(e =>
                e.inventory_id === item.inventory_id && e.location === loc
            );
            if (idx > -1) {
                allocatedInventory[idx].cases += 1;
            } else {
                allocatedInventory.push({
                    inventory_id: item.inventory_id,
                    fgp_item_id: null,
                    custom_item_name: item.custom_item_name,
                    location: loc,
                    cases: 1
                });
            }
            updateAllocationTable();
            submitBtn.disabled = (allocatedInventory.length === 0);
        });
    });

    allocateAllBtn.addEventListener("click", () => {
        const loc = locationSelect.value;
        initialPopFlavors.forEach(flavor => {
            const inv = inventoryMastersForPop.find(i => i.fgp_item_id === flavor.fgp_item_id);
            if (!inv) return;

            const idx = allocatedInventory.findIndex(e =>
                e.inventory_id === inv.inventory_id && e.location === loc
            );
            if (idx > -1) {
                allocatedInventory[idx].cases += 1;
            } else {
                allocatedInventory.push({
                    inventory_id: inv.inventory_id,
                    fgp_item_id: inv.fgp_item_id,
                    custom_item_name: inv.custom_item_name,
                    location: loc,
                    cases: 1
                });
            }
        });
        updateAllocationTable();
        submitBtn.disabled = (allocatedInventory.length === 0);
    });

    function updateAllocationTable() {
        const tbody = document.querySelector("#allocation-table tbody");
        tbody.innerHTML = "";

        allocatedInventory.forEach((e, idx) => {
            let itemName;
            if (e.fgp_item_id) {
                const found = initialPopFlavors.find(f => f.fgp_item_id === e.fgp_item_id);
                itemName = found ? found.name : "Unknown Flavor";
            } else {
                itemName = e.custom_item_name;
            }

            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${itemName}</td>
                <td>${e.location}</td>
                <td>${e.cases}</td>
                <td>
                  <button class="btn btn-sm btn-danger remove-allocation" data-index="${idx}">
                    Remove
                  </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        document.querySelectorAll(".remove-allocation").forEach(btn => {
            btn.addEventListener("click", (evt) => {
                const i = parseInt(evt.currentTarget.dataset.index);
                allocatedInventory.splice(i, 1);
                updateAllocationTable();
                submitBtn.disabled = (allocatedInventory.length === 0);
            });
        });
    }

    updateAllocationTable();
    submitBtn.disabled = (allocatedInventory.length === 0);

    submitBtn.addEventListener("click", () => {
        fetch("{{ route('franchise.allocate-inventory') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                allocatedInventory: allocatedInventory.map(e => ({
                    inventory_id: e.inventory_id,
                    location:     e.location,
                    cases:        e.cases
                }))
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.error === false) {
                successMsg.style.display = "block";
                setTimeout(() => successMsg.style.display = "none", 2000);
            } else {
                alert(data.message);
            }
        })
        .catch(() => alert("Error saving allocations."));
    });
});
</script>
@endsection
