<?php
/**
 * GTech Services Page
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technology Services | <?= $appName ?? 'Gideons Technology' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <style>
        .service-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .service-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <!-- Include header -->
    <?php include_once dirname(__DIR__) . '/partials/header.php'; ?>
    
    <!-- Main Content -->
    <div class="container py-5">
        <header class="text-center mb-5">
            <h1>Our Technology Services</h1>
            <p class="lead">Professional technology solutions for businesses and individuals</p>
        </header>
        
        <!-- Services Grid -->
        <div class="row g-4">
            <?php foreach ($services as $service): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card service-card">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-<?= getServiceIcon($service['name']) ?> service-icon"></i>
                        <h4 class="card-title"><?= htmlspecialchars($service['name']) ?></h4>
                        <p class="card-text"><?= htmlspecialchars($service['description']) ?></p>
                        <a href="/gtech/service/<?= $service['id'] ?>" class="btn btn-outline-primary mt-3">Learn More</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Why Choose Us -->
    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-5">Why Choose Our Services</h2>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-award fs-1 text-primary mb-3"></i>
                            <h5>Expertise</h5>
                            <p>Our team consists of certified professionals with years of experience</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-clock-history fs-1 text-primary mb-3"></i>
                            <h5>Reliability</h5>
                            <p>We deliver on time, every time, with consistent quality</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-cash-coin fs-1 text-primary mb-3"></i>
                            <h5>Affordability</h5>
                            <p>Competitive pricing without compromising on quality</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-headset fs-1 text-primary mb-3"></i>
                            <h5>Support</h5>
                            <p>Dedicated customer support for all your questions and concerns</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Call to Action -->
    <section class="py-5">
        <div class="container text-center">
            <h2>Ready to Get Started?</h2>
            <p class="lead mb-4">Contact us today for a free consultation about your technology needs</p>
            <a href="/gtech/consultation" class="btn btn-lg btn-primary">Request a Consultation</a>
        </div>
    </section>
    
    <!-- Include footer -->
    <?php include_once dirname(__DIR__) . '/partials/footer.php'; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Helper function for service icons -->
    <?php
    function getServiceIcon($serviceName) {
        $icons = [
            'Web Development' => 'globe',
            'Mobile App Development' => 'phone',
            'E-commerce Solutions' => 'cart',
            'Tech Repair' => 'tools',
            'IT Consulting' => 'briefcase',
            'Cloud Services' => 'cloud',
            'Cybersecurity' => 'shield-lock',
            'Data Recovery' => 'database',
            'Network Setup' => 'diagram-3',
            'Software Development' => 'code-square'
        ];
        
        return $icons[$serviceName] ?? 'gear';
    }
    ?>
</body>
</html>
