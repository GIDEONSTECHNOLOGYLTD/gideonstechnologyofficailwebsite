<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Repair Services - Gideon\'s Technology' ?></title>
    <meta name="description" content="<?= $description ?? 'Professional repair services for your computers, phones, and other devices.' ?>">
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../../../templates/partials/header.php'; ?>

    <!-- Main Content -->
    <main class="container my-5">
        <div class="row">
            <div class="col-lg-8">
                <h1 class="mb-4"><?= $pageHeader ?? 'Device Repair Services' ?></h1>
                <p class="lead">Trust our experts to repair your valuable devices quickly and professionally.</p>
                
                <div class="my-5">
                    <h2>Our Repair Services</h2>
                    <p>At Gideon's Technology, we specialize in fixing a wide range of electronic devices. Our technicians are certified and experienced in diagnosing and repairing:</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h3 class="card-title h5">Computer Repair</h3>
                                    <p class="card-text">We fix desktops, laptops, and workstations of all brands.</p>
                                    <a href="/gtech/services/repair/computer" class="btn btn-primary">Learn More</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h3 class="card-title h5">Phone Repair</h3>
                                    <p class="card-text">Screen replacement, battery issues, water damage, and more.</p>
                                    <a href="/gtech/services/repair/phone" class="btn btn-primary">Learn More</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h3 class="card-title h5">Tablet Repair</h3>
                                    <p class="card-text">iPad, Samsung, and other tablet repairs with genuine parts.</p>
                                    <a href="/gtech/services/repair/tablet" class="btn btn-primary">Learn More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="my-5">
                    <h2>Why Choose Our Repair Services?</h2>
                    <ul class="list-group list-group-flush mt-3">
                        <li class="list-group-item">
                            <strong>Fast Turnaround</strong> - Many repairs completed same-day
                        </li>
                        <li class="list-group-item">
                            <strong>Warranty</strong> - 90-day warranty on all repairs
                        </li>
                        <li class="list-group-item">
                            <strong>Genuine Parts</strong> - We use high-quality replacement parts
                        </li>
                        <li class="list-group-item">
                            <strong>No Fix, No Fee</strong> - If we can't fix it, you don't pay
                        </li>
                        <li class="list-group-item">
                            <strong>Free Diagnostics</strong> - Detailed assessment before repair
                        </li>
                    </ul>
                </div>
                
                <div class="my-5">
                    <h2>Our Repair Process</h2>
                    <div class="process-steps">
                        <div class="step">
                            <div class="step-number">1</div>
                            <h3>Diagnostics</h3>
                            <p>We thoroughly examine your device to identify all issues.</p>
                        </div>
                        
                        <div class="step">
                            <div class="step-number">2</div>
                            <h3>Quotation</h3>
                            <p>We provide a detailed quote with no hidden fees.</p>
                        </div>
                        
                        <div class="step">
                            <div class="step-number">3</div>
                            <h3>Repair</h3>
                            <p>Our technicians carefully repair your device.</p>
                        </div>
                        
                        <div class="step">
                            <div class="step-number">4</div>
                            <h3>Testing</h3>
                            <p>We thoroughly test your device to ensure everything works perfectly.</p>
                        </div>
                        
                        <div class="step">
                            <div class="step-number">5</div>
                            <h3>Return</h3>
                            <p>Your device is returned in perfect working condition.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h3 class="card-title">Request Repair Service</h3>
                        <p>Need your device fixed? Fill out our request form and we'll get back to you promptly.</p>
                        <a href="/gtech/services/request" class="btn btn-primary btn-block">Request Repair</a>
                    </div>
                </div>
                
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h3 class="card-title">Contact Us</h3>
                        <p><strong>Phone:</strong> (555) 123-4567</p>
                        <p><strong>Email:</strong> repair@gideons-technology.com</p>
                        <p><strong>Address:</strong> 123 Tech Street, Innovation City</p>
                        <p><strong>Hours:</strong> Mon-Fri: 9am-6pm, Sat: 10am-4pm</p>
                    </div>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title">Customer Testimonials</h3>
                        <div class="testimonial">
                            <p class="mb-2">"They fixed my laptop in just 2 hours! Amazing service and very reasonable prices."</p>
                            <p class="text-muted">- John D.</p>
                        </div>
                        <div class="testimonial">
                            <p class="mb-2">"I thought my phone was beyond repair after it got wet, but they managed to save it and all my data!"</p>
                            <p class="text-muted">- Sarah M.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/../../../templates/partials/footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/main.js"></script>
</body>
</html>