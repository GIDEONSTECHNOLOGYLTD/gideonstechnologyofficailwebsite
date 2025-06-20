<?php
require_once __DIR__ . '/../app/bootstrap.php';

$page_title = "About Us - " . SITE_NAME;
$meta_description = "Learn more about our company, our mission, and our commitment to delivering high-quality technology solutions.";
$meta_keywords = "about us, technology company, IT services, web development, fintech solutions";

require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4">About Us</h1>
                <p class="lead">We are passionate about delivering innovative technology solutions that drive business growth.</p>
            </div>
            <div class="col-lg-6">
                <img src="assets/img/about-hero.png" alt="About Us" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Our Story -->
<section class="story py-5">
    <div class="container">
        <h2 class="text-center mb-5">Our Story</h2>
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <p class="lead text-center">Founded with a vision to transform businesses through technology, we've been helping companies achieve their digital goals since our inception.</p>
                <p>At <?php echo SITE_NAME; ?>, we believe in:</p>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-primary me-2"></i>
                        Innovation and cutting-edge solutions
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-primary me-2"></i>
                        Customer-centric approach
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-primary me-2"></i>
                        Quality and reliability
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-primary me-2"></i>
                        Continuous learning and improvement
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="team bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-5">Our Team</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <img src="assets/img/team/ceo.jpg" class="card-img-top" alt="CEO">
                    <div class="card-body text-center">
                        <h5 class="card-title">John Doe</h5>
                        <p class="card-text text-muted">CEO & Founder</p>
                        <p class="card-text">With over 15 years of experience in technology and business leadership.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <img src="assets/img/team/cto.jpg" class="card-img-top" alt="CTO">
                    <div class="card-body text-center">
                        <h5 class="card-title">Jane Smith</h5>
                        <p class="card-text text-muted">CTO</p>
                        <p class="card-text">Expert in web development, cloud architecture, and emerging technologies.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <img src="assets/img/team/lead-dev.jpg" class="card-img-top" alt="Lead Developer">
                    <div class="card-body text-center">
                        <h5 class="card-title">Mike Johnson</h5>
                        <p class="card-text text-muted">Lead Developer</p>
                        <p class="card-text">Full-stack developer with expertise in modern web technologies.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact CTA -->
<section class="cta bg-primary text-white py-5">
    <div class="container text-center">
        <h2>Ready to Work with Us?</h2>
        <p class="lead">Let's discuss how we can help your business grow with our technology solutions.</p>
        <a href="contact.php" class="btn btn-light btn-lg">Contact Us Today</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
