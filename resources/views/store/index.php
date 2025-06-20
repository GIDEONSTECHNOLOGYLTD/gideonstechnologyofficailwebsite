<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-4">
    <!-- Store Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-primary text-white p-4 rounded">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1>Welcome to Gideon's Technology Store</h1>
                        <p class="lead mb-0">Discover the latest in technology and electronics</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <a href="/gstore/cart" class="btn btn-light">
                            <i class="fas fa-shopping-cart me-2"></i> View Cart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories and Products -->
    <div class="row">
        <!-- Categories Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Categories</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php if (isset($categories) && !empty($categories)): ?>
                        <?php foreach ($categories as $category): ?>
                            <a href="/gstore/category/<?= $category['id'] ?>" class="list-group-item list-group-item-action">
                                <?= htmlspecialchars($category['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="list-group-item">No categories found</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Price Filter -->
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Filter by Price</h5>
                </div>
                <div class="card-body">
                    <form action="/gstore/filter" method="get">
                        <div class="mb-3">
                            <label for="min-price" class="form-label">Min Price</label>
                            <input type="number" class="form-control" id="min-price" name="min_price" min="0" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label for="max-price" class="form-label">Max Price</label>
                            <input type="number" class="form-control" id="max-price" name="max_price" min="0" step="0.01">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-md-9">
            <div class="row">
                <?php if (isset($products) && !empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <?php if (isset($product['image']) && !empty($product['image'])): ?>
                                    <img src="<?= htmlspecialchars($product['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                                <?php else: ?>
                                    <div class="text-center p-4 bg-light">
                                        <i class="fas fa-image fa-4x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                    <p class="card-text text-muted"><?= htmlspecialchars(substr($product['description'], 0, 100)) ?>...</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="h5 mb-0 text-primary">$<?= number_format($product['price'], 2) ?></span>
                                        <a href="/gstore/product/<?= $product['id'] ?>" class="btn btn-outline-primary btn-sm">View Details</a>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <form action="/gstore/cart/add/<?= $product['id'] ?>" method="post">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-cart-plus me-2"></i> Add to Cart
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> No products found.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
