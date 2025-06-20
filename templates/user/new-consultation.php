<?php
/**
 * New Consultation Form Template
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Consultation | Gideons Technology</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <!-- Navigation -->
    <?php include __DIR__ . '/../partials/navbar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="/user/dashboard"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/user/profile"><i class="bi bi-person me-1"></i> Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/user/service-requests"><i class="bi bi-tools me-1"></i> Service Requests</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/user/service-history"><i class="bi bi-clock-history me-1"></i> Service History</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="/user/consultations"><i class="bi bi-calendar-check me-1"></i> Consultations</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/user/settings"><i class="bi bi-gear me-1"></i> Settings</a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Schedule a Consultation</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="/user/consultations" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Consultations
                        </a>
                    </div>
                </div>
                
                <?php include __DIR__ . '/../partials/flash.php'; ?>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Consultation Details</h5>
                            </div>
                            <div class="card-body">
                                <form action="/user/consultation/create" method="post">
                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?? '' ?>">
                                    
                                    <div class="mb-3">
                                        <label for="topic" class="form-label">Consultation Topic *</label>
                                        <input type="text" class="form-control" id="topic" name="topic" 
                                               placeholder="Enter the topic for this consultation" 
                                               value="<?= htmlspecialchars($formData['topic'] ?? '') ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="service_id" class="form-label">Related Service (Optional)</label>
                                        <select class="form-select" id="service_id" name="service_id">
                                            <option value="">Select a service...</option>
                                            <?php foreach ($services as $service): ?>
                                                <option value="<?= $service['id'] ?>">
                                                    <?= htmlspecialchars($service['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="date" class="form-label">Preferred Date *</label>
                                            <input type="date" class="form-control" id="date" name="date" required
                                                  min="<?= date('Y-m-d') ?>" value="<?= $formData['date'] ?? '' ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="time" class="form-label">Preferred Time *</label>
                                            <select class="form-select" id="time" name="time" required>
                                                <option value="">Select a time...</option>
                                                <option value="09:00">9:00 AM</option>
                                                <option value="10:00">10:00 AM</option>
                                                <option value="11:00">11:00 AM</option>
                                                <option value="13:00">1:00 PM</option>
                                                <option value="14:00">2:00 PM</option>
                                                <option value="15:00">3:00 PM</option>
                                                <option value="16:00">4:00 PM</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="format" class="form-label">Consultation Format *</label>
                                        <select class="form-select" id="format" name="format" required>
                                            <option value="video" <?= (isset($formData['format']) && $formData['format'] == 'video') ? 'selected' : '' ?>>Video Call</option>
                                            <option value="phone" <?= (isset($formData['format']) && $formData['format'] == 'phone') ? 'selected' : '' ?>>Phone Call</option>
                                            <option value="in_person" <?= (isset($formData['format']) && $formData['format'] == 'in_person') ? 'selected' : '' ?>>In Person</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description *</label>
                                        <textarea class="form-control" id="description" name="description" 
                                                  rows="4" placeholder="Please describe what you'd like to discuss" 
                                                  required><?= htmlspecialchars($formData['description'] ?? '') ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3 form-text">
                                        Fields marked with * are required
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-calendar-plus"></i> Schedule Consultation
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Consultation Information</h5>
                            </div>
                            <div class="card-body">
                                <h6>About Our Consultations</h6>
                                <p>Our experts are available for one-on-one consultations to discuss your technology needs, project requirements, or any tech-related questions.</p>
                                
                                <h6>What to Expect</h6>
                                <ul>
                                    <li>30-minute sessions with our tech specialists</li>
                                    <li>Personalized advice and solutions</li>
                                    <li>Follow-up documentation after the consultation</li>
                                    <li>Option for additional sessions if needed</li>
                                </ul>
                                
                                <h6>Need Immediate Help?</h6>
                                <p>For urgent matters, please contact our support team directly.</p>
                                <a href="/contact" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-telephone"></i> Contact Support
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Footer -->
    <?php include __DIR__ . '/../partials/footer.php'; ?>
    
    <!-- JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
