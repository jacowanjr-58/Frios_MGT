@extends('layouts.app')
@section('content')
    <style>
        .container {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            width: 100%;
        }

        .left-column {
            flex: 1;
            max-width: 20%;
            min-width: 250px;
        }

        .right-column {
            flex: 2;
            max-width: 80%;
            min-width: 450px;
        }

        .flavor-item {
            cursor: pointer;
            padding: 8px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            margin-bottom: 5px;
            border-radius: 5px;
            text-align: center;
            font-size: 0.9rem;
            width: 100%;
        }

        .flavor-item:hover {
            background-color: #e0e0e0;
        }

        .disabled {
            background-color: #d3d3d3;
            cursor: not-allowed;
        }

        .allocation-box {
            border: 2px solid #007bff;
            padding: 15px;
            border-radius: 8px;
            background: #f9f9f9;
            height: 100vh;
            overflow-y: auto;
        }

        .cases-input {
            width: 6ch;
            text-align: center;
        }

        .location-section {
            margin-top: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: #f1f1f1;
        }

        /* Sold bin styling */
        #sold-container {
            margin-top: 15px;
        }

        #sold-container .sold-btn {
            margin: 3px;
            width: 100%;
        }

        /* Added styling for flavor images */
        .flavor-img {
            max-height: 60px;
            margin-right: 5px;
            vertical-align: middle;
        }
    </style>
    <div class="content-body default-height">
        <!-- row -->
        <div class="container-fluid">
            <div class="alert alert-success" id="success-msg" style="display: none">
                <strong>Success:</strong> All Location submitted successfully
            </div>
            <div class="container mt-4">
                <!-- Left Column: Location Selector, Incoming Orders, and Sold Bin -->
                <div class="left-column" style="border: 2px solid #007BFF; border-radius: 10px; max-height: 100vh; overflow-y: auto; padding: 10px;">
                    <label for="location-select" style="padding-top: 20px;" class="form-label"><strong>Select Location:</strong></label>
                    <select id="location-select" class="form-select mb-3"></select>

                    <strong>Delivered Pop Flavors</strong>
                    <p>Click to allocate a case.</p>
                    <button id="allocate-all-btn" class="btn btn-primary me-2">
                        Allocate All
                    </button>
                    <div id="flavor-container"></div>

                    <hr />
                    <strong>Sold Cases</strong>
                    <p>Click a flavor to reallocate the sold case.</p>
                    <div id="sold-container"></div>
                </div>


                <!-- Right Column: Inventory Allocation -->
                <div class="right-column">
                    <div class="allocation-box">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Inventory Allocation</h4>
                            <div>
                                <button id="submit-allocation" class="btn btn-primary me-2" disabled>
                                    Submit
                                </button>
                                <button id="reset-allocation" class="btn btn-secondary">
                                    Reset
                                </button>
                            </div>
                        </div>
                        <div id="allocation-sections"></div>
                        <div id="message" class="mt-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            /*
             * Simulated data loaded from database tables.
             * For real-world usage, these arrays would be populated via API calls or server-side rendering.
             */

            // Initial locations (from the "locations" table)
            const initialLocations = @json($locations->pluck('name'));

            const initialPopFlavors = @json($initialPopFlavors);

            console.warn("============================");
            console.log(initialPopFlavors);


            // Inventory allocation data from the inventory table (preset to empty or some initial allocation)
            // const initialAllocatedInventory = [
            // Example: { flavor: "Strawberry", location: "Van 1", cases: 2, image1: }
            // ];

            const initialAllocatedInventory = @json($allocatedInventory);


            // Sold inventory: track how many cases have been sold for each flavor.
            const initialSoldInventory = {};
            initialPopFlavors.forEach(
                (flavor) => (initialSoldInventory[flavor.name] = 0)
            );

            // Global variables (will be modified during interaction)
            let locations, popFlavors, allocatedInventory, soldInventory;

            // Function to perform deep copies of our initial arrays/objects
            function loadInitialData() {
                locations = JSON.parse(JSON.stringify(initialLocations));
                popFlavors = JSON.parse(JSON.stringify(initialPopFlavors));
                allocatedInventory = JSON.parse(
                    JSON.stringify(initialAllocatedInventory)
                );
                soldInventory = JSON.parse(JSON.stringify(initialSoldInventory));
            }

            // Load the initial data
            loadInitialData();







            // DOM elements
            const flavorContainer = document.getElementById("flavor-container");
            const allocationSections = document.getElementById(
                "allocation-sections"
            );
            const submitButton = document.getElementById("submit-allocation");
            const resetButton = document.getElementById("reset-allocation");
            const locationSelect = document.getElementById("location-select");
            const allocateAllButton = document.getElementById("allocate-all-btn");

            // Render location dropdown (from locations table)
            function renderLocationDropdown() {
                locationSelect.innerHTML = "";
                locations.forEach((location) => {
                    let option = document.createElement("option");
                    option.value = location;
                    option.textContent = location;
                    locationSelect.appendChild(option);
                });
            }

            // Render delivered pop flavors (arrived orders) with images
            function renderFlavors() {
                flavorContainer.innerHTML = "";
                popFlavors.forEach((flavor) => {
                    let div = document.createElement("div");
                    div.classList.add("flavor-item","d-flex", "items-center");
                    // Include image next to the flavor name and available count
                    div.innerHTML =
                        `<img src="{{ asset('storage') }}/${flavor.image1}" alt="no-img" class="flavor-img"> ${flavor.name} (${flavor.available})`;
                    div.dataset.flavor = flavor.name;
                    div.addEventListener("click", () => addToAllocation(flavor));
                    flavorContainer.appendChild(div);
                });
            }

            // Update flavor buttons to reflect current available counts (include images)
            function updateFlavorButtons() {
                document.querySelectorAll(".flavor-item").forEach((div) => {
                    let flavor = popFlavors.find((f) => f.name === div.dataset.flavor);
                    div.innerHTML =
                        `<img src="{{ asset('storage') }}/${flavor.image1}" class="flavor-img"> ${flavor.name} (${flavor.available})`;
                    div.classList.toggle("disabled", flavor.available <= 0);
                });
            }

            // Render allocation sections for each location (from inventory table)
            function renderLocationSections() {
                allocationSections.innerHTML = "";
                locations.forEach((location) => {
                    let section = document.createElement("div");
                    section.classList.add("location-section");
                    section.innerHTML = `
                        <h5>${location}</h5>
                        <table class="table">
                        <thead>
                            <tr>
                            <th>Pop Flavor</th>
                            <th># Cases</th>
                            <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="allocation-body-${location}"></tbody>
                        </table>
                    `;
                    allocationSections.appendChild(section);
                });
            }

            // Add a flavor to the allocation for the selected location
            function addToAllocation(flavor) {
                let location = locationSelect.value;
                if (flavor.available <= 0) return;

                // Check if an allocation entry already exists for this flavor and location.
                let existingAllocation = allocatedInventory.find(
                    (a) => a.flavor === flavor.name && a.location === location
                );
                if (existingAllocation) {
                    let a = parseInt(existingAllocation.cases);
                    existingAllocation.cases = a + 1;
                } else {
                    allocatedInventory.push({
                        flavor: flavor.name,
                        location,
                        cases: 1,
                    });
                }
                // Decrement available count when allocating from incoming orders
                flavor.available -= 1;
                updateAllocationTable();
                updateFlavorButtons();
                submitButton.disabled = allocatedInventory.length === 0;
            }

            // "Allocate All" function: allocate all remaining cases for each flavor
            function allocateAll() {
                let location = locationSelect.value;
                popFlavors.forEach((flavor) => {
                    if (flavor.available > 0) {
                        let existingAllocation = allocatedInventory.find(
                            (a) => a.flavor === flavor.name && a.location === location
                        );
                        if (existingAllocation) {
                            existingAllocation.cases += flavor.available;
                        } else {
                            allocatedInventory.push({
                                flavor: flavor.name,
                                location,
                                cases: flavor.available,
                            });
                        }
                        flavor.available = 0;
                    }
                });
                updateAllocationTable();
                updateFlavorButtons();
                submitButton.disabled = allocatedInventory.length === 0;
            }

            // Update the allocation table (inventory allocation) in the right column
            function updateAllocationTable() {
                locations.forEach((location) => {
                    console.warn(locations);

                    let tbody = document.getElementById(`allocation-body-${location}`);
                    tbody.innerHTML = "";
                    console.log(allocatedInventory);


                    allocatedInventory
                        .filter((a) => a.location === location)
                        .forEach((allocation) => {
                            console.error(allocation);

                            // Retrieve the flavor object to get its image.
                            let flavorObj =
                                popFlavors.find((f) => f.name === allocation.flavor) ||
                                initialPopFlavors.find((f) => f.name === allocation.flavor);
                            let row = document.createElement("tr");
                            row.innerHTML = `
                                    <td><img src="{{asset('storage/')}}/${flavorObj.image1}" alt="${allocation.flavor}" class="flavor-img"> ${allocation.flavor}</td>
                                    <td><span class="cases-input">${allocation.cases}</span></td>
                                    <td>
                                    <button class="btn   remove-btn p-1" data-location="${allocation.location}" data-flavor="${allocation.flavor}">

                                        <svg width="25px" height="25px" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17 12C17 11.4477 16.5523 11 16 11H8C7.44772 11 7 11.4477 7 12C7 12.5523 7.44771 13 8 13H16C16.5523 13 17 12.5523 17 12Z" fill="#0F0F0F"/>
                                            <path fill-rule="currentColor" clip-rule="currentColor" d="M12 23C18.0751 23 23 18.0751 23 12C23 5.92487 18.0751 1 12 1C5.92487 1 1 5.92487 1 12C1 18.0751 5.92487 23 12 23ZM12 20.9932C7.03321 20.9932 3.00683 16.9668 3.00683 12C3.00683 7.03321 7.03321 3.00683 12 3.00683C16.9668 3.00683 20.9932 7.03321 20.9932 12C20.9932 16.9668 16.9668 20.9932 12 20.9932Z" fill="#0F0F0F"/>
                                        </svg>
                                        </button>
                                    <button class="btn  sell-button p-1" data-location="${allocation.location}" data-flavor="${allocation.flavor}">
                                        <svg width="25px" height="25px" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10.0303 8.96965C9.73741 8.67676 9.26253 8.67676 8.96964 8.96965C8.67675 9.26255 8.67675 9.73742 8.96964 10.0303L10.9393 12L8.96966 13.9697C8.67677 14.2625 8.67677 14.7374 8.96966 15.0303C9.26255 15.3232 9.73743 15.3232 10.0303 15.0303L12 13.0607L13.9696 15.0303C14.2625 15.3232 14.7374 15.3232 15.0303 15.0303C15.3232 14.7374 15.3232 14.2625 15.0303 13.9696L13.0606 12L15.0303 10.0303C15.3232 9.73744 15.3232 9.26257 15.0303 8.96968C14.7374 8.67678 14.2625 8.67678 13.9696 8.96968L12 10.9393L10.0303 8.96965Z" fill="#1C274C"/>
                                            <path fill-rule="currentColor" clip-rule="currentColor" d="M12 1.25C6.06294 1.25 1.25 6.06294 1.25 12C1.25 17.9371 6.06294 22.75 12 22.75C17.9371 22.75 22.75 17.9371 22.75 12C22.75 6.06294 17.9371 1.25 12 1.25ZM2.75 12C2.75 6.89137 6.89137 2.75 12 2.75C17.1086 2.75 21.25 6.89137 21.25 12C21.25 17.1086 17.1086 21.25 12 21.25C6.89137 21.25 2.75 17.1086 2.75 12Z" fill="#1C274C"/>
                                        </svg>
                                        </button>
                                    </td>
                                `;
                            tbody.appendChild(row);
                        });
                });

                // Remove button: returns a case back to available inventory
                document.querySelectorAll(".remove-btn").forEach((button) => {
                    button.addEventListener("click", function() {
                        let location = this.getAttribute("data-location");
                        let flavorName = this.getAttribute("data-flavor");
                        let allocation = allocatedInventory.find(
                            (a) => a.location === location && a.flavor === flavorName
                        );
                        if (allocation) {
                            let flavor = popFlavors.find((f) => f.name === flavorName);
                            flavor.available += 1;
                            allocation.cases -= 1;
                            if (allocation.cases === 0) {
                                allocatedInventory = allocatedInventory.filter(
                                    (a) => !(a.location === location && a.flavor === flavorName)
                                );
                            }
                        }
                        updateFlavorButtons();
                        updateAllocationTable();
                        if (allocatedInventory.length === 0) {
                            submitButton.disabled = true;
                        }
                    });
                });

                // Sell button: marks a case as sold (removes it from allocation, adds to sold bin)
                document.querySelectorAll(".sell-btn").forEach((button) => {
                    button.addEventListener("click", function() {
                        let location = this.getAttribute("data-location");
                        let flavorName = this.getAttribute("data-flavor");
                        let allocation = allocatedInventory.find(
                            (a) => a.location === location && a.flavor === flavorName
                        );
                        if (allocation) {
                            allocation.cases -= 1;
                            soldInventory[flavorName] =
                                (soldInventory[flavorName] || 0) + 1;
                            if (allocation.cases === 0) {
                                allocatedInventory = allocatedInventory.filter(
                                    (a) => !(a.location === location && a.flavor === flavorName)
                                );
                            }
                        }
                        updateAllocationTable();
                        updateSoldBin();
                        if (allocatedInventory.length === 0) {
                            submitButton.disabled = true;
                        }
                    });
                });
            }

            // Render the sold bin (from soldInventory) in the left column with images
            function updateSoldBin() {
                const soldContainer = document.getElementById("sold-container");
                soldContainer.innerHTML = "";
                for (const flavor in soldInventory) {
                    if (soldInventory[flavor] > 0) {
                        let button = document.createElement("button");
                        button.classList.add("btn", "btn-success", "btn-sm", "sold-btn");
                        // Retrieve the flavor object to get its image.
                        let flavorObj =
                            popFlavors.find((f) => f.name === flavor) ||
                            initialPopFlavors.find((f) => f.name === flavor);
                        button.innerHTML =
                            `<img src="${flavorObj.image1}" alt="${flavor}" class="flavor-img"> ${flavor} (${soldInventory[flavor]})`;
                        button.setAttribute("data-flavor", flavor);
                        soldContainer.appendChild(button);
                    }
                }

                // Clicking a sold flavor button reverses a sale: reallocate it to the currently selected location.
                document.querySelectorAll(".sold-btn").forEach((button) => {
                    button.addEventListener("click", function() {
                        let flavorName = this.getAttribute("data-flavor");
                        let flavor = popFlavors.find((f) => f.name === flavorName);
                        // Reverse the sale: add one case back to available inventory
                        flavor.available += 1;
                        addToAllocation(flavor);
                        soldInventory[flavorName] -= 1;
                        updateSoldBin();
                    });
                });
            }

            // Reset: restore initial page load arrays (simulated as reloading from the database)
            function resetAllocations() {
                loadInitialData();
                updateFlavorButtons();
                updateAllocationTable();
                renderFlavors();
                updateSoldBin();
                renderLocationDropdown();
                renderLocationSections();
                submitButton.disabled = allocatedInventory.length === 0;
            }

            // Event listener for the reset button
            resetButton.addEventListener("click", resetAllocations);
            allocateAllButton.addEventListener("click", allocateAll);
            submitButton.addEventListener('click', function() {
                // alert(allocatedInventory);
                console.log(allocatedInventory);
                submitButton.disabled = true;
                $.ajax({
                    type: "POST",
                    url: "{{ route('franchise.allocate-inventory') }}",
                    data: {
                        allocatedInventory: allocatedInventory
                    },
                    headers: {
                        'x-csrf-token': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.error) {
                            console.error(response.message);
                            submitButton.disabled = true;
                            return;
                        }
                        document.getElementById('success-msg').style.display = 'block';
                        setTimeout(() => {
                            // alert("Order submitted successfully")
                            window.location.reload()
                        }, 2000);
                    }
                });
            })

            // Initial render calls
            renderLocationDropdown();
            renderLocationSections();
            renderFlavors();

            renderLocationSections();
            updateAllocationTable();
            updateFlavorButtons();
        });


        $(document).ready(function () {
        // Quantity Button Click
        $(document).on('click', '.remove-btn', function () {
            let button = $(this);
            let location = button.data('location');
            let flavor = button.data('flavor');

            $.ajax({
                url: '{{ route('franchise.updateQuantity') }}',
                type: 'POST',
                data: {
                    location: location,
                    flavor: flavor,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    // Optional: remove row if needed
                    if (response.deleted) {
                        button.closest('tr').remove();
                    }
                },
                error: function (xhr) {
                    alert('Error updating quantity');
                }
            });
        });


        // Remove Button Click
        $(document).on('click', '.sell-button', function () {
    let button = $(this);
    let location = button.data('location');
    let flavor = button.data('flavor');

    $.ajax({
        url: '{{ route('franchise.removeItem') }}',
        type: 'POST',
        data: {
            location: location,
            flavor: flavor,
            _token: '{{ csrf_token() }}'
        },
        success: function (response) {
            button.closest('tr').remove();
        },
        error: function (xhr) {
            alert('Error removing item');
        }
    });
});


    });
    </script>
@endsection
