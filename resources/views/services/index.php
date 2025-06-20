<div class="services-page">
    <h1><?php echo $title; ?></h1>

    <!-- Featured Services -->
    <section class="featured-services">
        <?php foreach ($featuredServices as $service): ?>
            <div class="service-item">
                <div class="service-card">
                    <div class="service-image">
                        <img src="<?php echo $service['image']; ?>" alt="<?php echo $service['name']; ?>">
                    </div>
                    <div class="service-content">
                        <h3><?php echo $service['name']; ?></h3>
                        <p><?php echo $service['description']; ?></p>
                        <a href="/services/<?php echo strtolower(str_replace(' ', '-', $service['name'])); ?>" class="learn-more">Learn More</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </section>

    <!-- Service Categories -->
    <section class="service-categories">
        <h2>Our Service Categories</h2>
        <div class="categories-grid">
            <?php foreach ($categories as $slug => $name): ?>
                <div class="category-item">
                    <div class="category-card">
                        <h3><?php echo $name; ?></h3>
                        <a href="/services/<?php echo $slug; ?>" class="view-services">View Services</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>
