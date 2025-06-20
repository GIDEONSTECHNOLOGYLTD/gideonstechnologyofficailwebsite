<?php
/**
 * Tech Repair Service Page Template
 * 
 * Displays information about our tech repair services
 */

// Set page variables
$pageTitle = 'Professional Tech Repair Services | Gideon\'s Technology';
$pageDescription = 'Expert repairs for computers, laptops, and mobile devices';
$bodyClass = 'repair-service-page';

// Custom styles for this page
$customStyles = '
.service-feature {
    margin-bottom: 20px;
}
.process-step {
    text-align: center;
    margin-bottom: 30px;
}
';

// Include header - use path relative to current file location
include __DIR__ . '/partials/header.php';
?>

<!-- Page Hero -->
<section class="page-hero bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="page-title">Tech Repair Service</h1>
                <p class="lead">Our Repair Services Include:</p>
                <div class="row mt-5">
                    <div class="col-md-6 col-lg-3">
                        <div class="service-feature">
                            <i class="fas fa-laptop fa-2x mb-3"></i>
                            <p>Computer & Laptop Repairs</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="service-feature">
                            <i class="fas fa-mobile-alt fa-2x mb-3"></i>
                            <p>Smartphone Screen Replacement</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="service-feature">
                            <i class="fas fa-bug fa-2x mb-3"></i>
                            <p>Software Troubleshooting</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="service-feature">
                            <i class="fas fa-shield-alt fa-2x mb-3"></i>
                            <p>Virus & Malware Removal</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-lg-3">
                        <div class="service-feature">
                            <i class="fas fa-database fa-2x mb-3"></i>
                            <p>Data Recovery Services</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="service-feature">
                            <i class="fas fa-microchip fa-2x mb-3"></i>
                            <p>Hardware Upgrades</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="service-feature">
                            <i class="fas fa-network-wired fa-2x mb-3"></i>
                            <p>Network Setup & Troubleshooting</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="service-feature">
                            <i class="fas fa-cogs fa-2x mb-3"></i>
                            <p>Operating System Installation</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section class="pricing-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center mb-5">
                <h2 class="section-title">Repair Service Packages</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="pricing-card">
                    <div class="pricing-header">
                        <h3>Basic Diagnostics</h3>
                        <div class="price">$49</div>
                    </div>
                    <div class="pricing-body">
                        <ul class="feature-list">
                            <li>Hardware Diagnostics</li>
                            <li>Software Troubleshooting</li>
                            <li>Basic Virus Removal</li>
                        </ul>
                    </div>
                    <div class="pricing-footer">
                        <a href="/contact" class="btn btn-outline-primary">Request Service</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="pricing-card highlighted">
                    <div class="pricing-header">
                        <h3>Standard Repair</h3>
                        <div class="price">$99</div>
                    </div>
                    <div class="pricing-body">
                        <ul class="feature-list">
                            <li>All Basic Features</li>
                            <li>Hardware Repairs</li>
                            <li>Advanced Virus Removal</li>
                            <li>System Optimization</li>
                        </ul>
                    </div>
                    <div class="pricing-footer">
                        <a href="/contact" class="btn btn-primary">Request Service</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="pricing-card">
                    <div class="pricing-header">
                        <h3>Premium Service</h3>
                        <div class="price">$199</div>
                    </div>
                    <div class="pricing-body">
                        <ul class="feature-list">
                            <li>All Standard Features</li>
                            <li>Data Recovery</li>
                            <li>Hardware Upgrades</li>
                            <li>1-Year Support Plan</li>
                        </ul>
                    </div>
                    <div class="pricing-footer">
                        <a href="/contact" class="btn btn-outline-primary">Request Service</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Process Section -->
<section class="process-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center mb-5">
                <h2 class="section-title">Our Repair Process</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="process-step">
                    <div class="step-number">1</div>
                    <h4>Diagnostics</h4>
                    <p>We perform a thorough assessment of your device</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="process-step">
                    <div class="step-number">2</div>
                    <h4>Repair Quote</h4>
                    <p>We provide a detailed repair quote</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="process-step">
                    <div class="step-number">3</div>
                    <h4>Repair Service</h4>
                    <p>Our technicians perform the necessary repairs</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="process-step">
                    <div class="step-number">4</div>
                    <h4>Quality Check</h4>
                    <p>We verify that all issues are resolved</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section py-5 text-center bg-primary text-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2>Ready to get your device fixed?</h2>
                <p class="lead mb-4">Our expert technicians are ready to help solve your tech problems.</p>
                <a href="/contact" class="btn btn-light btn-lg">Request Repair Service</a>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-5">
                <h3>Contact Us</h3>
                <ul class="contact-info-list">
                    <li>
                        <i class="fas fa-phone"></i>
                        <span>(555) 123-4567</span>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <span>support@gideons-tech.com</span>
                    </li>
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <span>123 Tech Avenue, Silicon Valley, CA</span>
                    </li>
                </ul>
                
                <h4 class="mt-4">Business Hours</h4>
                <ul class="hours-list">
                    <li>
                        <span>Monday - Friday:</span>
                        <span>9:00 AM - 6:00 PM</span>
                    </li>
                    <li>
                        <span>Saturday:</span>
                        <span>10:00 AM - 4:00 PM</span>
                    </li>
                    <li>
                        <span>Sunday:</span>
                        <span>Closed</span>
                    </li>
                </ul>
            </div>
            <div class="col-lg-7">
                <h3>Why Choose Us?</h3>
                <div class="row features-grid">
                    <div class="col-md-4">
                        <div class="feature-box text-center">
                            <i class="fas fa-certificate fa-3x mb-3 text-primary"></i>
                            <h5>Certified Technicians</h5>
                            <p>Our repair specialists are fully certified and trained.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-box text-center">
                            <i class="fas fa-shield-alt fa-3x mb-3 text-primary"></i>
                            <h5>90-Day Warranty</h5>
                            <p>All our repair services come with a 90-day warranty.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-box text-center">
                            <i class="fas fa-clock fa-3x mb-3 text-primary"></i>
                            <h5>Quick Turnaround</h5>
                            <p>Most repairs are completed within 24-48 hours.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer - use path relative to current file location
include __DIR__ . '/partials/footer.php';
?>