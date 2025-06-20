<?php require APP_PATH . '/views/layouts/header.php'; ?>

<div class="cart-container">
    <div class="container">
        <div class="cart-header">
            <h1>Shopping Cart</h1>
            <p>Review your items before checkout</p>
        </div>

        <?php if (empty($cartItems)): ?>
        <div class="cart-empty">
            <i class="fa fa-shopping-cart"></i>
            <h2>Your cart is empty</h2>
            <p>Add some items to your cart to proceed with checkout</p>
            <a href="/gstore" class="btn btn-primary">
                <i class="fa fa-arrow-left"></i> Continue Shopping
            </a>
        </div>
        <?php else: ?>
        <div class="cart-content">
            <div class="cart-items">
                <div class="cart-header-row">
                    <div class="cart-item-name">Product</div>
                    <div class="cart-item-price">Price</div>
                    <div class="cart-item-quantity">Quantity</div>
                    <div class="cart-item-total">Total</div>
                    <div class="cart-item-remove">Remove</div>
                </div>

                <?php foreach ($cartItems as $item): ?>
                <div class="cart-item">
                    <div class="cart-item-name">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="item-variant"><?php echo htmlspecialchars($item['variant']); ?></p>
                        </div>
                    </div>
                    <div class="cart-item-price">
                        $<?php echo number_format($item['price'], 2); ?>
                    </div>
                    <div class="cart-item-quantity">
                        <div class="quantity-selector">
                            <button class="btn btn-outline-secondary" 
                                    onclick="updateQuantity(<?php echo htmlspecialchars($item['id']); ?>, -1)">
                                <i class="fa fa-minus"></i>
                            </button>
                            <input type="number" 
                                   id="quantity-<?php echo htmlspecialchars($item['id']); ?>" 
                                   value="<?php echo htmlspecialchars($item['quantity']); ?>"
                                   min="1" 
                                   max="10">
                            <button class="btn btn-outline-secondary" 
                                    onclick="updateQuantity(<?php echo htmlspecialchars($item['id']); ?>, 1)">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="cart-item-total">
                        $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                    </div>
                    <div class="cart-item-remove">
                        <button class="btn btn-link" 
                                onclick="removeItem(<?php echo htmlspecialchars($item['id']); ?>)">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <div class="summary-item">
                    <span>Subtotal</span>
                    <span>$<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="summary-item">
                    <span>Shipping</span>
                    <span>$<?php echo number_format($shipping, 2); ?></span>
                </div>
                <div class="summary-item discount" <?php echo $discount ? '' : 'style="display: none;"'; ?>>
                    <span>Discount</span>
                    <span>-$<?php echo number_format($discount, 2); ?></span>
                </div>
                <div class="summary-item total">
                    <span>Total</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </div>

                <div class="cart-actions">
                    <a href="/gstore" class="btn btn-outline-primary">
                        <i class="fa fa-arrow-left"></i> Continue Shopping
                    </a>
                    <button class="btn btn-primary" onclick="proceedToCheckout()">
                        <i class="fa fa-credit-card"></i> Proceed to Checkout
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.cart-container {
    padding: 40px 0;
}

.cart-header {
    text-align: center;
    margin-bottom: 50px;
}

.cart-header h1 {
    font-size: 2.5em;
    color: #2c3e50;
    margin-bottom: 15px;
}

.cart-empty {
    text-align: center;
    padding: 80px 0;
    color: #666;
}

.cart-empty i {
    font-size: 4em;
    color: #ddd;
    margin-bottom: 30px;
}

.cart-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 40px;
}

.cart-items {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.cart-header-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
    margin-bottom: 20px;
    font-weight: bold;
    color: #666;
}

.cart-item {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
    padding: 20px 0;
    border-bottom: 1px solid #eee;
}

.cart-item:last-child {
    border-bottom: none;
}

.cart-item-name {
    display: flex;
    align-items: center;
    gap: 20px;
}

.cart-item-name img {
    width: 80px;
    height: 80px;
    object-fit: contain;
}

.item-details h3 {
    color: #2c3e50;
    margin-bottom: 5px;
}

.item-details .item-variant {
    color: #666;
    font-size: 0.9em;
}

.quantity-selector {
    display: flex;
    align-items: center;
    gap: 10px;
}

.quantity-selector input {
    width: 60px;
    text-align: center;
}

.cart-item-total,
.cart-item-price {
    text-align: right;
    color: #2c3e50;
}

.cart-item-remove button {
    color: #e74c3c;
}

.cart-summary {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
    color: #666;
}

.summary-item.total {
    font-weight: bold;
    color: #2c3e50;
    margin-top: 20px;
}

.summary-item.total span:last-child {
    color: #e74c3c;
}

.cart-actions {
    display: flex;
    gap: 20px;
    margin-top: 30px;
}

@media (max-width: 768px) {
    .cart-content {
        grid-template-columns: 1fr;
    }
    
    .cart-header-row,
    .cart-item {
        grid-template-columns: 2fr 1fr 1fr 1fr;
    }
    
    .cart-item-remove {
        display: none;
    }
    
    .cart-actions {
        flex-direction: column;
        gap: 10px;
    }
}
</style>

<script>
function updateQuantity(itemId, change) {
    const quantityInput = document.getElementById(`quantity-${itemId}`);
    const newQuantity = parseInt(quantityInput.value) + change;
    
    if (newQuantity < 1) return;
    
    quantityInput.value = newQuantity;
    
    fetch(`/gstore/update-cart/${itemId}`, {
        method: 'POST',
        body: JSON.stringify({ quantity: newQuantity }),
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartSummary(data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating quantity');
    });
}

function removeItem(itemId) {
    if (confirm('Are you sure you want to remove this item?')) {
        fetch(`/gstore/remove-from-cart/${itemId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error removing item');
        });
    }
}

function proceedToCheckout() {
    window.location.href = '/gstore/checkout';
}

function updateCartSummary(data) {
    const subtotalElement = document.querySelector('.summary-item:first-child span:last-child');
    const shippingElement = document.querySelector('.summary-item:nth-child(2) span:last-child');
    const discountElement = document.querySelector('.summary-item:nth-child(3)');
    const totalElement = document.querySelector('.summary-item:last-child span:last-child');

    if (subtotalElement) subtotalElement.textContent = `$${data.subtotal.toFixed(2)}`;
    if (shippingElement) shippingElement.textContent = `$${data.shipping.toFixed(2)}`;
    
    if (data.discount > 0) {
        if (discountElement) {
            discountElement.style.display = 'flex';
            discountElement.querySelector('span:last-child').textContent = `-$${data.discount.toFixed(2)}`;
        }
    } else {
        if (discountElement) discountElement.style.display = 'none';
    }
    
    if (totalElement) totalElement.textContent = `$${data.total.toFixed(2)}`;
}
</script>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
