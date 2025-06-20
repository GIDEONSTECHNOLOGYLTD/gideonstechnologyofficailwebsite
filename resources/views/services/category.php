
<div class="services-page">
    <h1><?php echo $title; ?></h1>

    <?php if ($category): ?>
        <div class="category-description">
            <p><?php echo $category['description']; ?></p>
        </div>
    <?php endif; ?>

    <section class="services-list">
        <?php if ($services): ?>
            <?php foreach ($services as $service): ?>
                <div class="service-item">
                    <div class="service-card">
                        <div class="service-image">
                            <img src="<?php echo $service['image']; ?>" alt="<?php echo $service['name']; ?>">
                        </div>
                        <div class="service-content">
                            <h3><?php echo $service['name']; ?></h3>
                            <p><?php echo $service['description']; ?></p>
                            <div class="service-features">
                                <?php if (!empty($service['features'])): ?>
                                    <ul>
                                        <?php foreach ($service['features'] as $feature): ?>
                                            <li><?php echo $feature; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                            <a href="/contact?service=<?php echo urlencode($service['name']); ?>" class="cta-button">Request Service</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-services">
                <p>No services available in this category at the moment.</p>
            </div>
        <?php endif; ?>
    </section>
</div>

