
<div class="services-page">
    <div class="service-details">
        <div class="service-header">
            <h1><?php echo $service['name']; ?></h1>
            <?php if (!empty($service['category'])): ?>
                <div class="service-category">
                    <a href="/services/<?php echo $service['category']['slug']; ?>"><?php echo $service['category']['name']; ?></a>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($service['image'])): ?>
            <div class="service-hero">
                <img src="<?php echo $service['image']; ?>" alt="<?php echo $service['name']; ?>">
            </div>
        <?php endif; ?>

        <div class="service-content">
            <div class="service-description">
                <p><?php echo $service['description']; ?></p>
            </div>

            <?php if (!empty($service['features'])): ?>
                <div class="service-features">
                    <h2>Key Features</h2>
                    <ul>
                        <?php foreach ($service['features'] as $feature): ?>
                            <li><?php echo $feature; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($service['benefits'])): ?>
                <div class="service-benefits">
                    <h2>Benefits</h2>
                    <ul>
                        <?php foreach ($service['benefits'] as $benefit): ?>
                            <li><?php echo $benefit; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($service['pricing'])): ?>
                <div class="service-pricing">
                    <h2>Pricing</h2>
                    <div class="pricing-details">
                        <?php echo $service['pricing']; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="service-cta">
                <a href="/contact?service=<?php echo urlencode($service['name']); ?>" class="cta-button">Request This Service</a>
            </div>
        </div>
    </div>
</div>

