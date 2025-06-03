@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Allocate Inventory</h3>

    <div class="row">
        <!-- Left column: Controls (Location, Delivered Flavors, Custom Items) -->
        <div class="col-md-5">
            <!-- 1) Select Location -->
            <div class="mb-4">
                <label for="location-select" class="form-label"><strong>Select Location</strong></label>
                <select id="location-select" class="form-select">
                    @foreach($locations as $loc)
                        <option value="{{ $loc->name }}">{{ $loc->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 2) Delivered Pop Flavors -->
            <div class="mb-4">
                <strong>Delivered Pop Flavors</strong>
                <p class="small text-muted">Click a flavor to allocate one case.</p>
                <button id="allocate-all-btn" class="btn btn-primary btn-sm mb-2">
                    Allocate All
                </button>
                <div id="flavor-container">
                    {{-- Buttons for delivered Pop flavors will be injected here --}}
                </div>
            </div>

            <!-- 3) Custom Items (from Inventory table) -->
            <div class="mb-4">
                <strong>Custom Inventory Items</strong>
                <p class="small text-muted">Click a custom item to allocate one case.</p>
                <div id="custom-container">
                    {{-- Buttons for custom items will be injected here --}}
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
                    {{-- Existing allocations will be injected here on page load --}}
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

{{-- Pass data from PHP to JavaScript --}}
@php
    // $initialPopFlavors: array of objects { fgp_item_id, name }
    // $customItems:   array of strings (custom_item_name values from Inventory)
    // $existingAllocations: array of objects { fgp_item_id (or null), custom_item_name (or ""), location, cases }
@endphp

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // 1) Initialize data from backend
        const initialPopFlavors   = @json($initialPopFlavors);
        const initialCustomItems  = @json($customItems);
        let allocatedInventory    = @json($existingAllocations);

        // 2) Cache DOM elements
        const locationSelect    = document.getElementById("location-select");
        const flavorContainer   = document.getElementById("flavor-container");
        const customContainer   = document.getElementById("custom-container");
        const allocateAllBtn    = document.getElementById("allocate-all-btn");
        const submitBtn         = document.getElementById("submit-allocations-btn");
        const successMsg        = document.getElementById("success-msg");

        // 3) Render flavor buttons for each delivered Pop flavor
        initialPopFlavors.forEach(flavor => {
            const btn = document.createElement("button");
            btn.type = "button";
            btn.className = "btn btn-outline-secondary btn-sm m-1 flavor-btn";
            btn.textContent = flavor.name;
            btn.dataset.id = flavor.fgp_item_id;
            flavorContainer.appendChild(btn);

            btn.addEventListener("click", () => {
                const loc = locationSelect.value;
                // Check if this flavor+location is already allocated
                const existingIdx = allocatedInventory.findIndex(e =>
                    e.fgp_item_id === flavor.fgp_item_id && e.location === loc
                );
                if (existingIdx > -1) {
                    allocatedInventory[existingIdx].cases += 1;
                } else {
                    allocatedInventory.push({
                        fgp_item_id: flavor.fgp_item_id,
                        custom_item_name: null,
                        location: loc,
                        cases: 1
                    });
                }
                updateAllocationTable();
                submitBtn.disabled = (allocatedInventory.length === 0);
            });
        });

        // 4) Render buttons for each custom item
        initialCustomItems.forEach(name => {
            const btn = document.createElement("button");
            btn.type = "button";
            btn.className = "btn btn-outline-info btn-sm m-1 custom-btn";
            btn.textContent = name;
            customContainer.appendChild(btn);

            btn.addEventListener("click", () => {
                const loc = locationSelect.value;
                // Check if this custom item+location is already allocated
                const existingIdx = allocatedInventory.findIndex(e =>
                    e.custom_item_name === name && e.location === loc
                );
                if (existingIdx > -1) {
                    allocatedInventory[existingIdx].cases += 1;
                } else {
                    allocatedInventory.push({
                        fgp_item_id: null,
                        custom_item_name: name,
                        location: loc,
                        cases: 1
                    });
                }
                updateAllocationTable();
                submitBtn.disabled = (allocatedInventory.length === 0);
            });
        });

        // 5) “Allocate All” button: allocate one case of every delivered flavor
        allocateAllBtn.addEventListener("click", () => {
            const loc = locationSelect.value;
            initialPopFlavors.forEach(flavor => {
                const existingIdx = allocatedInventory.findIndex(e =>
                    e.fgp_item_id === flavor.fgp_item_id && e.location === loc
                );
                if (existingIdx > -1) {
                    allocatedInventory[existingIdx].cases += 1;
                } else {
                    allocatedInventory.push({
                        fgp_item_id: flavor.fgp_item_id,
                        custom_item_name: null,
                        location: loc,
                        cases: 1
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
                    // Find the Pop flavor name
                    const found = initialPopFlavors.find(f => f.fgp_item_id === entry.fgp_item_id);
                    itemName = found ? found.name : "Unknown Flavor";
                } else {
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

            // Re-bind “Remove” buttons
            document.querySelectorAll(".remove-allocation").forEach(btn => {
                btn.addEventListener("click", (e) => {
                    const i = parseInt(e.currentTarget.dataset.index);
                    allocatedInventory.splice(i, 1);
                    updateAllocationTable();
                    submitBtn.disabled = (allocatedInventory.length === 0);
                });
            });
        }

        // 7) Initial render of existing allocations (if any)
        updateAllocationTable();
        submitBtn.disabled = (allocatedInventory.length === 0);

        // 8) Submit all allocations via AJAX
        submitBtn.addEventListener("click", () => {
            fetch("{{ route('franchise.allocate-inventory') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    allocatedInventory: allocatedInventory.map(entry => ({
                        fgp_item_id:      entry.fgp_item_id,
                        custom_item_name: entry.custom_item_name || "",
                        location:         entry.location,
                        cases:            entry.cases
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
            .catch(err => {
                alert("Error saving allocations.");
            });
        });
    });
</script>
@endsection
