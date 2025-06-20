<!-- Hero Section -->
<section class="hero bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold">Innovative Technology Solutions</h1>
                <p class="lead">We build cutting-edge web applications, mobile solutions, and provide expert technical consulting to help your business grow.</p>
                <div class="d-flex mt-4">
                    <a href="/services" class="btn btn-light btn-lg me-2">Our Services</a>
                    <a href="/contact" class="btn btn-outline-light btn-lg">Contact Us</a>
                </div>
            </div>
            <div class="col-md-6 text-center">
                <img src="/assets/images/hero-image.svg" alt="Technology Solutions" class="img-fluid">
            </div>
        </div>
    </div>
</section>

<!-- Featured Services -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Our Featured Services</h2>
            <p class="text-muted">Discover how we can help your business succeed</p>
        </div>
        
        <div class="row">
            <?php if (empty($services)): ?>
                <div class="col-12 text-center">
                    <p>No featured services available at the moment.</p>
                </div>
            <?php else: ?>
                <?php foreach ($services as $service): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <?php if (!empty($service['image'])): ?>
                                <img src="/assets/images/services/<?= $this->escape($service['image']) ?>" class="card-img-top" alt="<?= $this->escape($service['name']) ?>">
                            <?php else: ?>
                                <img src="/assets/images/placeholder.jpg" class="card-img-top" alt="Service Placeholder">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= $this->escape($service['name']) ?></h5>
                                <p class="card-text"><?= $this->escape($service['short_description']) ?></p>
                            </div>
                            <div class="card-footer bg-white border-0">
                                <a href="/services/<?= $this->escape($service['slug']) ?>" class="btn btn-outline-primary">Learn More</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="/services" class="btn btn-primary">View All Services</a>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <img src="/assets/images/about-image.jpg" alt="About Us" class="img-fluid rounded shadow">
            </div>
            <div class="col-md-6">
                <h2 class="fw-bold">About Gideon's Technology</h2>
                <p class="lead">We're passionate about technology and dedicated to delivering exceptional solutions.</p>
                <p>Gideon's Technology has been at the forefront of technological innovation since 2015. We combine technical expertise with industry knowledge to create solutions that drive business growth and efficiency.</p>
                <a href="/about" class="btn btn-outline-primary mt-3">Learn More About Us</a>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">What Our Clients Say</h2>
            <p class="text-muted">Don't take our word for it - hear what our clients have to say</p>
        </div>
        
        <div class="row">
            <?php if (empty($testimonials)): ?>
                <div class="col-12 text-center">
                    <p>No testimonials available at the moment.</p>
                </div>
            <?php else: ?>
                <?php foreach ($testimonials as $testimonial): ?>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <div>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?php if ($i <= $testimonial['rating']): ?>
                                                <i class="fas fa-star text-warning"></i>
                                            <?php else: ?>
                                                <i class="far fa-star text-warning"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                    <i class="fas fa-quote-right text-primary fs-1 opacity-25"></i>
                                </div>
                                <p class="card-text"><?= $this->escape($testimonial['content']) ?></p>
                            </div>
                            <div class="card-footer bg-white">
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($testimonial['client_image'])): ?>
                                        <img src="/assets/images/testimonials/<?= $this->escape($testimonial['client_image']) ?>" class="rounded-circle me-3" width="50" height="50" alt="<?= $this->escape($testimonial['client_name']) ?>">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                            <?= strtoupper(substr($testimonial['client_name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h6 class="mb-0 fw-bold"><?= $this->escape($testimonial['client_name']) ?></h6>
                                        <?php if (!empty($testimonial['client_position'])): ?>
                                            <small class="text-muted"><?= $this->escape($testimonial['client_position']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 bg-primary text-white text-center">
    <div class="container">
        <h2 class="fw-bold mb-4">Ready to transform your business?</h2>
        <p class="lead mb-4">Let's work together to create innovative solutions that drive your success.</p>
        <a href="/contact" class="btn btn-light btn-lg">Get Started Today</a>
    </div>
</section>