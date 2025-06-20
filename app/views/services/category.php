<?php include_once APP_PATH . '/views/layouts/header.php'; ?>

<div class="container">
    <div class="category-header">
        <h1><?php echo htmlspecialchars($category['name']); ?></h1>
        <p><?php echo htmlspecialchars($category['description']); ?></p>
    </div>

    <div class="services-grid">
        <?php foreach ($services as $service): ?>
        <div class="service-card">
            <div class="service-image">
                <img src="<?php echo htmlspecialchars($service['image_url']); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>">
            </div>
            <div class="service-content">
                <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                <p class="service-description"><?php echo htmlspecialchars($service['description']); ?></p>
                <div class="service-meta">
                    <span class="price">$<?php echo htmlspecialchars($service['price']); ?></span>
                    <span class="category"><?php echo htmlspecialchars($service['category_name']); ?></span>
                </div>
                <div class="service-actions">
                    <a href="/services/<?php echo htmlspecialchars($service['id']); ?>" class="btn btn-primary">Learn More</a>
                    <a href="/order/<?php echo htmlspecialchars($service['id']); ?>" class="btn btn-secondary">Order Now</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.category-header {
    text-align: center;
    padding: 40px 0;
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
    margin-bottom: 40px;
}

.category-header h1 {
    font-size: 2.5em;
    margin-bottom: 15px;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    padding: 20px;
}

.service-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
}

.service-card:hover {
    transform: translateY(-5px);
}

.service-image {
    width: 100%;
    height: 200px;
    overflow: hidden;
}

.service-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.service-content {
    padding: 20px;
}

.service-content h3 {
    margin: 0 0 15px 0;
    color: #2c3e50;
}

.service-description {
    color: #666;
    margin-bottom: 20px;
}

.service-meta {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
    color: #666;
}

.service-meta .price {
    color: #e74c3c;
    font-weight: bold;
    font-size: 1.2em;
}

.service-actions {
    display: flex;
    gap: 10px;
}

.btn {
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    color: white;
    transition: background-color 0.3s;
}

.btn-primary {
    background: #3498db;
}

.btn-primary:hover {
    background: #2980b9;
}

.btn-secondary {
    background: #2ecc71;
}

.btn-secondary:hover {
    background: #27ae60;
}

@media (max-width: 768px) {
    .services-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include_once APP_PATH . '/views/layouts/footer.php'; ?>
