<?php
/**
 * GStore Home Page Template
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Store - <?= htmlspecialchars($appName ?? 'Gideon\'s Technology') ?></title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .featured-product {
            margin-bottom: 30px;
            border: 1px solid #eee;
            padding: 15px;
            border-radius: 8px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .featured-product:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .featured-product img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .product-price {
            font-size: 1.25rem;
            font-weight: bold;
            color: #3c66a0;
        }
        .sale-price {
            color: #dc3545;
        }
        .original-price {
            text-decoration: line-through;
            color: #6c757d;
            font-size: 0.9rem;
        }
        .hero-section {
            background-color: #f8f9fa;
            padding: 60px 0;
            margin-bottom: 40px;
        }
        .section-heading {
            margin-bottom: 30px;
            position: relative;
            padding-bottom: 15px;
        }
        .section-heading:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: #3c66a0;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../partials/header.php'; ?>

    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1>Premium Website Templates</h1>
                    <p class="lead">Professionally designed templates for your business, blog, portfolio and more.</p>
                    <a href="/gstore/products" class="btn btn-primary btn-lg">Browse All Templates</a>
                </div>
                <div class="col-lg-6">
                    <img src="/assets/images/templates-showcase.jpg" alt="Templates Showcase" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <h2 class="section-heading">Featured Templates</h2>
        
        <div class="row">
            <?php if (!empty($featuredProducts)): ?>
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="col-md-4">
                        <div class="featured-product">
                            <img src="/assets/images/products/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" onerror="this.src='/assets/images/placeholder.jpg'">
                            <h3><?= htmlspecialchars($product['name']) ?></h3>
                            <p><?= htmlspecialchars($product['description']) ?></p>
                            <div class="mb-3">
                                <?php if ($product['on_sale']): ?>
                                    <span class="product-price sale-price">$<?= number_format($product['sale_price'], 2) ?></span>
                                    <span class="original-price">$<?= number_format($product['price'], 2) ?></span>
                                <?php else: ?>
                                    <span class="product-price">$<?= number_format($product['price'], 2) ?></span>
                                <?php endif; ?>
                            </div>
                            <a href="/gstore/product/<?= $product['id'] ?>" class="btn btn-outline-primary">View Details</a>
                            <button class="btn btn-primary add-to-cart" data-product-id="<?= $product['id'] ?>">Add to Cart</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">No featured products available at the moment.</div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="row mt-5">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <h3>Premium Quality</h3>
                        <p>All our templates are professionally designed with attention to detail and best practices.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <h3>Responsive Design</h3>
                        <p>Our templates are fully responsive and look great on all devices, from mobile to desktop.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <h3>Support Included</h3>
                        <p>Get 6 months of customer support with every purchase to help with setup and customization.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4 mb-5">
            <a href="/gstore/products" class="btn btn-lg btn-primary">View All Templates</a>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/footer.php'; ?>
    
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add to cart functionality
            const addToCartButtons = document.querySelectorAll('.add-to-cart');
            
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    
                    fetch('/gstore/api/cart/add', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: 1
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Product added to cart!');
                            // Update cart count in navbar if exists
                            const cartCountElement = document.querySelector('.cart-count');
                            if (cartCountElement) {
                                cartCountElement.textContent = data.cart_count;
                            }
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while adding to cart.');
                    });
                });
            });
        });
    </script>
</body>
</html>