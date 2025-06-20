<?php require APP_PATH . '/views/layouts/header.php'; ?>

<div class="gstore-container">
    <div class="container">
        <div class="store-header">
            <h1>GStore</h1>
            <p>Your one-stop shop for technology products</p>
        </div>

        <div class="store-categories">
            <div class="row">
                <?php foreach ($categories as $category): ?>
                <div class="col-md-3">
                    <div class="category-card">
                        <div class="category-image">
                            <img src="<?php echo htmlspecialchars($category['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($category['name']); ?>">
                        </div>
                        <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                        <p><?php echo htmlspecialchars($category['description']); ?></p>
                        <a href="/gstore/products/<?php echo htmlspecialchars($category['slug']); ?>" 
                           class="btn btn-primary">
                            Shop Now
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="featured-products">
            <h2>Featured Products</h2>
            <div class="products-grid">
                <?php foreach ($featuredProducts as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="product-actions">
                            <button class="btn btn-outline-primary" 
                                    onclick="addToCart(<?php echo htmlspecialchars($product['id']); ?>)">
                                <i class="fa fa-shopping-cart"></i>
                            </button>
                            <a href="/gstore/product/<?php echo htmlspecialchars($product['id']); ?>" 
                               class="btn btn-outline-secondary">
                                <i class="fa fa-info-circle"></i>
                            </a>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-price">
                            $<?php echo number_format($product['price'], 2); ?>
                            <?php if ($product['discount']): ?>
                            <span class="discount-price">
                                $<?php echo number_format($product['original_price'], 2); ?>
                            </span>
                            <?php endif; ?>
                        </p>
                        <div class="product-rating">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <i class="fa fa-star <?php echo $i < $product['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                            <?php endfor; ?>
                            <span class="rating-count"><?php echo $product['rating_count']; ?> reviews</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="new-arrivals">
            <h2>New Arrivals</h2>
            <div class="products-grid">
                <?php foreach ($newArrivals as $product): ?>
                <div class="product-card new-arrival">
                    <div class="product-badge">New</div>
                    <div class="product-image">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="product-actions">
                            <button class="btn btn-outline-primary" 
                                    onclick="addToCart(<?php echo htmlspecialchars($product['id']); ?>)">
                                <i class="fa fa-shopping-cart"></i>
                            </button>
                            <a href="/gstore/product/<?php echo htmlspecialchars($product['id']); ?>" 
                               class="btn btn-outline-secondary">
                                <i class="fa fa-info-circle"></i>
                            </a>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-price">
                            $<?php echo number_format($product['price'], 2); ?>
                            <?php if ($product['discount']): ?>
                            <span class="discount-price">
                                $<?php echo number_format($product['original_price'], 2); ?>
                            </span>
                            <?php endif; ?>
                        </p>
                        <div class="product-rating">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <i class="fa fa-star <?php echo $i < $product['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                            <?php endfor; ?>
                            <span class="rating-count"><?php echo $product['rating_count']; ?> reviews</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
.gstore-container {
    padding: 40px 0;
}

.store-header {
    text-align: center;
    margin-bottom: 50px;
}

.store-header h1 {
    font-size: 3em;
    color: #2c3e50;
    margin-bottom: 15px;
}

.store-categories {
    margin-bottom: 50px;
}

.category-card {
    text-align: center;
    padding: 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
}

.category-card:hover {
    transform: translateY(-5px);
}

.category-image {
    width: 100%;
    height: 200px;
    margin-bottom: 20px;
    overflow: hidden;
    border-radius: 5px;
}

.category-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.category-card h3 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    margin-top: 20px;
}

.product-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
}

.product-card:hover {
    transform: translateY(-5px);
}

.product-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #e74c3c;
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 0.8em;
}

.product-image {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: transform 0.3s;
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

.product-actions {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    display: flex;
    justify-content: space-around;
    padding: 10px;
    background: rgba(255, 255, 255, 0.9);
    transform: translateY(100%);
    transition: transform 0.3s;
}

.product-card:hover .product-actions {
    transform: translateY(0);
}

.product-info {
    padding: 20px;
}

.product-info h3 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.product-price {
    color: #e74c3c;
    font-weight: bold;
    font-size: 1.2em;
}

.discount-price {
    color: #666;
    text-decoration: line-through;
    margin-left: 10px;
}

.product-rating {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 10px;
    color: #666;
}

.rating-count {
    font-size: 0.9em;
    color: #666;
}

.new-arrival {
    position: relative;
}

@media (max-width: 768px) {
    .gstore-container {
        padding: 20px 0;
    }
    
    .store-header h1 {
        font-size: 2em;
    }
    
    .category-card {
        padding: 15px;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
    
    .product-image {
        height: 200px;
    }
}
</style>

<script>
function addToCart(productId) {
    const quantity = 1; // Default quantity
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    fetch('/gstore/add-to-cart', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Product added to cart');
            // Update cart count in header (if implemented)
        } else {
            alert(data.message || 'Error adding to cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding to cart');
    });
}
</script>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
