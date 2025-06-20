/**
 * GStore Cart Management
 * Client-side JavaScript for managing shopping cart
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart count
    updateCartCount();
    
    // Add event listeners to "Add to Cart" buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const productId = this.getAttribute('data-id');
            addToCart(productId);
        });
    });
    
    // Add event listeners to quantity inputs in cart
    const quantityInputs = document.querySelectorAll('.cart-quantity');
    
    quantityInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const productId = this.getAttribute('data-id');
            const quantity = parseInt(this.value);
            updateCartItem(productId, quantity);
        });
    });
    
    // Add event listeners to remove buttons in cart
    const removeButtons = document.querySelectorAll('.remove-from-cart');
    
    removeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const productId = this.getAttribute('data-id');
            removeFromCart(productId);
        });
    });
});

/**
 * Add a product to the cart
 * 
 * @param {string} productId The ID of the product to add
 * @param {number} quantity The quantity to add (default: 1)
 */
function addToCart(productId, quantity = 1) {
    // Show loading state
    const button = document.querySelector(`.add-to-cart[data-id="${productId}"]`);
    if (button) {
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
        button.disabled = true;
    }
    
    // Send AJAX request to add item to cart
    fetch('/gstore/api/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count in UI
            updateCartCount(data.cart_count);
            
            // Show success message
            showNotification('Success', 'Product added to cart', 'success');
        } else {
            showNotification('Error', data.error || 'Could not add product to cart', 'error');
        }
        
        // Restore button state
        if (button) {
            setTimeout(() => {
                button.innerHTML = '<i class="fas fa-check"></i> Added to Cart';
                
                setTimeout(() => {
                    button.innerHTML = '<i class="fas fa-cart-plus me-2"></i>Add to Cart';
                    button.disabled = false;
                }, 1500);
            }, 500);
        }
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
        showNotification('Error', 'An unexpected error occurred', 'error');
        
        // Restore button state
        if (button) {
            button.innerHTML = '<i class="fas fa-cart-plus me-2"></i>Add to Cart';
            button.disabled = false;
        }
    });
}

/**
 * Update the quantity of a cart item
 * 
 * @param {string} productId The ID of the product to update
 * @param {number} quantity The new quantity
 */
function updateCartItem(productId, quantity) {
    // If quantity is zero or negative, remove the item
    if (quantity <= 0) {
        removeFromCart(productId);
        return;
    }
    
    // Send AJAX request to update item in cart
    fetch('/gstore/api/cart/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count in UI
            updateCartCount(data.cart_count);
            
            // Recalculate subtotals
            recalculateCart();
            
            // Show success message
            showNotification('Cart Updated', data.message, 'success');
        } else {
            showNotification('Error', data.error || 'Could not update cart', 'error');
            
            // Reset the input to the previous value
            // In a real app, you would need to store the previous value somehow
            const input = document.querySelector(`.cart-quantity[data-id="${productId}"]`);
            if (input) {
                input.value = 1; // Default fallback
            }
        }
    })
    .catch(error => {
        console.error('Error updating cart:', error);
        showNotification('Error', 'An unexpected error occurred', 'error');
    });
}

/**
 * Remove a product from the cart
 * 
 * @param {string} productId The ID of the product to remove
 */
function removeFromCart(productId) {
    // Send AJAX request to remove item from cart
    fetch('/gstore/api/cart/remove', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            product_id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count in UI
            updateCartCount(data.cart_count);
            
            // Remove the row from the cart table
            const row = document.querySelector(`.cart-item[data-id="${productId}"]`);
            if (row) {
                row.remove();
            }
            
            // Recalculate subtotals
            recalculateCart();
            
            // Show success message
            showNotification('Item Removed', data.message, 'success');
            
            // If cart is now empty, reload to show empty cart message
            if (data.cart_count === 0) {
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        } else {
            showNotification('Error', data.error || 'Could not remove item from cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error removing from cart:', error);
        showNotification('Error', 'An unexpected error occurred', 'error');
    });
}

/**
 * Update the cart count display in the UI
 * 
 * @param {number} count The new cart count (optional)
 */
function updateCartCount(count = null) {
    const cartCountElement = document.getElementById('cart-count');
    
    if (cartCountElement) {
        if (count !== null) {
            cartCountElement.textContent = count;
            
            // Show/hide based on count
            if (count > 0) {
                cartCountElement.style.display = 'inline-block';
            } else {
                cartCountElement.style.display = 'none';
            }
        } else {
            // If count not provided, fetch it from the server
            fetch('/gstore/api/cart/count', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cartCountElement.textContent = data.cart_count;
                    
                    // Show/hide based on count
                    if (data.cart_count > 0) {
                        cartCountElement.style.display = 'inline-block';
                    } else {
                        cartCountElement.style.display = 'none';
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching cart count:', error);
            });
        }
    }
}

/**
 * Recalculate cart totals
 */
function recalculateCart() {
    const cartItems = document.querySelectorAll('.cart-item');
    let subtotal = 0;
    
    cartItems.forEach(item => {
        const price = parseFloat(item.getAttribute('data-price'));
        const quantity = parseInt(item.querySelector('.cart-quantity').value);
        const itemSubtotal = price * quantity;
        
        // Update item subtotal display
        const subtotalElement = item.querySelector('.item-subtotal');
        if (subtotalElement) {
            subtotalElement.textContent = '$' + itemSubtotal.toFixed(2);
        }
        
        subtotal += itemSubtotal;
    });
    
    // Update cart subtotal
    const subtotalElement = document.getElementById('cart-subtotal');
    if (subtotalElement) {
        subtotalElement.textContent = '$' + subtotal.toFixed(2);
    }
    
    // Calculate tax (assuming 8% tax rate)
    const taxRate = 0.08;
    const tax = subtotal * taxRate;
    
    // Update tax amount
    const taxElement = document.getElementById('cart-tax');
    if (taxElement) {
        taxElement.textContent = '$' + tax.toFixed(2);
    }
    
    // Calculate total
    const total = subtotal + tax;
    
    // Update total amount
    const totalElement = document.getElementById('cart-total');
    if (totalElement) {
        totalElement.textContent = '$' + total.toFixed(2);
    }
}

/**
 * Show a notification message
 * 
 * @param {string} title The notification title
 * @param {string} message The notification message
 * @param {string} type The notification type (success, error, warning, info)
 */
function showNotification(title, message, type = 'info') {
    // Check if the toast container exists
    let toastContainer = document.querySelector('.toast-container');
    
    // If not, create it
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    // Create a unique ID for this toast
    const toastId = 'toast-' + Date.now();
    
    // Determine background color based on type
    let bgClass = 'bg-info';
    if (type === 'success') bgClass = 'bg-success';
    if (type === 'error') bgClass = 'bg-danger';
    if (type === 'warning') bgClass = 'bg-warning';
    
    // Create the toast HTML
    const toastHtml = `
    <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header ${bgClass} text-white">
            <strong class="me-auto">${title}</strong>
            <small>Just now</small>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            ${message}
        </div>
    </div>
    `;
    
    // Add the toast to the container
    toastContainer.innerHTML += toastHtml;
    
    // Initialize the toast
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: 5000
    });
    
    // Show the toast
    toast.show();
    
    // Remove the toast element after it's hidden
    toastElement.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}