@extends('layouts.app')

@section('content')
<div class="content-body default-height">
    <!-- row -->
    <div class="container-fluid">


        <h3 class="mb-4">Allocate Inventory</h3>

        <div class="row">
            <!-- Left column: Location selector, Delivered Flavors, Custom Items -->
            <div class="col-md-5">
                <!-- 1) Select Location -->
                <div class="mb-4">
                    <label for="location-select" class="form-label"><strong>Select Location</strong></label>
                    <select id="location-select" class="form-select form-control">
                        @foreach($locations as $loc)
                        <option value="{{ $loc->name }}">{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 2) Delivered Pop Flavors (real inventory) -->
                <div class="mb-4">
                    <strong>Delivered Pop Flavors</strong>
                    <p class="small text-muted">Click a flavor to allocate one case.</p>
                    <button id="allocate-all-btn" class="btn btn-primary btn-sm mb-2">
                        Allocate All
                    </button>
                    <div id="flavor-container">
                        {{-- Buttons for each real Pop flavor will be injected via JS --}}
                    </div>
                </div>

                <!-- 3) Custom Inventory Items -->
                <div class="mb-4">
                    <strong>Custom Inventory Items</strong>
                    <p class="small text-muted">Click a custom item to allocate one case.</p>
                    <div id="custom-container">
                        {{-- Buttons for each custom item will be injected via JS --}}
                    </div>
                </div>
            </div>

            <!-- Right column: Allocation Table & Submit -->
            <div class="col-md-7">
                <!-- Success message -->
                <div id="success-msg" class="alert alert-success" style="display: none;">
                    Allocation saved successfully.
                </div>

                <!-- Allocation table -->
                <table class="table table-bordered" id="allocation-table">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th>Location</th>
                            <th>Cases</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Existing allocations will appear here on page load --}}
                    </tbody>
                </table>

                <!-- Submit button -->
                <div class="d-flex justify-content-end">
                    <button id="submit-allocations-btn" class="btn btn-success" disabled>
                        Submit Allocations
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Pass data from Laravel to JavaScript --}}
<script>
document.addEventListener("DOMContentLoaded", () => {
    // 1) Data from controller
    const initialPopFlavors = @json($initialPopFlavors);
    const customItems       = @json($customItems);
    let allocatedInventory  = @json($existingAllocations);

    // 2) Cache DOM elements
    const locationSelect  = document.getElementById("location-select");
    const flavorContainer = document.getElementById("flavor-container");
    const customContainer = document.getElementById("custom-container");
    const allocateAllBtn  = document.getElementById("allocate-all-btn");
    const submitBtn       = document.getElementById("submit-allocations-btn");
    const successMsg      = document.getElementById("success-msg");

    // 3) Render a button for each delivered Pop flavor (inventoryMaster with non‐null fgp_item_id)
    initialPopFlavors.forEach(master => {
        const btn = document.createElement("button");
        btn.type = "button";
        btn.className = "btn btn-outline-secondary btn-sm m-1 flavor-btn";
        // Use the accessor: master.flavor.name
        btn.textContent = master.flavor.name;
        btn.dataset.id = master.inventory_id;
        flavorContainer.appendChild(btn);

        btn.addEventListener("click", () => {
            const loc = locationSelect.value;
            // Find or create an allocation entry for this inventory_id + location
            const idx = allocatedInventory.findIndex(e =>
                e.inventory_id === master.inventory_id && e.location === loc
            );
            if (idx > -1) {
                allocatedInventory[idx].cases += 1;
            } else {
                allocatedInventory.push({
                    inventory_id:      master.inventory_id,
                    fgp_item_id:       master.fgp_item_id,
                    custom_item_name:  master.custom_item_name,
                    location:          loc,
                    cases:             1
                });
            }
            updateAllocationTable();
            submitBtn.disabled = (allocatedInventory.length === 0);
        });
    });

    // 4) Render a button for each custom inventory item (fgp_item_id is null)
    customItems.forEach(master => {
        const btn = document.createElement("button");
        btn.type = "button";
        btn.className = "btn btn-outline-info btn-sm m-1 custom-btn";
        btn.textContent = master.custom_item_name;
        btn.dataset.id = master.inventory_id;
        customContainer.appendChild(btn);

        btn.addEventListener("click", () => {
            const loc = locationSelect.value;
            const idx = allocatedInventory.findIndex(e =>
                e.inventory_id === master.inventory_id && e.location === loc
            );
            if (idx > -1) {
                allocatedInventory[idx].cases += 1;
            } else {
                allocatedInventory.push({
                    inventory_id:      master.inventory_id,
                    fgp_item_id:       master.fgp_item_id,      // will be null
                    custom_item_name:  master.custom_item_name,
                    location:          loc,
                    cases:             1
                });
            }
            updateAllocationTable();
            submitBtn.disabled = (allocatedInventory.length === 0);
        });
    });

    // 5) “Allocate All” button: allocate one case of every delivered Pop flavor
    allocateAllBtn.addEventListener("click", () => {
        const loc = locationSelect.value;
        initialPopFlavors.forEach(master => {
            const idx = allocatedInventory.findIndex(e =>
                e.inventory_id === master.inventory_id && e.location === loc
            );
            if (idx > -1) {
                allocatedInventory[idx].cases += 1;
            } else {
                allocatedInventory.push({
                    inventory_id:      master.inventory_id,
                    fgp_item_id:       master.fgp_item_id,
                    custom_item_name:  master.custom_item_name,
                    location:          loc,
                    cases:             1
                });
            }
        });
        updateAllocationTable();
        submitBtn.disabled = (allocatedInventory.length === 0);
    });

    // 6) Render the allocation table rows
    function updateAllocationTable() {
        const tbody = document.querySelector("#allocation-table tbody");
        tbody.innerHTML = "";

        allocatedInventory.forEach((entry, idx) => {
            let itemName;
            if (entry.fgp_item_id) {
                // Real Pop flavor
                const master = initialPopFlavors.find(m => m.inventory_id === entry.inventory_id);
                itemName = master ? master.flavor.name : "Unknown Flavor";
            } else {
                // Custom item
                itemName = entry.custom_item_name;
            }

            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${itemName}</td>
                <td>${entry.location}</td>
                <td>${entry.cases}</td>
                <td>
                    <button class="btn btn-sm btn-danger remove-allocation" data-index="${idx}">
                        Remove
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        // Re-bind “Remove” buttons after rendering
        document.querySelectorAll(".remove-allocation").forEach(btn => {
            btn.addEventListener("click", (e) => {
                const i = parseInt(e.currentTarget.dataset.index);
                allocatedInventory.splice(i, 1);
                updateAllocationTable();
                submitBtn.disabled = (allocatedInventory.length === 0);
            });
        });
    }

    // 7) Initial render of existing allocations, if any
    updateAllocationTable();
    submitBtn.disabled = (allocatedInventory.length === 0);

    // 8) Submit all allocations via AJAX
    submitBtn.addEventListener("click", () => {
        fetch("{{ route('franchise.inventory.allocate-inventory' , ['franchisee' => request()->route('franchisee')]) }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                allocatedInventory: allocatedInventory.map(entry => ({
                    inventory_id: entry.inventory_id,
                    location:     entry.location,
                    cases:        entry.cases
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
        .catch(() => {
            alert("Error saving allocations.");
        });
    });
});
</script>
@endsection

