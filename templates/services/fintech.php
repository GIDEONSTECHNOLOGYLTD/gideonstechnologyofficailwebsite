<!DOCTYPE html>
<html>
<head>
    <title><?= $appName ?> - Fintech Solutions</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Fintech Solutions</h1>
        <p class="lead">Innovative financial technology solutions by Gideons Technology.</p>
        
        <div class="row mt-4">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Our Fintech Services</h3>
                    </div>
                    <div class="card-body">
                        <h4>Payment Processing</h4>
                        <p>Secure, reliable payment solutions for businesses of all sizes:</p>
                        <ul>
                            <li>Credit and debit card processing</li>
                            <li>Mobile payment integration</li>
                            <li>Cryptocurrency payment options</li>
                            <li>Recurring billing solutions</li>
                            <li>Multi-currency support</li>
                        </ul>
                        
                        <h4 class="mt-4">Digital Banking</h4>
                        <p>Modern digital banking solutions for financial institutions:</p>
                        <ul>
                            <li>Mobile banking applications</li>
                            <li>Online account management</li>
                            <li>Secure transaction processing</li>
                            <li>Customer relationship management</li>
                            <li>Financial analytics</li>
                        </ul>
                        
                        <h4 class="mt-4">Financial Software</h4>
                        <p>Custom financial software development:</p>
                        <ul>
                            <li>Accounting systems</li>
                            <li>Investment tracking</li>
                            <li>Budget management tools</li>
                            <li>Risk assessment platforms</li>
                            <li>Financial reporting solutions</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Technology Stack</h3>
                    </div>
                    <div class="card-body">
                        <p>We use cutting-edge technologies to deliver our financial solutions:</p>
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Frontend</h5>
                                <ul>
                                    <li>React</li>
                                    <li>Angular</li>
                                    <li>Flutter for mobile apps</li>
                                    <li>Progressive Web Apps</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5>Backend</h5>
                                <ul>
                                    <li>Node.js</li>
                                    <li>Python</li>
                                    <li>Blockchain technologies</li>
                                    <li>Secure API development</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Request a Consultation</h3>
                    </div>
                    <div class="card-body">
                        <form action="/fintech-request" method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="company" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="company" name="company" required>
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
                                <label for="service_type" class="form-label">Service Type</label>
                                <select class="form-select" id="service_type" name="service_type" required>
                                    <option value="">Select a service</option>
                                    <option value="payment">Payment Processing</option>
                                    <option value="banking">Digital Banking</option>
                                    <option value="software">Financial Software</option>
                                    <option value="consulting">Financial Consulting</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="message" class="form-label">Project Details</label>
                                <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Submit Request</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Our Clients</h3>
                    </div>
                    <div class="card-body">
                        <p>We've worked with a range of financial institutions and businesses:</p>
                        <ul>
                            <li>Regional banks</li>
                            <li>Credit unions</li>
                            <li>Payment processors</li>
                            <li>E-commerce businesses</li>
                            <li>Investment firms</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Why Choose Us?</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li>✓ Bank-grade security</li>
                            <li>✓ Compliant with financial regulations</li>
                            <li>✓ Scalable solutions</li>
                            <li>✓ Dedicated support team</li>
                            <li>✓ Competitive pricing</li>
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