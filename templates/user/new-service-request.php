<?php
/**
 * New Service Request Form Template
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Create Service Request' ?> | <?= $appName ?? 'Gideons Technology' ?></title>
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
                            <a class="nav-link" href="/user/dashboard">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/user/profile">
                                <i class="bi bi-person me-1"></i> Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="/user/service-requests">
                                <i class="bi bi-tools me-1"></i> Service Requests
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/user/service-history">
                                <i class="bi bi-clock-history me-1"></i> Service History
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/user/consultations">
                                <i class="bi bi-calendar-check me-1"></i> Consultations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/user/settings">
                                <i class="bi bi-gear me-1"></i> Settings
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Create New Service Request</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="/user/service-requests" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Requests
                        </a>
                    </div>
                </div>
                
                <?php include __DIR__ . '/../partials/flash.php'; ?>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Service Request Details</h5>
                            </div>
                            <div class="card-body">
                                <form action="/user/service-request/create" method="post">
                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?? '' ?>">
                                    
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Request Title *</label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                               placeholder="e.g. Website Development, Tech Support" 
                                               value="<?= htmlspecialchars($formData['title'] ?? '') ?>" required>
                                        <?php if (isset($errors['title'])): ?>
                                            <div class="text-danger mt-1"><?= $errors['title'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="service_id" class="form-label">Service Type *</label>
                                        <select class="form-select" id="service_id" name="service_id" required>
                                            <option value="">Select a service...</option>
                                            <?php foreach ($services as $service): ?>
                                                <option value="<?= $service['id'] ?>" <?= isset($formData['service_id']) && $formData['service_id'] == $service['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($service['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if (isset($errors['service_id'])): ?>
                                            <div class="text-danger mt-1"><?= $errors['service_id'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description *</label>
                                        <textarea class="form-control" id="description" name="description" 
                                                  rows="5" placeholder="Please provide details about your request" 
                                                  required><?= htmlspecialchars($formData['description'] ?? '') ?></textarea>
                                        <?php if (isset($errors['description'])): ?>
                                            <div class="text-danger mt-1"><?= $errors['description'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="priority" class="form-label">Priority *</label>
                                        <select class="form-select" id="priority" name="priority" required>
                                            <option value="low" <?= (isset($formData['priority']) && $formData['priority'] == 'low') ? 'selected' : '' ?>>Low - Not urgent</option>
                                            <option value="medium" <?= (isset($formData['priority']) && $formData['priority'] == 'medium') ? 'selected' : (!isset($formData['priority']) ? 'selected' : '') ?>>Medium - Standard</option>
                                            <option value="high" <?= (isset($formData['priority']) && $formData['priority'] == 'high') ? 'selected' : '' ?>>High - Important</option>
                                            <option value="urgent" <?= (isset($formData['priority']) && $formData['priority'] == 'urgent') ? 'selected' : '' ?>>Urgent - Critical issue</option>
                                        </select>
                                        <?php if (isset($errors['priority'])): ?>
                                            <div class="text-danger mt-1"><?= $errors['priority'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="preferred_contact" class="form-label">Preferred Contact Method</label>
                                        <select class="form-select" id="preferred_contact" name="preferred_contact">
                                            <option value="email" <?= (isset($formData['preferred_contact']) && $formData['preferred_contact'] == 'email') ? 'selected' : 'selected' ?>>Email</option>
                                            <option value="phone" <?= (isset($formData['preferred_contact']) && $formData['preferred_contact'] == 'phone') ? 'selected' : '' ?>>Phone</option>
                                            <option value="both" <?= (isset($formData['preferred_contact']) && $formData['preferred_contact'] == 'both') ? 'selected' : '' ?>>Both Email & Phone</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-text mb-3">
                                        Fields marked with * are required
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-send"></i> Submit Service Request
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Information</h5>
                            </div>
                            <div class="card-body">
                                <h6>About Our Services</h6>
                                <p>Our team of experts specializes in various technology solutions to help you achieve your goals.</p>
                                
                                <h6>Request Process</h6>
                                <ol class="ps-3">
                                    <li>Submit your service request</li>
                                    <li>Receive confirmation and initial assessment</li>
                                    <li>Our team reviews and processes your request</li>
                                    <li>Service execution and regular updates</li>
                                    <li>Completion and feedback</li>
                                </ol>
                                
                                <h6>Need an Urgent Consultation?</h6>
                                <p>For urgent matters, you can schedule a consultation with our experts.</p>
                                <a href="/user/consultations" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-calendar-plus"></i> Schedule Consultation
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
