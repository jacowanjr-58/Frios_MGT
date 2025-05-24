@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Order Pops</h2>

    <div id="pop-selection" class="d-flex flex-wrap gap-4 mb-5"></div>

    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Cart</h4>
            <div id="cart-display" class="d-flex flex-wrap gap-3"></div>
            <div class="mt-3">
                <strong>Total:</strong> $<span id="cart-total">0</span>
                <pre>
Categories: {{ json_encode($categoriesData, JSON_PRETTY_PRINT) }}
Inventory: {{ json_encode($orderableInventory, JSON_PRETTY_PRINT) }}
</pre>
            </div>
        </div>
    </div>
</div>

<script>
    const categoriesData = {!! json_encode($categoriesData) !!};
    const inventory = {!! json_encode($orderableInventory) !!};
</script>

<script>
    

    const cart = {};
    const cartTotalEl = document.getElementById('cart-total');
    const cartDisplayEl = document.getElementById('cart-display');
    const popSelectionEl = document.getElementById('pop-selection');

    function renderInventory() {
        categoriesData.forEach(cat => {
            const col = document.createElement('div');
            col.className = 'border p-3 rounded';
            col.style.minWidth = '280px';
            const title = document.createElement('h5');
            title.innerText = cat.category;
            col.appendChild(title);

            cat.options.forEach(option => {
                const section = document.createElement('div');
                section.className = 'mb-3';
                const label = document.createElement('strong');
                label.innerText = option;
                section.appendChild(label);

                const row = document.createElement('div');
                row.className = 'd-flex flex-wrap gap-2 mt-2';

                inventory.forEach(item => {
                    if (item[cat.category.toLowerCase()].includes(option)) {
                        const icon = document.createElement('img');
                        icon.src = item.image;
                        icon.title = item.type + " ($" + item.price + ")";
                        icon.style.width = '50px';
                        icon.style.cursor = 'pointer';
                        icon.onclick = () => addToCart(item);
                        row.appendChild(icon);
                    }
                });

                section.appendChild(row);
                col.appendChild(section);
            });

            popSelectionEl.appendChild(col);
        });
    }

    function addToCart(item) {
        const key = item.type;
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
        Object.keys(cart).forEach(key => {
            const entry = cart[key];
            const wrapper = document.createElement('div');
            wrapper.className = 'position-relative';

            const img = document.createElement('img');
            img.src = entry.image;
            img.style.width = '60px';
            wrapper.appendChild(img);

            const qty = document.createElement('div');
            qty.innerText = entry.quantity;
            qty.className = 'badge bg-primary position-absolute top-0 start-0';
            wrapper.appendChild(qty);

            const removeBtn = document.createElement('button');
            removeBtn.innerText = '-';
            removeBtn.className = 'btn btn-danger btn-sm position-absolute bottom-0 end-0';
            removeBtn.onclick = () => removeFromCart(key);
            wrapper.appendChild(removeBtn);

            cartDisplayEl.appendChild(wrapper);
            total += entry.quantity * entry.price;
        });
        cartTotalEl.innerText = total;
    }

    renderInventory();
</script>
@endsection