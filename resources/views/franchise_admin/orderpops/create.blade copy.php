@extends('layouts.app') {{-- or whatever your layout is --}}

@section('content')
<div class="content-body default-height">
  <div class="container-fluid">
    <div class="container py-4">
      <h2 class="mb-4">Order Pops</h2>

      <form id="order-form"
            method="POST"
            action="{{ route('franchise.orderpops.confirm') }}">
        @csrf

        {{-- hidden JSON payload loaded in confirmOrder() in controller--}}
        <input type="hidden"
               name="ordered_items"
               id="ordered_items_input">

        {{-- POP SELECTION: three columns --}}
        <div id="pop-selection" class="row mb-3">
          <div class="col text-center" id="availability-col">
            <h5>Availability</h5>
            <div class="d-flex flex-wrap gap-3 justify-content-center mt-3"></div>
          </div>
          <div class="col text-center" id="flavor-col">
            <h5>Flavor</h5>
            <div class="d-flex flex-wrap gap-3 justify-content-center mt-3"></div>
          </div>
          <div class="col text-center" id="allergen-col">
            <h5>Allergen</h5>
            <div class="d-flex flex-wrap gap-3 justify-content-center mt-3"></div>
          </div>
        </div>

        {{-- CART --}}
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Cart</h4>
            <div id="cart-display" class="d-flex flex-wrap gap-3 justify-content-center mt-3"></div>
            <div>
              <strong>Total:</strong>
              $<span id="cart-total">0.00</span>
            </div>
            <button type="submit"
                    class="btn btn-primary mt-3">
              Confirm Order
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const pops = @json($orderableInventory);

  const cols = {
    availability: document.querySelector('#availability-col .d-flex'),
    flavor:       document.querySelector('#flavor-col       .d-flex'),
    allergen:     document.querySelector('#allergen-col     .d-flex'),
  };

  let cart = {};
  const orderedInput = document.getElementById('ordered_items_input');

  function createPopCard(pop) {
    const wrapper = document.createElement('div');
    wrapper.classList.add('pop-card','position-relative','me-2','mb-2');
    wrapper.addEventListener('click', () => addToCart(pop));

    const img = document.createElement('img');
    img.src   = pop.image1;
    img.alt   = pop.name;
    img.title = pop.name;
    img.classList.add('img-fluid','pop-icon');
    wrapper.appendChild(img);

    const costBadge = document.createElement('div');
    costBadge.classList.add(
      'badge','bg-success',
      'position-absolute','bottom-0','start-0','translate-middle-y'
    );
    costBadge.textContent = `$${parseFloat(pop.unit_cost).toFixed(2)}`;
    wrapper.appendChild(costBadge);

    return wrapper;
  }

  function renderPops() {
    // clear
    Object.values(cols).forEach(c => c.innerHTML = '');

    Object.entries(cols).forEach(([catKey, container]) => {
      // find all sub‐categories
      const subs = Array.from(new Set(
        pops.flatMap(p => p[catKey] || [])
      ));

      subs.forEach(sub => {
        const section = document.createElement('div');
        section.classList.add('w-100','mb-4');

        const h5 = document.createElement('h5');
        h5.textContent = sub;
        // heading spacing (optional, you can keep mb-2 if you like)
        h5.classList.add('mb-2');
        section.appendChild(h5);

        // ← this is the important bit:
        const row = document.createElement('div');
        row.classList.add('d-flex','flex-wrap','gap-2','mt-4');

        pops
          .filter(p => p[catKey]?.includes(sub))
          .forEach(p => row.appendChild(createPopCard(p)));

        section.appendChild(row);
        container.appendChild(section);
      });
    });
  }

  function updateCart() {
    const cartDisplay = document.getElementById('cart-display');
    cartDisplay.innerHTML = '';
    let total = 0;

    Object.values(cart).forEach(item => {
      total += item.unit_cost * item.quantity;

      const wr = document.createElement('div');
      wr.classList.add('position-relative','me-2','mb-2');

      const img = document.createElement('img');
      img.src = item.image1;
      img.alt = item.name;
      img.classList.add('img-fluid');
      img.style.cursor = 'pointer';
      img.addEventListener('click', () => removeFromCart(item.fgp_item_id));
      wr.appendChild(img);

      const qty = document.createElement('span');
      qty.classList.add(
        'badge','bg-primary',
        'position-absolute','top-0','start-100','translate-middle'
      );
      qty.textContent = item.quantity;
      wr.appendChild(qty);

      cartDisplay.appendChild(wr);
    });

    document.getElementById('cart-total').textContent = `$${total.toFixed(2)}`;

    orderedInput.value = JSON.stringify(
      Object.values(cart).map(i => ({
        fgp_item_id: i.fgp_item_id,
        name:        i.name,
        unit_cost:   parseFloat(i.unit_cost).toFixed(2),
        quantity:    i.quantity
      }))
    );
  }

  function addToCart(pop) {
    const id = pop.fgp_item_id;
    if (!cart[id]) cart[id] = {...pop, quantity: 0};
    cart[id].quantity++;
    updateCart();
  }

  function removeFromCart(id) {
    if (!cart[id]) return;
    if (cart[id].quantity > 1) {
      cart[id].quantity--;
    } else {
      delete cart[id];
    }
    updateCart();
  }

  document.addEventListener('DOMContentLoaded', () => {
    renderPops();
    updateCart();
  });
</script>
@endpush



