{{-- resources/views/order_pops/create.blade.php --}}
@extends('layouts.app')
@section('content')
<div class="content-body default-height">
    <!-- row -->
    <div class="container-fluid">
        <div class="form-head mb-4 ml-12 mt-24 pt-10 d-flex flex-wrap align-items-center max-w-fit">
            <div class="container py-4">
                <h1 class="mb-4 text-center">Build Your Pop Order</h1>

                <form id="order-form" action="{{ route('franchise.orderpops.confirm') }}" method="POST">
                    @csrf
                    {{-- Hidden payload --}}
                    <input type="hidden" name="ordered_items" id="ordered_items_input" value="">

                    {{-- Cart display --}}
                    <div class="text-center ">
                        <span class="inline-flex">
                            <h3 class="text-center mb-3">Your Cart</h3>

                        </span>
                    </div>
                    <div id="cart"
                        class="d-flex flex-wrap gap-3 p-3 border rounded bg-light justify-content-center mb-2">


                    </div>
                    <h5 class="text-center">Total: $<span id="cart-total">0.00</span></h5>

                    <div class="row mb-4">
                        <button type="submit" class="btn-default  btn-lg">Review Order</button>
                    </div>
                </form>



                {{-- Three columns --}}
                <div class="row mb-4">
                    @foreach(['Availability','Flavor','Allergen'] as $colType)
                    <div class="col-md-4 border-start @if($colType==='Availability') border-0 @endif p-4">
                        <h4 class="text-center bg-dark text-white py-2 rounded">{{ $colType }}</h4>

                        @foreach($categoriesByType[$colType] ?? [] as $category)
                        {{-- Sub‐category heading --}}
                        <div class="subcategory-label bg-secondary text-white text-center py-1 my-3 rounded">
                            {{ $category->name }}
                        </div>

                        {{-- Pops in this subcategory --}}
                        <div class="d-flex flex-wrap gap-3 justify-content-center">
                            @foreach($category->items as $item)
                            <div class="pop-icon text-center" data-id="{{ $item->fgp_item_id }}"
                                data-name="{{ $item->name }}" data-unit-cost="{{ $item->case_cost }}"
                                data-image="{{ asset('storage/' . $item->image1) }}">
                                <img src="{{ asset('storage/' . $item->image1) }}" alt="{{ $item->name }}"
                                    class="pop-image rounded">
                                <div class="unit-cost">${{ number_format($item->case_cost, 2) }}</div>
                                <div class="pop-overlay">{{ $item->name }}</div>
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>


            </div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
.pop-icon {
  position: relative;
  width: 100px;
  padding: 10px;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.08);
  cursor: pointer;
  display: flex;
  flex-direction: column;
  align-items: center;
  transition: transform 0.2s ease-in-out;
}

.pop-icon:hover {
  transform: scale(1.04);
}

.pop-image {
  width: 60px;
  height: 90px;
  object-fit: cover;
  display: block;
  border-radius: 6px;
}

.unit-cost {
  font-weight: bold;
  margin-top: 6px;
  font-size: 0.85rem;
}

.pop-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(0, 0, 0, 0.7);
  color: #fff;
  padding: 2px 0;
  font-size: 12px;
  opacity: 0;
  transition: opacity 0.2s ease-in-out;
}

.pop-icon:hover .pop-overlay {
  opacity: 1;
}

.cart-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 70px;
  margin-bottom: 6px;
}

.cart-image-wrapper {
  position: relative;
  width: 60px;
  height: 60px;
  overflow: hidden;
  border-radius: 5px;
  flex-shrink: 0;
}

.cart-image-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.cart-name {
  font-size: 11px;
  text-align: center;
  margin-top: 4px;
  max-width: 100%;
  line-height: 1.1;
  word-break: break-word;
}

.quantity {
  position: absolute;
  top: 0;
  left: 0;
  background: rgba(0, 0, 0, 0.7);
  color: #fff;
  font-size: 12px;
  padding: 1px 4px;
  border-bottom-right-radius: 3px;
}



.remove-btn {
  position: absolute;
  bottom: 0;
  right: 0;
  background: red;
  color: white;
  border: none;
  font-size: 12px;
  padding: 1px 4px;
  border-top-left-radius: 3px;
  cursor: pointer;
}

.border-start {
  border-left: 1px solid #dee2e6 !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  let orderedItems = [];

  function updateCart() {
    const cartEl   = document.getElementById('cart');
    const totalEl  = document.getElementById('cart-total');
    cartEl.innerHTML = '';
    let total = 0;

    orderedItems.forEach(item => {
      total += item.case_cost * item.quantity;
      const div = document.createElement('div');
      div.className = 'cart-item';
        div.innerHTML = `
        <div class="cart-image-wrapper">
          <img src="${item.image}" alt="${item.name}">
          <div class="quantity">${item.quantity}</div>
          <button type="button" class="remove-btn" data-id="${item.id}">&times;</button>
        </div>
      <div class="cart-name">${item.name}</div>
      `;
      cartEl.appendChild(div);
    });

    totalEl.textContent = total.toFixed(2);
    document.getElementById('ordered_items_input').value = JSON.stringify(orderedItems);
  }

  // Add to cart
  document.querySelectorAll('.pop-icon').forEach(el => {
    el.addEventListener('click', () => {
      const id        = el.dataset.id;
      const name      = el.dataset.name;
      const case_cost = parseFloat(el.dataset.unitCost);
      const image     = el.dataset.image;
      const existing  = orderedItems.find(i => i.id == id);

      if (existing) {
        existing.quantity++;
      } else {
        orderedItems.push({ id, name, case_cost, image, quantity: 1 });
      }
      updateCart();
    });
  });

  // Remove / decrement
  document.getElementById('cart').addEventListener('click', e => {
    if (!e.target.matches('.remove-btn')) return;
    const id  = e.target.dataset.id;
    const idx = orderedItems.findIndex(i => i.id == id);
    if (idx > -1) {
      orderedItems[idx].quantity--;
      if (orderedItems[idx].quantity < 1) orderedItems.splice(idx, 1);
      updateCart();
    }
  });
});
</script>
@endpush






