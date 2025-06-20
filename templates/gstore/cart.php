<?php
/**
 * GStore Cart Page Template
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - <?= htmlspecialchars($appName ?? 'Gideon\'s Technology') ?></title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .cart-container {
            padding: 30px 0;
        }
        .cart-heading {
            margin-bottom: 30px;
            position: relative;
            padding-bottom: 15px;
        }
        .cart-heading:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: #3c66a0;
        }
        .cart-item {
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #eee;
            border-radius: 5px;
        }
        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }
        .cart-summary {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
        }
        .quantity-input {
            width: 70px;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../partials/header.php'; ?>

    <div class="container cart-container">
        <h1 class="cart-heading">Shopping Cart</h1>
        
        <?php if (empty($cartItems)): ?>
            <div class="alert alert-info">
                Your cart is empty. <a href="/gstore/products" class="alert-link">Browse our products</a> to add items to your cart.
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-8">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <img src="/assets/images/products/<?= htmlspecialchars($item['product']['image']) ?>" alt="<?= htmlspecialchars($item['product']['name']) ?>" onerror="this.src='/assets/images/placeholder.jpg'">
                                </div>
                                <div class="col-md-4">
                                    <h5><a href="/gstore/product/<?= $item['product']['id'] ?>"><?= htmlspecialchars($item['product']['name']) ?></a></h5>
                                    <p class="text-muted"><?= htmlspecialchars($item['product']['description']) ?></p>
                                </div>
                                <div class="col-md-2">
                                    <?php if ($item['product']['on_sale'] ?? false): ?>
                                        <span class="text-danger">$<?= number_format($item['product']['sale_price'], 2) ?></span>
                                        <small class="text-muted"><del>$<?= number_format($item['product']['price'], 2) ?></del></small>
                                    <?php else: ?>
                                        <span>$<?= number_format($item['product']['price'], 2) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control quantity-input" min="1" value="<?= $item['quantity'] ?>" data-product-id="<?= $item['product']['id'] ?>">
                                </div>
                                <div class="col-md-2 text-end">
                                    <strong>$<?= number_format($item['total'], 2) ?></strong>
                                    <button class="btn btn-sm btn-danger remove-item" data-product-id="<?= $item['product']['id'] ?>"><i class="fa fa-trash"></i> Remove</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="col-md-4">
                    <div class="cart-summary">
                        <h4>Order Summary</h4>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <strong>$<?= number_format($cartTotal, 2) ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax:</span>
                            <strong>$<?= number_format($cartTotal * 0.1, 2) ?></strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span>Total:</span>
                            <strong>$<?= number_format($cartTotal * 1.1, 2) ?></strong>
                        </div>
                        
                        <a href="/gstore/checkout" class="btn btn-primary btn-lg w-100">Proceed to Checkout</a>
                        <a href="/gstore/products" class="btn btn-outline-secondary w-100 mt-2">Continue Shopping</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include __DIR__ . '/../partials/footer.php'; ?>
    
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle quantity updates
            const quantityInputs = document.querySelectorAll('.quantity-input');
            
            quantityInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const productId = this.getAttribute('data-product-id');
                    const quantity = parseInt(this.value);
                    
                    if (quantity < 1) {
                        this.value = 1;
                        return;
                    }
                    
                    updateCartItem(productId, quantity);
                });
            });
            
            // Handle item removal
            const removeButtons = document.querySelectorAll('.remove-item');
            
            removeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    removeCartItem(productId);
                });
            });
            
            // Update cart item quantity
            function updateCartItem(productId, quantity) {
                fetch('/gstore/api/cart/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: quantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload page to show updated cart
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the cart.');
                });
            }
            
            // Remove item from cart
            function removeCartItem(productId) {
                if (confirm('Are you sure you want to remove this item from your cart?')) {
                    fetch('/gstore/api/cart/remove', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reload page to show updated cart
                            window.location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while removing the item from cart.');
                    });
                }
            }
        });
    </script>
</body>
</html>