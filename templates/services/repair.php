<!DOCTYPE html>
<html>
<head>
    <title><?= $appName ?> - Hardware & Repair Services</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Hardware & Repair Services</h1>
        <p class="lead">Professional computer repair and hardware services by Gideons Technology.</p>
        
        <div class="row mt-4">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Our Repair Services</h3>
                    </div>
                    <div class="card-body">
                        <h4>Computer Repair</h4>
                        <p>Our trained technicians can diagnose and fix a wide range of computer issues, including:</p>
                        <ul>
                            <li>Hardware failures and replacements</li>
                            <li>Operating system problems</li>
                            <li>Virus and malware removal</li>
                            <li>Data recovery</li>
                            <li>Performance optimization</li>
                            <li>Software installation and updates</li>
                        </ul>
                        
                        <h4 class="mt-4">Laptop Repair</h4>
                        <p>We specialize in laptop repairs for all major brands:</p>
                        <ul>
                            <li>Screen replacements</li>
                            <li>Keyboard and touchpad issues</li>
                            <li>Battery replacements</li>
                            <li>Charging port repairs</li>
                            <li>Liquid damage recovery</li>
                        </ul>
                        
                        <h4 class="mt-4">Mobile Device Repair</h4>
                        <p>Our skilled technicians can repair your smartphones and tablets:</p>
                        <ul>
                            <li>Screen replacements</li>
                            <li>Battery replacements</li>
                            <li>Software troubleshooting</li>
                            <li>Water damage recovery</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Hardware Sales</h3>
                    </div>
                    <div class="card-body">
                        <p>We offer quality hardware components and devices at competitive prices:</p>
                        <div class="row">
                            <div class="col-md-6">
                                <ul>
                                    <li>Desktop computers</li>
                                    <li>Laptops</li>
                                    <li>Tablets</li>
                                    <li>Printers</li>
                                    <li>Monitors</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul>
                                    <li>Hard drives and SSDs</li>
                                    <li>Memory modules</li>
                                    <li>Processors</li>
                                    <li>Graphics cards</li>
                                    <li>Peripherals and accessories</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Request a Repair</h3>
                    </div>
                    <div class="card-body">
                        <form action="/repair-request" method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="device_type" class="form-label">Device Type</label>
                                <select class="form-select" id="device_type" name="device_type" required>
                                    <option value="">Select a device</option>
                                    <option value="desktop">Desktop Computer</option>
                                    <option value="laptop">Laptop</option>
                                    <option value="smartphone">Smartphone</option>
                                    <option value="tablet">Tablet</option>
                                    <option value="printer">Printer</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="issue" class="form-label">Describe the Issue</label>
                                <textarea class="form-control" id="issue" name="issue" rows="4" required></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Submit Request</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Contact Us</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Phone:</strong> (555) 123-4567</p>
                        <p><strong>Email:</strong> repair@gideonstech.com</p>
                        <p><strong>Hours:</strong> Monday-Friday, 9am-6pm</p>
                        <p><strong>Address:</strong> 123 Tech Avenue, City, State 12345</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Why Choose Us?</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li>✓ 90-day warranty on all repairs</li>
                            <li>✓ Certified technicians</li>
                            <li>✓ Fast turnaround times</li>
                            <li>✓ Competitive pricing</li>
                            <li>✓ Free diagnostics</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="/gtech" class="btn btn-secondary">Back to Gtech Platform</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>