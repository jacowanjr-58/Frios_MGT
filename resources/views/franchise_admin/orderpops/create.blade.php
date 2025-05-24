@extends('layouts.app')
@section('content')
    <style>
        .kanban-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 100%;
        }

        .row-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 20px;
            width: 100%;
        }

        .category-column {
            flex: 1;
            background: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
            min-height: 200px;
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 20px;
        }

        .column-title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }

        /* Slot styling: arranged as flex container */
        .slot {
            border: 2px dashed #ccc;
            padding: 5px;
            background: white;
            margin-bottom: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            position: relative;
        }

        .slot>div:first-child {
            font-weight: bold;
            margin-bottom: 5px;
            flex-basis: 100%;
        }

        /* Show two cases per row in a slot */
        .slot>.case {
            flex: 0 0 calc(25% - 5px);
        }

        /* Cart drop zone: flex container that wraps */
        .cart-slot {
            background: #e3f2fd;
            border: 2px solid #007bff;
            min-height: 120px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 10px;
            align-items: center;
            justify-content: flex-start;
        }

        /* Case styling: container for the image */
        .case {
            position: relative;
            cursor: grab;
            border-radius: 5px;
            transition: transform 0.2s, background 0.2s;
            border: none;
            margin: 2px 0;
            overflow: hidden;
        }

        .case:hover {
            transform: scale(1.1);
        }

        /* Image inside the case */
        .case img {
            width: 100%;
            display: block;
        }

        /* Price overlay on the inventory items remains unchanged */
        .price-overlay {
            position: absolute;
            bottom: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 2px 5px;
            font-size: 10px;
            border-top-left-radius: 5px;
        }

        /* Cart item styling */
        .cart-item {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 5px;
            overflow: hidden;
        }

        .cart-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .quantity-overlay {
            position: absolute;
            top: 0;
            left: 0;
            background: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 2px 5px;
            font-size: 12px;
            border-bottom-right-radius: 5px;
        }

        .cart-item button {
            position: absolute;
            bottom: 0;
            right: 0;
            background: red;
            color: white;
            border: none;
            padding: 2px 5px;
            cursor: pointer;
            border-radius: 3px;
            font-size: 12px;
        }

        .cart-item button:hover {
            background: darkred;
        }
    </style>
    <!--**********************************
                Content body start
            ***********************************-->
    <div class="content-body default-height">
        <!-- row -->
        <div class="container-fluid">

            <div class="form-head mb-4 d-flex flex-wrap align-items-center">
                <div class="me-auto">
                    <h2 class="font-w600 mb-0">Dashboard \</h2>
                    <p>Place Pops Order</p>
                </div>

                <a href="javascript:history.back()" class="btn btn-secondary btn-sm">
                    <i class="fa fa-arrow-left me-2"></i> Back
                </a>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="col-xl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Place Pops Order</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="basic-form">

                                            <!-- Display Success Message -->


                                                <div class="row">
                                                    <div class="kanban-container">
                                                        <!-- Container for the dynamically built nested slots -->
                                                        <div id="inventorySlots" class="row-container"></div>
                                                        {{-- {{dd($categorizedItems)}} --}}
                                                        <!-- Cart Section -->

                                                            <form action="{{ route('franchise.orderpops.confirm') }}" method="get">
                                                                @csrf
                                                                <input type="hidden" name="ordered_items" id="orderedItemsInput">

                                                                <div class="row-container">
                                                                    <div class="category-column" style="width: 100%">
                                                                        <div class="column-title" id="totalPrice">Cart Total = $0</div>
                                                                        <div class="cart-slot" id="cart" ondragover="allowDrop(event)" ondrop="drop(event)"></div>
                                                                    </div>
                                                                </div>

                                                                <button type="button" class="btn btn-primary bg-primary" onclick="submitCart()">Next</button>
                                                            </form>

                                                        <script>
function submitCart() {
    let cartItems = [];

    cart.forEach((data, caseType) => {
        console.log("Cart Data:", data);  // Debugging

        cartItems.push({
            itemId: data.itemId,  // Ensure item ID is retrieved correctly
            name: caseType,
            price: data.price,
            quantity: data.quantity,
            image: data.image
        });
    });

    console.log("Final Cart Items Before Submission:", cartItems); // Debugging

    // Create a form and submit via POST
    let form = document.createElement('form');
    form.method = 'get';
    form.action = "{{ route('franchise.orderpops.confirm') }}";

    let csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = "{{ csrf_token() }}";

    let input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'ordered_items';
    input.value = JSON.stringify(cartItems);

    form.appendChild(csrfToken);
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
}




                                                        // Allow drag and drop
                                                        function allowDrop(event) {
                                                            event.preventDefault();
                                                        }

                                                        function drop(event) {
                                                            event.preventDefault();
                                                            let draggedItem = event.dataTransfer.getData("text/html");
                                                            document.getElementById("cart").innerHTML += draggedItem;
                                                        }
                                                        </script>



                                                        {{-- <script>
                                                           function submitCart() {
    let cartItems = [];
console.log(cartItems);
    // Fetch cart items dynamically
    document.querySelectorAll('.cart-item').forEach(item => {
        let itemData = {
            name: item.dataset.name,
            quantity: item.dataset.quantity || 1,
            price: item.dataset.price,
            total: (item.dataset.price * (item.dataset.quantity || 1)).toFixed(2)
        };
        console.log("Cart Item:", itemData);
        cartItems.push(itemData);
    });

    // Log cart items before submitting
    console.log("Final Cart Items:", cartItems);

    // Store items in hidden input
    document.getElementById('orderedItemsInput').value = JSON.stringify(cartItems);
    console.log("Hidden Input Value:", document.getElementById('orderedItemsInput').value);

    // Submit the form
    document.querySelector('form').submit();
}

                                                        </script> --}}


                                                    </div>
                                                </div>



                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        // Categories Data Array; Create in php
        const categoriesData = {!! json_encode(
            collect($categorizedItems)->map(function ($categories, $type) {
                return [
                    'category' => $type,
                    'options' => array_keys($categories) // Extract category names under each type
                ];
            })->values()
        ) !!};

        const inventory = {!! json_encode(
    collect($categorizedItems)->flatMap(function ($items, $category) {
        return collect($items)->flatMap(function ($subItems, $subCategory) {
            return collect($subItems)->map(function ($item) use ($subCategory) {
                return [
                    'fgp_item_id' => $item['fgp_item_id'],
                    'type' => strtolower(trim($item['name'])), // Normalize caseType
                    'flavor' => strtoupper($subCategory),
                    'availability' => strtoupper($subCategory),
                    'allergen' => strtoupper($subCategory),
                    'price' => $item['case_cost'] ?? 0,
                    'image' => $item['image1']
                        ? asset('storage/' . $item['image1'])
                        : "https://friospops.wpenginepowered.com/wp-content/uploads/2019/11/Chocolate-Frios-Pop.jpg"
                ];
            });
        });
    })->toArray()
) !!};

        // Inventory Array with updated data (sample items shown, images can come from Image1 in DB)
        //     const inventory = [
        //  { type: "BANANA PUDDING  ", flavor: "CREAMY", availability: "SEASONAL", allergen: "", price: 65, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2019/11/Banana-Pudding-Frios-Pop.jpg"},
        // { type: "BIRTHDAY CAKES  ", flavor: "CREAMY", availability: "STEADY EDDIE", allergen: "", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2019/11/Birthday-Cake-Frios-Pop.jpg"},
        // { type: "BLACK BERRY GINGER LEMONADE", flavor: "FRUITY, No Sugar added, Dye Free", availability: "SEASONAL", allergen: "Nut free", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2019/08/Blackberry-Ginger-Lemonade-1024x1024.jpg"},
        // { type: "BLUE RASPBERRY  ", flavor: "FRUITY, Vegan, Gluten free", availability: "STEADY EDDIE", allergen: "", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2020/08/Blue-Raspberry-300x300.jpg"},
        // { type: "BLUEBERRY CHEESECAKE  ", flavor: "CREAMY", availability: "STEADY EDDIE", allergen: "", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2020/02/Blueberry-Cheesecake-POP-1024x1024.png"},
        // { type: "CARAMEL SEA SALT ", flavor: "CREAMY", availability: "STEADY EDDIE", allergen: "Nut free", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2019/11/Caramel-Sea-Salt-Frios-Pop.jpg"},
        // { type: "CHOCOLATE   ", flavor: "CREAMY", availability: "STEADY EDDIE", allergen: "Nut free", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2019/11/Chocolate-Frios-Pop.jpg"},
        // { type: "CHOCOLATE COLD BREW ", flavor: "CREAMY", availability: "SEASONAL", allergen: "", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2020/12/Chocolate-Cold-Brew.jpg"},
        // { type: "COLD BREW  ", flavor: "CREAMY", availability: "SEASONAL", allergen: "", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2020/08/Cold-Brew-1024x1024.jpg"},
        // { type: "COOKIE DOUGH  ", flavor: "CREAMY", availability: "SEASONAL", allergen: "", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2020/11/Cookie-Dough.jpg"},
        // { type: "COOKIES AND CREAM ", flavor: "CREAMY", availability: "STEADY EDDIE", allergen: "", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2020/07/Cookies-Cream-1024x1024.jpg"},
        // { type: "CREAMY COCONUT  ", flavor: "CREAMY", availability: "SEASONAL", allergen: "", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2020/08/Creamy-Coconut-1024x1024.jpg"},
        // { type: "FRUIT PUNCH  ", flavor: "FRUITY, No Sugar added, Dye Free", availability: "STEADY EDDIE", allergen: "Nut free, Wheat free, Soy Free", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2021/03/Fruit-Punch.jpg"},
        // { type: "FRUITY PEBBLES  ", flavor: "CREAMY", availability: "STEADY EDDIE", allergen: "", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2020/07/Fruity-Pebbles-1024x1024.jpg"},
        // { type: "KEY LIME PIE ", flavor: "CREAMY", availability: "STEADY EDDIE", allergen: "", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2020/02/Key-Lime-Pie-1024x1024.jpg"},
        // { type: "MINT CHOCOLATE CHIP ", flavor: "CREAMY", availability: "SEASONAL,Rotating Rhonda", allergen: "", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2019/07/Mint-Chocolate-Chip-1024x1024.jpg"},
        // { type: "ORANGE CREAM  ", flavor: "CREAMY", availability: "SEASONAL", allergen: "", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2020/02/Orange-Cream-1024x1024.jpg"},
        // { type: "PEANUT BUTTER BANANA PROTEIN", flavor: "PROTEIN", availability: "STEADY EDDIE", allergen: "", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2024/10/pbbanana-plus-1024x1024.png"},
        // { type: "PICKLE   ", flavor: "FRUITY", availability: "SEASONAL", allergen: "Nut free", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2020/08/pickle-1024x1024.jpg"},
        // { type: "PINEAPPLE MANGO  ", flavor: "FRUITY, No Sugar added, Dye Free", availability: "STEADY EDDIE", allergen: "", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2020/07/Pineapple-Mango-1024x1024.jpg"},
        // { type: "PINK LEMONADE  ", flavor: "FRUITY", availability: "STEADY EDDIE", allergen: "Nut free, Wheat free, Soy Free", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2020/07/Pink-Lemonade-1024x1024.jpg"},
        // { type: "PUMPKIN SPICE LATTE ", flavor: "CREAMY", availability: "SEASONAL", allergen: "", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2020/09/Pumpkin-Cheesecake-1024x1024.jpg"},
        // { type: "ROOT BEER FLOAT ", flavor: "CREAMY", availability: "SEASONAL", allergen: "", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2020/02/Root-Beer-Float-1024x1024.jpg"},
        // { type: "S'MORES   ", flavor: "CREAMY", availability: "SEASONAL", allergen: "", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2020/08/Smores-1024x1024.jpg"},
        // { type: "STRAWBERRIES AND CREAM ", flavor: "CREAMY", availability: "SEASONAL", allergen: "", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2024/01/straw-and-cream-1024x1024.png"},
        // { type: "STRAWBERRY MANGO", flavor: "FRUITY", availability: "STEADY EDDIE", allergen: "", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2020/07/Strawberry-Mango-1024x1024.jpg"},
        // { type: "STRAWBERRY   ", flavor: "FRUITY", availability: "STEADY EDDIE", allergen: "", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2020/08/Strawberry-2048x2048.jpg"},
        // { type: "STRAWBERRY PROTEIN  ", flavor: "PROTEIN", availability: "STEADY EDDIE", allergen: "", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2024/10/strawberry-plus-1024x1024.png"},
        // { type: "STRAWBERRY MOJITO  ", flavor: "FRUITY", availability: "SEASONAL", allergen: "", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2020/08/Strawberry-Mojito-1024x1024.jpg"},
        // { type: "WHITE PEACH LEMONADE ", flavor: "FRUITY, No Sugar added, Dye Free", availability: "SEASONAL", allergen: "Nut free, Wheat free, Soy Free", price: 70, image: "https://friospops.wpenginepowered.com/wp-content/uploads/2023/05/White-Peach-Lemonade-POP-1024x1024.png"},


        //       // ... additional inventory items as needed ...
        //     ];

        // Retrieve cart from localStorage if exists, else initialize an empty Map.
        let cart = new Map(JSON.parse(localStorage.getItem("cart")) || []);

        // Allow dropping items (used by the Cart drop zone)
        function allowDrop(event) {
            event.preventDefault();
        }

        // Build nested slots using categoriesData.
        // Slot IDs will be in the format: CATEGORY-OPTION (both uppercase, no spaces)
        function buildNestedSlots() {
            const inventorySlots = document.getElementById("inventorySlots");
            inventorySlots.innerHTML = "";
            console.log("Building nested slots...");
            categoriesData.forEach((categoryObj) => {
                console.log("Processing category:", categoryObj.category);
                const categoryContainer = document.createElement("div");
                categoryContainer.classList.add("category-column");
                const columnTitle = document.createElement("div");
                columnTitle.classList.add("column-title");
                columnTitle.textContent = categoryObj.category;
                categoryContainer.appendChild(columnTitle);
                categoryObj.options.forEach((option) => {
                    const slot = document.createElement("div");
                    slot.classList.add("slot");
                    const slotId =
                        categoryObj.category.toUpperCase().replace(/\s+/g, "") +
                        "-" +
                        option.toUpperCase().replace(/\s+/g, "");
                    slot.id = slotId;
                    const label = document.createElement("div");
                    label.textContent = option;
                    slot.appendChild(label);
                    categoryContainer.appendChild(slot);
                    console.log("Created slot:", slotId);
                });
                inventorySlots.appendChild(categoryContainer);
            });
        }

        // Create a draggable case element that shows the image,
        // uses the flavor name as a tooltip, and overlays the price.
        function createCase(type, price, imageUrl, slot) {
    const caseDiv = document.createElement("div");
    caseDiv.classList.add("case");
    caseDiv.title = type.trim();
    caseDiv.setAttribute("data-type", type.trim().toLowerCase()); // Normalize type
    caseDiv.setAttribute("data-price", price);
    caseDiv.setAttribute("data-image", imageUrl);
    caseDiv.draggable = true;
    caseDiv.ondragstart = drag;

    const img = document.createElement("img");
    img.src = imageUrl;
    img.alt = type.trim();

    const overlay = document.createElement("div");
    overlay.classList.add("price-overlay");
    overlay.textContent = `$${price}`;

    caseDiv.appendChild(img);
    caseDiv.appendChild(overlay);
    slot.appendChild(caseDiv);
}

function drag(event) {
    const source = event.currentTarget;
    event.dataTransfer.setData("caseType", source.getAttribute("data-type"));
    event.dataTransfer.setData("price", source.getAttribute("data-price"));
    event.dataTransfer.setData("image", source.getAttribute("data-image"));
}

function drop(event) {
    event.preventDefault();

    const caseType = event.dataTransfer.getData("caseType").toLowerCase().trim(); // Normalize caseType
    const price = parseInt(event.dataTransfer.getData("price"), 10);
    const image = event.dataTransfer.getData("image");

    // Find item in inventory
    const item = inventory.find(i => i.type.toLowerCase().trim() === caseType);

    if (!item) {
        alert("This item is not available in the inventory.");
        return;
    }

    const itemId = item.fgp_item_id;

    if (cart.has(caseType)) {
        const existing = cart.get(caseType);
        cart.set(caseType, { itemId, price, quantity: existing.quantity + 1, image });
    } else {
        cart.set(caseType, { itemId, price, quantity: 1, image });
    }

    updateCart();
}

        // Update the cart display and localStorage.
        function updateCart() {
            const cartSlot = document.getElementById("cart");
            cartSlot.innerHTML = "";
            let totalPrice = 0;
            cart.forEach((data, caseType) => {
                totalPrice += data.price * data.quantity;
                const cartItem = document.createElement("div");
                cartItem.classList.add("cart-item");

                // Create the image element for the cart item.
                const img = document.createElement("img");
                img.src = data.image;
                img.alt = caseType;

                // Create the quantity overlay.
                const qtyOverlay = document.createElement("div");
                qtyOverlay.classList.add("quantity-overlay");
                qtyOverlay.textContent = data.quantity;

                // Create the decrement button.
                const decButton = document.createElement("button");
                decButton.textContent = " - ";
                decButton.onclick = function () {
                    removeFromCart(caseType);
                };

                // Append elements to the cart item.
                cartItem.appendChild(img);
                cartItem.appendChild(qtyOverlay);
                cartItem.appendChild(decButton);

                cartSlot.appendChild(cartItem);
            });
            document.getElementById("totalPrice").textContent = `Cart Total = $${totalPrice}`;
            localStorage.setItem("cart", JSON.stringify(Array.from(cart.entries())));
        }

        // Remove an item from the cart.
        function removeFromCart(caseType) {
            if (cart.has(caseType)) {
                let data = cart.get(caseType);
                if (data.quantity > 1) {
                    cart.set(caseType, { price: data.price, quantity: data.quantity - 1, image: data.image });
                } else {
                    cart.delete(caseType);
                }
            }
            updateCart();
        }

        // Load inventory items into the appropriate nested slots.
        function loadInventory() {
            inventory.forEach((item) => {
                const categories = ["flavor", "availability", "allergen"];
                categories.forEach((catKey) => {
                    if (item[catKey]) {
                        const options = item[catKey].split(",");
                        options.forEach((opt) => {
                            const trimmedOpt = opt.trim();
                            if (trimmedOpt) {
                                const slotId =
                                    catKey.toUpperCase() +
                                    "-" +
                                    trimmedOpt.toUpperCase().replace(/\s+/g, "");
                                const slot = document.getElementById(slotId);
                                if (slot) {
                                    createCase(item.type, item.price, item.image, slot);
                                } else {
                                    console.warn("No slot found for:", slotId);
                                }
                            }
                        });
                    }
                });
            });
        }

        document.addEventListener("DOMContentLoaded", function () {
            buildNestedSlots();
            loadInventory();
            updateCart();
        });
    </script>
@endsection


<!-- Shipping Fields -->
<div class="mt-4">
    <label>Recipient Name</label>
    <input type="text" name="ship_to_name" class="form-input" required>
</div>
<div class="mt-2">
    <label>Address Line 1</label>
    <input type="text" name="ship_to_address1" class="form-input" required>
</div>
<div class="mt-2">
    <label>Address Line 2</label>
    <input type="text" name="ship_to_address2" class="form-input">
</div>
<div class="grid grid-cols-2 gap-4 mt-2">
    <div>
        <label>City</label>
        <input type="text" name="ship_to_city" class="form-input" required>
    </div>
    <div>
        <label>State</label>
        <input type="text" name="ship_to_state" class="form-input" required>
    </div>
</div>
<div class="grid grid-cols-2 gap-4 mt-2">
    <div>
        <label>ZIP Code</label>
        <input type="text" name="ship_to_zip" class="form-input" required>
    </div>
    <div>
        <label>Country</label>
        <input type="text" name="ship_to_country" class="form-input" value="US" required>
    </div>
</div>
<div class="mt-2">
    <label>Phone</label>
    <input type="text" name="ship_to_phone" class="form-input">
</div>
<div class="mt-2">
    <label>Shipping Method</label>
    <input type="text" name="ship_method" class="form-input">
</div>


<!-- Shipping Fields (hidden defaults) -->
<input type="hidden" name="ship_to_country" value="US">
<input type="hidden" name="ship_method" value="Standard">

<!-- Visible fields (optional) -->
<div class="mt-4">
    <label>Recipient Name</label>
    <input type="text" name="ship_to_name" class="form-input" value="{{ old('ship_to_name') }}">
    @error('ship_to_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
</div>
<div class="mt-2">
    <label>Address Line 1</label>
    <input type="text" name="ship_to_address1" class="form-input" value="{{ old('ship_to_address1') }}">
    @error('ship_to_address1') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
</div>
<div class="mt-2">
    <label>Address Line 2</label>
    <input type="text" name="ship_to_address2" class="form-input" value="{{ old('ship_to_address2') }}">
</div>
<div class="grid grid-cols-2 gap-4 mt-2">
    <div>
        <label>City</label>
        <input type="text" name="ship_to_city" class="form-input" value="{{ old('ship_to_city') }}">
        @error('ship_to_city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
    <div>
        <label>State</label>
        <input type="text" name="ship_to_state" class="form-input" value="{{ old('ship_to_state') }}">
        @error('ship_to_state') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
</div>
<div class="mt-2">
    <label>ZIP Code</label>
    <input type="text" name="ship_to_zip" class="form-input" value="{{ old('ship_to_zip') }}">
    @error('ship_to_zip') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
</div>
<div class="mt-2">
    <label>Phone</label>
    <input type="text" name="ship_to_phone" class="form-input" value="{{ old('ship_to_phone') }}">
</div>
