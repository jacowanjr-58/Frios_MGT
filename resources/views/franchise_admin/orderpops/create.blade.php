@extends('layouts.app')

@section('content')
        <div class="content-body default-height">
            <!-- row -->
			<div class="container-fluid">
                <div class="container py-4">
                    <h2 class="mb-4">Order Pops</h2>

                    <form id="order-form" method="POST" action="{{ route('franchise.orderpops.confirm') }}">
                        @csrf
                        <input type="hidden" name="ordered_items" id="ordered_items_input">

                        <div id="pop-selection" class="d-flex flex-wrap gap-4 mb-5"></div>

                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Cart</h4>
                                <div id="cart-display" class="d-flex flex-wrap gap-3"></div>
                                <div class="mt-3">
                                    <strong>Total:</strong> $<span id="cart-total">0.00</span>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">Confirm Order</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>    

<script>
    const orderableInventory = @json($orderableInventory);
    const categories = ['Availability', 'Flavor', 'Allergen'];
    const matrix = {};

    categories.forEach(category => {
        matrix[category] = {};
    });

    orderableInventory.forEach(item => {
    categories.forEach(category => {
        const subtypes = item[category.toLowerCase()] || [];
        subtypes.forEach(sub => {
            if (!matrix[category][sub]) matrix[category][sub] = [];
            matrix[category][sub].push(item);
        });
    });
});

    const cart = {};
    const cartTotalEl = document.getElementById('cart-total');
    const cartDisplayEl = document.getElementById('cart-display');
    const popSelectionEl = document.getElementById('pop-selection');
    const orderedItemsInput = document.getElementById('ordered_items_input');

    function renderMatrix() {
        Object.entries(matrix).forEach(([category, subtypes]) => {
            const col = document.createElement('div');
            col.className = 'p-3';

            const catTitle = document.createElement('h4');
            catTitle.innerText = category;
            col.appendChild(catTitle);

            Object.entries(subtypes).forEach(([sub, pops]) => {
                const subHeader = document.createElement('div');
                subHeader.innerHTML = `<strong>${sub}</strong>`;
                col.appendChild(subHeader);

                const row = document.createElement('div');
                row.className = 'd-flex flex-wrap gap-2 mb-4';

                pops.forEach(pop => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'position-relative';
                    wrapper.style.width = '60px';
                    wrapper.style.cursor = 'pointer';

                    const img = document.createElement('img');
                    img.src = pop.image;
                    img.title = pop.type;
                    img.className = 'img-fluid';
                    img.onclick = () => addToCart(pop);

                    const overlay = document.createElement('div');
                    overlay.innerText = `$${pop.price}`;
                    overlay.className = 'position-absolute bottom-0 start-0 bg-dark text-white w-100 text-center small';

                    wrapper.appendChild(img);
                    wrapper.appendChild(overlay);
                    row.appendChild(wrapper);
                });

                col.appendChild(row);
            });

            popSelectionEl.appendChild(col);
        });
    }

    function addToCart(item) {
        const key = item.fgp_item_id;
        if (!cart[key]) {
            cart[key] = { ...item, quantity: 0 };
        }
        cart[key].quantity += 1;
        renderCart();
    }

    function removeFromCart(key) {
        if (cart[key]) {
            cart[key].quantity -= 1;
            if (cart[key].quantity <= 0) delete cart[key];
            renderCart();
        }
    }

    function renderCart() {
        cartDisplayEl.innerHTML = '';
        let total = 0;
        const itemsArray = [];

        Object.keys(cart).forEach(key => {
            const entry = cart[key];
            const wrapper = document.createElement('div');
            wrapper.className = 'position-relative';
            wrapper.style.cursor = 'pointer';

            const img = document.createElement('img');
            img.src = entry.image;
            img.style.width = '60px';
            img.onclick = () => removeFromCart(key);

            const qty = document.createElement('div');
            qty.innerText = entry.quantity;
            qty.className = 'badge bg-primary position-absolute top-0 start-0';

            wrapper.appendChild(img);
            wrapper.appendChild(qty);
            cartDisplayEl.appendChild(wrapper);

            total += entry.quantity * entry.price;
            itemsArray.push({
                fgp_item_id: entry.fgp_item_id,
                type: entry.type,
                price: entry.price,
                quantity: entry.quantity,
            });
        });

        orderedItemsInput.value = JSON.stringify(itemsArray);
        cartTotalEl.innerText = total.toFixed(2);
    }

    renderMatrix();
</script>
@endsection
