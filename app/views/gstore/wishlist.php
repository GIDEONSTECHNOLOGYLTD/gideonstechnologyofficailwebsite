<?php require APP_PATH . '/views/layouts/header.php'; ?>

<div class="wishlist-container">
    <div class="container">
        <div class="wishlist-header">
            <h1>My Wishlist</h1>
            <p>Your saved items for later</p>
        </div>

        <?php if (empty($wishlistItems)): ?>
        <div class="wishlist-empty">
            <i class="fa fa-heart"></i>
            <h2>Your wishlist is empty</h2>
            <p>Add items to your wishlist to save them for later</p>
            <a href="/gstore" class="btn btn-primary">
                <i class="fa fa-shopping-cart"></i> Start Shopping
            </a>
        </div>
        <?php else: ?>
        <div class="wishlist-content">
            <div class="wishlist-items">
                <?php foreach ($wishlistItems as $item): ?>
                <div class="wishlist-item">
                    <div class="wishlist-item-image">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>">
                    </div>
                    <div class="wishlist-item-details">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <div class="wishlist-item-price">
                            $<?php echo number_format($item['price'], 2); ?>
                            <?php if ($item['discount']): ?>
                            <span class="original-price">
                                $<?php echo number_format($item['original_price'], 2); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="wishlist-item-actions">
                            <div class="action-buttons">
                                <button class="btn btn-outline-primary" 
                                        onclick="addToCart(<?php echo htmlspecialchars($item['id']); ?>)">
                                    <i class="fa fa-shopping-cart"></i> Add to Cart
                                </button>
                                <button class="btn btn-outline-danger" 
                                        onclick="removeFromWishlist(<?php echo htmlspecialchars($item['id']); ?>)">
                                    <i class="fa fa-trash"></i> Remove
                                </button>
                            </div>
                            <div class="wishlist-item-meta">
                                <span class="wishlist-date">
                                    Added on <?php echo date('M d, Y', strtotime($item['created_at'])); ?>
                                </span>
                                <span class="wishlist-notes">
                                    <?php echo htmlspecialchars($item['notes'] ?? ''); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="wishlist-actions">
                <button class="btn btn-primary" onclick="selectAllItems()">
                    <i class="fa fa-check-square"></i> Select All
                </button>
                <button class="btn btn-danger" onclick="removeSelectedItems()">
                    <i class="fa fa-trash"></i> Remove Selected
                </button>
                <button class="btn btn-success" onclick="addToCartSelected()">
                    <i class="fa fa-shopping-cart"></i> Add Selected to Cart
                </button>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.wishlist-container {
    padding: 40px 0;
}

.wishlist-header {
    text-align: center;
    margin-bottom: 50px;
}

.wishlist-header h1 {
    font-size: 2.5em;
    color: #2c3e50;
    margin-bottom: 15px;
}

.wishlist-empty {
    text-align: center;
    padding: 80px 0;
    color: #666;
}

.wishlist-empty i {
    font-size: 4em;
    color: #ddd;
    margin-bottom: 30px;
}

.wishlist-content {
    background: white;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.wishlist-items {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}

.wishlist-item {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
}

.wishlist-item:hover {
    transform: translateY(-5px);
}

.wishlist-item-image {
    width: 200px;
    height: 200px;
    margin-bottom: 20px;
    border-radius: 5px;
    overflow: hidden;
}

.wishlist-item-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.wishlist-item-details h3 {
    color: #2c3e50;
    margin-bottom: 15px;
}

.wishlist-item-price {
    color: #e74c3c;
    font-weight: bold;
    font-size: 1.2em;
    margin-bottom: 15px;
}

.original-price {
    color: #666;
    text-decoration: line-through;
    margin-left: 10px;
}

.wishlist-item-actions {
    margin-top: 20px;
}

.action-buttons {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.wishlist-item-meta {
    color: #666;
    font-size: 0.9em;
}

.wishlist-date {
    display: block;
    margin-bottom: 5px;
}

.wishlist-notes {
    display: block;
    color: #3498db;
}

.wishlist-actions {
    display: flex;
    gap: 20px;
    margin-top: 30px;
}

@media (max-width: 768px) {
    .wishlist-empty {
        padding: 40px 20px;
    }
    
    .wishlist-content {
        padding: 20px;
    }
    
    .wishlist-items {
        grid-template-columns: 1fr;
    }
    
    .wishlist-item {
        padding: 15px;
    }
    
    .wishlist-item-image {
        width: 150px;
        height: 150px;
    }
    
    .wishlist-actions {
        flex-direction: column;
        gap: 10px;
    }
}
</style>

<script>
function addToCart(productId) {
    fetch(`/gstore/add-to-cart/${productId}`, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Product added to cart');
            removeFromWishlist(productId);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding to cart');
    });
}

function removeFromWishlist(productId) {
    if (confirm('Are you sure you want to remove this item from your wishlist?')) {
        fetch(`/gstore/remove-from-wishlist/${productId}`, {
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
            alert('Error removing from wishlist');
        });
    }
}

function selectAllItems() {
    const items = document.querySelectorAll('.wishlist-item');
    items.forEach(item => {
        item.classList.add('selected');
    });
}

function removeSelectedItems() {
    if (confirm('Are you sure you want to remove selected items?')) {
        const selectedItems = document.querySelectorAll('.wishlist-item.selected');
        selectedItems.forEach(item => {
            const productId = item.dataset.productId;
            removeFromWishlist(productId);
        });
    }
}

function addToCartSelected() {
    const selectedItems = document.querySelectorAll('.wishlist-item.selected');
    selectedItems.forEach(item => {
        const productId = item.dataset.productId;
        addToCart(productId);
    });
}

// Add wishlist toggle functionality
function toggleWishlist(productId) {
    const button = document.getElementById(`wishlist-${productId}`);
    const isWishlisted = button.classList.contains('wishlisted');
    
    fetch(`/gstore/toggle-wishlist/${productId}`, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.classList.toggle('wishlisted');
            button.innerHTML = isWishlisted 
                ? '<i class="fa fa-heart"></i> Add to Wishlist' 
                : '<i class="fa fa-heart-o"></i> Remove from Wishlist';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error toggling wishlist');
    });
}
</script>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
