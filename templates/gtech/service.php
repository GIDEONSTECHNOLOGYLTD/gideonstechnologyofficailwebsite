<?php
/**
 * GTech Service Detail Page
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($service['name'] ?? 'Service') ?> | <?= $appName ?? 'Gideons Technology' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <!-- Include header -->
    <?php include_once dirname(__DIR__) . '/partials/header.php'; ?>
    
    <!-- Service Hero -->
    <div class="bg-light py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1><?= htmlspecialchars($service['name'] ?? 'Service Details') ?></h1>
                    <p class="lead"><?= htmlspecialchars($service['description'] ?? 'Professional technology services tailored to your needs') ?></p>
                    <a href="/gtech/consultation" class="btn btn-primary btn-lg mt-3">Request a Consultation</a>
                </div>
                <div class="col-lg-6">
                    <img src="https://via.placeholder.com/600x400?text=<?= urlencode($service['name'] ?? 'Service') ?>" class="img-fluid rounded shadow" alt="<?= htmlspecialchars($service['name'] ?? 'Service') ?>">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Service Details -->
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <h2>About This Service</h2>
                <p class="mb-4"><?= htmlspecialchars($service['long_description'] ?? 'Our professional team provides high-quality technology solutions tailored to your specific needs. With years of experience in the industry, we deliver reliable and efficient services that help you achieve your goals.') ?></p>
                
                <h3 class="mt-5">Key Features</h3>
                <div class="row mt-4">
                    <div class="col-md-6 mb-4">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <h5>Professional Expertise</h5>
                                <p>Our team consists of certified professionals with extensive experience</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <h5>Customized Solutions</h5>
                                <p>Tailored approaches to meet your specific requirements</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <h5>Ongoing Support</h5>
                                <p>Continuous assistance and maintenance after project completion</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <h5>Competitive Pricing</h5>
                                <p>Cost-effective solutions without compromising on quality</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h3 class="mt-5">Our Process</h3>
                <div class="row mt-4">
                    <div class="col-md-3 text-center mb-4">
                        <div class="bg-light rounded-circle d-inline-flex justify-content-center align-items-center" style="width: 80px; height: 80px;">
                            <i class="bi bi-chat-dots fs-2 text-primary"></i>
                        </div>
                        <h5 class="mt-3">Consultation</h5>
                        <p>We discuss your needs and requirements</p>
                    </div>
                    <div class="col-md-3 text-center mb-4">
                        <div class="bg-light rounded-circle d-inline-flex justify-content-center align-items-center" style="width: 80px; height: 80px;">
                            <i class="bi bi-clipboard-check fs-2 text-primary"></i>
                        </div>
                        <h5 class="mt-3">Planning</h5>
                        <p>We create a detailed project plan</p>
                    </div>
                    <div class="col-md-3 text-center mb-4">
                        <div class="bg-light rounded-circle d-inline-flex justify-content-center align-items-center" style="width: 80px; height: 80px;">
                            <i class="bi bi-gear fs-2 text-primary"></i>
                        </div>
                        <h5 class="mt-3">Implementation</h5>
                        <p>We execute the plan with precision</p>
                    </div>
                    <div class="col-md-3 text-center mb-4">
                        <div class="bg-light rounded-circle d-inline-flex justify-content-center align-items-center" style="width: 80px; height: 80px;">
                            <i class="bi bi-headset fs-2 text-primary"></i>
                        </div>
                        <h5 class="mt-3">Support</h5>
                        <p>We provide ongoing assistance</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Service Details</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <i class="bi bi-tag me-2"></i>
                                <strong>Category:</strong> <?= htmlspecialchars(getCategoryName($service['category_id'] ?? 1)) ?>
                            </li>
                            <li class="mb-3">
                                <i class="bi bi-clock me-2"></i>
                                <strong>Duration:</strong> <?= htmlspecialchars($service['duration'] ?? 'Varies based on project scope') ?>
                            </li>
                            <li class="mb-3">
                                <i class="bi bi-currency-dollar me-2"></i>
                                <strong>Starting Price:</strong> $<?= htmlspecialchars($service['price'] ?? 'Custom quote') ?>
                            </li>
                            <li class="mb-3">
                                <i class="bi bi-people me-2"></i>
                                <strong>Target Audience:</strong> <?= htmlspecialchars($service['audience'] ?? 'Businesses and individuals') ?>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Request Information</h4>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="mb-3">
                                <label for="name" class="form-label">Your Name</label>
                                <input type="text" class="form-control" id="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone">
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Send Request</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Services -->
    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-5">Related Services</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-laptop fs-1 text-primary mb-3"></i>
                            <h5>Web Development</h5>
                            <p>Custom websites and web applications tailored to your needs</p>
                            <a href="/gtech/service/1" class="btn btn-outline-primary mt-3">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-phone fs-1 text-primary mb-3"></i>
                            <h5>Mobile App Development</h5>
                            <p>Native and cross-platform mobile applications</p>
                            <a href="/gtech/service/2" class="btn btn-outline-primary mt-3">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-shield-check fs-1 text-primary mb-3"></i>
                            <h5>Cybersecurity</h5>
                            <p>Protect your business with our security solutions</p>
                            <a href="/gtech/service/3" class="btn btn-outline-primary mt-3">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Include footer -->
    <?php include_once dirname(__DIR__) . '/partials/footer.php'; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php
    // Helper function to get category name
    function getCategoryName($categoryId) {
        $categories = [
            1 => 'Business Services',
            2 => 'Individual Services',
            3 => 'Web Development',
            4 => 'Mobile Development',
            5 => 'IT Support'
        ];
        
        return $categories[$categoryId] ?? 'General';
    }
    ?>
</body>
</html>
