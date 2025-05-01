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
    <!--**********************************
                    Content body start
                ***********************************-->
    <div class="content-body default-height">
        <!-- row -->
        <div class="container-fluid">


            <div class="container mt-4">
                <!-- Left Column: Location Selector, Incoming Orders, and Sold Bin -->
                <div class="left-column">
                    <label for="" class="form-label"><strong>Select Location:</strong></label>
                    <select id="location-select" class="form-select mb-3">
                        <option value="Van 1">Van 1</option>
                        <option value="Van 2">Van 2</option>
                        <option value="Trailer 1">Trailer 1</option>
                        <option value="Freezer 1">Freezer 1</option>
                        <option value="Freezer 2">Freezer 2</option>
                    </select>

                    <strong>Delivered Pop Flavors</strong>
                    <p>Click to allocate a case.</p>
                    <!-- Allocate All button -->
                    <button id="allocate-all-btn" class="btn btn-primary me-2">
                        Allocate All
                    </button>
                    <div id="flavor-container">
                        @foreach ($flavors as $flavor)
                            <div class="flavor-item">
                                <img src="{{$flavor->image1}}" onerror="this.onerror=null; this.src='https://support.heberjahiz.com/hc/article_attachments/21013076295570';" alt="{{{$flavor->name}}}" class="flavor-img" data-flavor="{{$flavor->name}}" onclick="addToAllocation({{$flavor}})"> {{$flavor->name}} ({{$flavor->availableQuantity()}});
                            </div>
                        @endforeach 
                    </div>

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
        
        const allocatedInventory = [
            // Example: { flavor: "Strawberry", location: "Van 1", cases: 2, image1: }
          ];
        
        locationSelect = document.getElementById('location-select');
          function addToAllocation(flavor) {
            console.log(flavor.availableQuantity);
            
            let location = locationSelect.value;
            if (flavor.availableQuantity <= 0) return;
  
            // Check if an allocation entry already exists for this flavor and location.
            let existingAllocation = allocatedInventory.find(
              (a) => a.flavor === flavor.name && a.location === location
            );
            if (existingAllocation) {
              existingAllocation.cases += 1;
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
            console.log(allocatedInventory);
            
        }

        
          // Update the allocation table (inventory allocation) in the right column
          function updateAllocationTable() {
            locations.forEach((location) => {
              let tbody = document.getElementById(`allocation-body-${location}`);
              tbody.innerHTML = "";
  
              allocatedInventory
                .filter((a) => a.location === location)
                .forEach((allocation) => {
                  // Retrieve the flavor object to get its image.
                  let flavorObj =
                    popFlavors.find((f) => f.name === allocation.flavor) ||
                    initialPopFlavors.find((f) => f.name === allocation.flavor);
                  let row = document.createElement("tr");
                  row.innerHTML = `
                  <td><img src="${flavorObj.image1}" alt="${allocation.flavor}" class="flavor-img"> ${allocation.flavor}</td>
                  <td><span class="cases-input">${allocation.cases}</span></td>
                  <td>
                    <button class="btn btn-danger btn-sm remove-btn" data-location="${allocation.location}" data-flavor="${allocation.flavor}"><i class="fas fa-minus-circle">−</i></button>
                    <button class="btn btn-success btn-sm sell-btn" data-location="${allocation.location}" data-flavor="${allocation.flavor}">$</button>
                  </td>
                `;
                  tbody.appendChild(row);
                });
            });
  
            // Remove button: returns a case back to available inventory
            document.querySelectorAll(".remove-btn").forEach((button) => {
              button.addEventListener("click", function () {
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
              button.addEventListener("click", function () {
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

    document.addEventListener("DOMContentLoaded", function () {
          /*
           * Simulated data loaded from database tables.
           * For real-world usage, these arrays would be populated via API calls or server-side rendering.
           */
  
          // Initial locations (from the "locations" table)
          const initialLocations = [
            "Van 1",
            "Van 2",
            "Trailer 1",
            "Freezer 1",
            "Freezer 2",
          ];
  
          // Incoming orders table data: Available Pop Flavors
          // Now each flavor has an "image1" attribute with the image URL.
          const initialPopFlavors = [
            {
              name: "Chocolate",
              available: 10,
              image1:
                "https://friospops.wpenginepowered.com/wp-content/uploads/2019/11/Chocolate-Frios-Pop.jpg",
            },
            {
              name: "Strawberry",
              available: 15,
              image1:
                "https://friospops.wpenginepowered.com/wp-content/uploads/2020/08/Strawberry-2048x2048.jpg",
            },
            {
              name: "Blue Raspberry",
              available: 12,
              image1:
                "https://friospops.wpenginepowered.com/wp-content/uploads/2020/08/Blue-Raspberry-300x300.jpg",
            },
            {
              name: "Caramel Sea Salt",
              available: 8,
              image1:
                "https://friospops.wpenginepowered.com/wp-content/uploads/2019/11/Caramel-Sea-Salt-Frios-Pop.jpg",
            },
            {
              name: "Fruity Pebbles",
              available: 20,
              image1:
                "https://friospops.wpenginepowered.com/wp-content/uploads/2020/07/Fruity-Pebbles-1024x1024.jpg",
            },
          ];
  
          // Inventory allocation data from the inventory table (preset to empty or some initial allocation)
          const initialAllocatedInventory = [
            // Example: { flavor: "Strawberry", location: "Van 1", cases: 2, image1: }
          ];
  
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
        //   const locationSelect = document.getElementById("location-select");
          const allocateAllButton = document.getElementById("allocate-all-btn");
  
          // Render location dropdown (from locations table)
        //   function renderLocationDropdown() {
        //     locationSelect.innerHTML = "";
        //     locations.forEach((location) => {
        //       let option = document.createElement("option");
        //       option.value = location;
        //       option.textContent = location;
        //       locationSelect.appendChild(option);
        //     });
        //   }
  
          // Render delivered pop flavors (arrived orders) with images
        //   function renderFlavors() {
        //     flavorContainer.innerHTML = "";
        //     popFlavors.forEach((flavor) => {
        //       let div = document.createElement("div");
        //       div.classList.add("flavor-item");
        //       // Include image next to the flavor name and available count
        //       div.innerHTML = `<img src="${flavor.image1}" alt="${flavor.name}" class="flavor-img"> ${flavor.name} (${flavor.available})`;
        //       div.dataset.flavor = flavor.name;
        //       div.addEventListener("click", () => addToAllocation(flavor));
        //       flavorContainer.appendChild(div);
        //     });
        //   }
  
          // Update flavor buttons to reflect current available counts (include images)
          function updateFlavorButtons() {
            document.querySelectorAll(".flavor-item").forEach((div) => {
              let flavor = popFlavors.find((f) => f.name === div.dataset.flavor);
              div.innerHTML = `<img src="${flavor.image1}" alt="${flavor.name}" class="flavor-img"> ${flavor.name} (${flavor.available})`;
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
              existingAllocation.cases += 1;
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
              let tbody = document.getElementById(`allocation-body-${location}`);
              tbody.innerHTML = "";
  
              allocatedInventory
                .filter((a) => a.location === location)
                .forEach((allocation) => {
                  // Retrieve the flavor object to get its image.
                  let flavorObj =
                    popFlavors.find((f) => f.name === allocation.flavor) ||
                    initialPopFlavors.find((f) => f.name === allocation.flavor);
                  let row = document.createElement("tr");
                  row.innerHTML = `
                  <td><img src="${flavorObj.image1}" alt="${allocation.flavor}" class="flavor-img"> ${allocation.flavor}</td>
                  <td><span class="cases-input">${allocation.cases}</span></td>
                  <td>
                    <button class="btn btn-danger btn-sm remove-btn" data-location="${allocation.location}" data-flavor="${allocation.flavor}"><i class="fas fa-minus-circle">−</i></button>
                    <button class="btn btn-success btn-sm sell-btn" data-location="${allocation.location}" data-flavor="${allocation.flavor}">$</button>
                  </td>
                `;
                  tbody.appendChild(row);
                });
            });
  
            // Remove button: returns a case back to available inventory
            document.querySelectorAll(".remove-btn").forEach((button) => {
              button.addEventListener("click", function () {
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
              button.addEventListener("click", function () {
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
                button.innerHTML = `<img src="${flavorObj.image1}" alt="${flavor}" class="flavor-img"> ${flavor} (${soldInventory[flavor]})`;
                button.setAttribute("data-flavor", flavor);
                soldContainer.appendChild(button);
              }
            }
  
            // Clicking a sold flavor button reverses a sale: reallocate it to the currently selected location.
            document.querySelectorAll(".sold-btn").forEach((button) => {
              button.addEventListener("click", function () {
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
            // renderFlavors();
            updateSoldBin();
            // renderLocationDropdown();
            renderLocationSections();
            submitButton.disabled = allocatedInventory.length === 0;
          }
  
          // Event listener for the reset button
          resetButton.addEventListener("click", resetAllocations);
          allocateAllButton.addEventListener("click", allocateAll);
  
          // Initial render calls
        //   renderLocationDropdown();
          renderLocationSections();
        //   renderFlavors();
        });
      </script>
@endsection

