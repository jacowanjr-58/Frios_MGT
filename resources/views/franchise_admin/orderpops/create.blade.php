{{-- resources/views/order_pops/create.blade.php --}}
@extends('layouts.app')
@section('content')
<div class="content-body default-height">
    <div class="container-fluid px-4 py-5">
        <!-- Header Section -->
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold text-primary mb-3">Build Your Pop Order</h1>
            <p class="lead text-muted">Select your favorite pops and build your perfect order</p>
        </div>

        <!-- Cart Section -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-10">
                <div class="card border-0 cart-card">
                    <div class="card-header bg-gradient text-white text-center py-3">
                        <h3 class="mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Your Cart
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <div id="cart" class="cart-container mb-4">
                            <div class="empty-cart text-center text-muted py-5">
                                <i class="fas fa-shopping-bag fa-3x mb-3 opacity-50"></i>
                                <p class="h5">Your cart is empty</p>
                                <p>Click on items below to add them to your cart</p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0 text-primary">Total Amount:</h4>
                            <h3 class="mb-0 fw-bold text-success">$<span id="cart-total">0.00</span></h3>
                        </div>
                        
                        <form id="order-form" action="{{ route('franchise.orderpops.confirm', ['franchise' => $franchise]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="ordered_items" id="ordered_items_input" value="">
                            <input type="hidden" name="is_paid" value="0">
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5 py-3 rounded-pill" id="review-btn" disabled>
                                    <i class="fas fa-eye me-2"></i>
                                    Review Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="row">
            @foreach($categories as $parentCategory)
            <div class="col-lg-4 mb-4">
                <div class="category-card h-100">
                    <div class="category-header text-center py-3 mb-4">
                        <h4 class="mb-0 fw-bold text-white">{{ $parentCategory->name }}</h4>
                    </div>
                    
                    @foreach($parentCategory->children as $subcategory)
                    <div class="subcategory-section mb-4">
                        <div class="subcategory-header text-center py-2 mb-3">
                            <h6 class="mb-0 fw-semibold">{{ $subcategory->name }}</h6>
                        </div>
                        
                        <div class="products-grid">
                            @foreach($subcategory->items as $item)
                            <div class="product-card" 
                                 data-id="{{ $item->fgp_item_id ?? $item->id }}" 
                                 data-name="{{ $item->name }}"
                                 data-unit-cost="{{ $item->case_cost }}" 
                                 data-image="{{ asset('storage/' . $item->image1) }}"
                                 data-debug-fgp-id="{{ $item->fgp_item_id }}"
                                 data-debug-regular-id="{{ $item->id }}">
                                <div class="product-image-wrapper">
                                    <img src="{{ asset('storage/' . $item->image1) }}" 
                                         alt="{{ $item->name }}" 
                                         class="product-image">
                                    <div class="product-overlay">
                                        <i class="fas fa-plus-circle fa-2x"></i>
                                    </div>
                                </div>
                                <div class="product-info text-center p-3">
                                    <h6 class="product-name mb-2">{{ $item->name }}</h6>
                                    <div class="product-price fw-bold text-primary">
                                        ${{ number_format($item->case_cost, 2) }}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
:root {
    --primary-color: #4f46e5;
    --primary-dark: #3730a3;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --light-bg: #f8fafc;
    --border-color: #e2e8f0;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
}

.text-primary {
    color: var(--primary-color) !important;
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

.btn-primary:disabled {
    background: #9ca3af;
    transform: none;
    cursor: not-allowed;
}

/* Cart Styles */
.cart-card {
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.cart-card:hover {
    transform: translateY(-5px);
}

.bg-gradient {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
}

.cart-container {
    min-height: 120px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 15px;
    padding: 20px;
    background: var(--light-bg);
    border-radius: 15px;
    border: 2px dashed var(--border-color);
    transition: all 0.3s ease;
}

.cart-container.has-items {
    border-color: var(--primary-color);
    background: #f0f9ff;
    border-style: solid;
}

.cart-container.empty {
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Mobile cart layout */
@media (max-width: 768px) {
    .cart-container {
        grid-template-columns: 1fr;
        gap: 12px;
        padding: 15px;
    }
}

.empty-cart {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.cart-item {
    background: white;
    border-radius: 15px;
    padding: 15px;
    box-shadow: var(--shadow-md);
    transition: all 0.3s ease;
    width: 280px;
    border: 2px solid transparent;
    display: flex;
    align-items: center;
    gap: 15px;
}

.cart-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-color);
}

.cart-image-wrapper {
    position: relative;
    width: 70px;
    height: 70px;
    border-radius: 10px;
    overflow: hidden;
    flex-shrink: 0;
}

.cart-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.cart-item-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.cart-item-name {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    line-height: 1.2;
    margin: 0;
}

.cart-item-price {
    font-size: 13px;
    color: var(--primary-color);
    font-weight: 500;
}

.cart-item-controls {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 5px;
}

.quantity-controls {
    display: flex;
    align-items: center;
    background: var(--light-bg);
    border-radius: 8px;
    border: 1px solid var(--border-color);
    overflow: hidden;
}

.qty-btn {
    background: white;
    border: none;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--primary-color);
    font-weight: bold;
    transition: all 0.2s ease;
}

.qty-btn:hover {
    background: var(--primary-color);
    color: white;
}

.qty-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.qty-display {
    padding: 0 12px;
    font-weight: 600;
    font-size: 14px;
    color: #374151;
    min-width: 30px;
    text-align: center;
}

.cart-total-display {
    font-size: 13px;
    font-weight: 600;
    color: var(--success-color);
}

.remove-item-btn {
    background: var(--danger-color);
    color: white;
    border: none;
    font-size: 12px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.remove-item-btn:hover {
    background: #dc2626;
    transform: scale(1.1);
}

/* Mobile responsiveness for cart items */
@media (max-width: 768px) {
    .cart-item {
        width: 100%;
        max-width: 300px;
        padding: 12px;
        gap: 12px;
    }
    
    .cart-image-wrapper {
        width: 60px;
        height: 60px;
    }
    
    .cart-item-name {
        font-size: 13px;
    }
    
    .qty-btn {
        width: 24px;
        height: 24px;
    }
}

/* Category Styles */
.category-card {
    background: white;
    border-radius: 20px;
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid var(--border-color);
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
}

.category-header {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    margin: -1px -1px 0 -1px;
}

.subcategory-header {
    background: linear-gradient(135deg, #6b7280, #4b5563);
    color: white;
    border-radius: 10px;
    margin: 0 15px;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 15px;
    padding: 0 15px 15px;
}

/* Product Card Styles */
.product-card {
    background: white;
    border-radius: 15px;
    box-shadow: var(--shadow-sm);
    transition: all 0.3s ease;
    cursor: pointer;
    border: 2px solid transparent;
    overflow: hidden;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-color);
}

.product-image-wrapper {
    position: relative;
    overflow: hidden;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.1);
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(79, 70, 229, 0.9);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
}

.product-card:hover .product-overlay {
    opacity: 1;
}

.product-info {
    padding: 15px;
}

.product-name {
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    line-height: 1.3;
    margin-bottom: 8px;
    min-height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-price {
    font-size: 16px;
    color: var(--primary-color);
}

/* Responsive Design */
@media (max-width: 768px) {
    .products-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    
    .cart-item {
        width: 80px;
    }
    
    .cart-image-wrapper {
        width: 50px;
        height: 60px;
    }
    
    .category-card {
        margin-bottom: 20px;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding: 15px;
    }
    
    .display-4 {
        font-size: 2rem;
    }
    
    .cart-container {
        padding: 15px;
        gap: 10px;
    }
}

/* Animation for cart updates */
@keyframes cartItemAdded {
    0% {
        transform: scale(0) rotate(180deg);
        opacity: 0;
    }
    50% {
        transform: scale(1.2) rotate(90deg);
        opacity: 0.8;
    }
    100% {
        transform: scale(1) rotate(0deg);
        opacity: 1;
    }
}

.cart-item.newly-added {
    animation: cartItemAdded 0.5s ease-out;
}

/* Success feedback */
.product-card.added-feedback {
    border-color: var(--success-color);
    background: #f0fdf4;
}

.product-card.added-feedback .product-overlay {
    background: rgba(16, 185, 129, 0.9);
    opacity: 1;
}

.product-card.added-feedback .product-overlay i:before {
    content: "\f00c";
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  let orderedItems = [];

  function updateCart() {
    const cartEl = document.getElementById('cart');
    const totalEl = document.getElementById('cart-total');
    const reviewBtn = document.getElementById('review-btn');
    
    cartEl.innerHTML = '';
    let total = 0;
    
    console.log('Updating cart display with', orderedItems.length, 'items');

    if (orderedItems.length === 0) {
      cartEl.innerHTML = `
        <div class="empty-cart text-center text-muted py-5">
          <i class="fas fa-shopping-bag fa-3x mb-3 opacity-50"></i>
          <p class="h5">Your cart is empty</p>
          <p>Click on items below to add them to your cart</p>
        </div>
      `;
      cartEl.classList.remove('has-items');
      cartEl.classList.add('empty');
      reviewBtn.disabled = true;
    } else {
      cartEl.classList.add('has-items');
      cartEl.classList.remove('empty');
      reviewBtn.disabled = false;
      
      orderedItems.forEach((item, index) => {
        const itemTotal = item.case_cost * item.quantity;
        total += itemTotal;
        
        const div = document.createElement('div');
        div.className = 'cart-item';
        div.innerHTML = `
          <div class="cart-image-wrapper">
            <img src="${item.image}" alt="${item.name}">
          </div>
          <div class="cart-item-details">
            <h6 class="cart-item-name">${item.name}</h6>
            <div class="cart-item-price">$${item.case_cost.toFixed(2)} each</div>
            <div class="cart-item-controls">
              <div class="quantity-controls">
                <button type="button" class="qty-btn decrease-btn" data-id="${item.id}" title="Decrease quantity">
                  <i class="fas fa-minus"></i>
                </button>
                <span class="qty-display">${item.quantity}</span>
                <button type="button" class="qty-btn increase-btn" data-id="${item.id}" title="Increase quantity">
                  <i class="fas fa-plus"></i>
                </button>
              </div>
              <div class="cart-total-display">$${itemTotal.toFixed(2)}</div>
            </div>
          </div>
          <button type="button" class="remove-item-btn" data-id="${item.id}" title="Remove from cart">
            <i class="fas fa-trash"></i>
          </button>
        `;
        
        // Add animation for newly added items
        if (item.isNewlyAdded) {
          div.classList.add('newly-added');
          delete item.isNewlyAdded;
        }
        
        cartEl.appendChild(div);
      });
    }

    totalEl.textContent = total.toFixed(2);
    document.getElementById('ordered_items_input').value = JSON.stringify(orderedItems);
    
    console.log('Cart updated - Total items:', orderedItems.length, 'Total amount: $' + total.toFixed(2));
    console.log('Review button enabled:', !reviewBtn.disabled);
  }

  // Add to cart with improved feedback
  document.querySelectorAll('.product-card').forEach(el => {
    el.addEventListener('click', () => {
      const id = el.dataset.id;
      const name = el.dataset.name;
      const case_cost = parseFloat(el.dataset.unitCost);
      const image = el.dataset.image;
      
      // Debug the data attributes
      console.log('=== ITEM CLICK DEBUG ===');
      console.log('Element:', el);
      console.log('data-id:', el.dataset.id);
      console.log('data-name:', el.dataset.name);
      console.log('data-unit-cost:', el.dataset.unitCost);
      console.log('Debug - fgp_item_id:', el.dataset.debugFgpId);
      console.log('Debug - regular id:', el.dataset.debugRegularId);
      console.log('All dataset:', el.dataset);
      
      // Validate and fix ID if needed
      let finalId = id;
      if (!id || id.trim() === '') {
        console.warn('Primary ID is empty, trying fallbacks...');
        finalId = el.dataset.debugFgpId || el.dataset.debugRegularId;
        console.log('Using fallback ID:', finalId);
      }
      
      if (!finalId || finalId.trim() === '') {
        console.error('ERROR: No valid ID found!');
        console.error('Available IDs - fgp_item_id:', el.dataset.debugFgpId, 'regular id:', el.dataset.debugRegularId);
        alert('Error: Item ID is missing. Please refresh the page and try again.');
        return;
      }
      
      const existing = orderedItems.find(i => i.id == finalId);

      if (existing) {
        existing.quantity++;
        console.log('Increased quantity for existing item:', existing);
      } else {
        const newItem = { 
          id: finalId, 
          name, 
          case_cost, 
          priceValue: case_cost, // Add for confirm page compatibility
          image, 
          quantity: 1,
          isNewlyAdded: true 
        };
        orderedItems.push(newItem);
        console.log('Added new item to cart:', newItem);
      }

      // Visual feedback
      el.classList.add('added-feedback');
      setTimeout(() => {
        el.classList.remove('added-feedback');
      }, 600);

      // Store in sessionStorage for confirm page
      sessionStorage.setItem('orderItems', JSON.stringify(orderedItems));
      console.log('SUCCESS: Item added to cart');
      console.log('Final cart items:', orderedItems);
      console.log('Cart now has', orderedItems.length, 'items');
      console.log('SessionStorage updated:', JSON.parse(sessionStorage.getItem('orderItems')));

      updateCart();
    });
  });

  // Enhanced cart interaction handlers
  document.getElementById('cart').addEventListener('click', e => {
    const target = e.target.closest('button');
    if (!target) return;
    
    const id = target.dataset.id;
    const idx = orderedItems.findIndex(i => i.id == id);
    
    if (idx === -1) return;
    
    if (target.classList.contains('increase-btn')) {
      // Increase quantity
      orderedItems[idx].quantity++;
    } else if (target.classList.contains('decrease-btn')) {
      // Decrease quantity
      orderedItems[idx].quantity--;
      if (orderedItems[idx].quantity < 1) {
        orderedItems.splice(idx, 1);
      }
    } else if (target.classList.contains('remove-item-btn')) {
      // Remove item completely
      orderedItems.splice(idx, 1);
    }
    
    // Update sessionStorage
    sessionStorage.setItem('orderItems', JSON.stringify(orderedItems));
    console.log('Cart interaction - Updated items in sessionStorage, total items:', orderedItems.length);
    
    updateCart();
  });

  // Form submission validation
  document.getElementById('order-form').addEventListener('submit', function(e) {
    console.log('Form submitted with', orderedItems.length, 'items');
    console.log('Items being submitted:', orderedItems);
    
    if (orderedItems.length === 0) {
      e.preventDefault();
      alert('Please add items to your cart before reviewing the order.');
      return false;
    }
    
    // Ensure sessionStorage is updated before navigation
    sessionStorage.setItem('orderItems', JSON.stringify(orderedItems));
    console.log('Final sessionStorage update before form submission');
  });

  // Initialize cart and clear any old sessionStorage
  sessionStorage.removeItem('orderItems');
  updateCart();
  console.log('Cart system initialized');
});
</script>
@endpush






