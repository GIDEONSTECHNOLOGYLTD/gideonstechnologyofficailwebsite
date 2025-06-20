<?php
/**
 * User Service History Template
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Service History' ?> | <?= $appName ?? 'Gideons Technology' ?></title>
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
                            <a class="nav-link" href="/user/service-requests">
                                <i class="bi bi-tools me-1"></i> Service Requests
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="/user/service-history">
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
                    <h1 class="h2">Service History</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="/user/new-service-request" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> New Service Request
                        </a>
                    </div>
                </div>
                
                <?php include __DIR__ . '/../partials/flash.php'; ?>
                
                <?php if (empty($serviceHistory)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> You don't have any completed services yet. 
                        <a href="/user/service-requests" class="alert-link">Check your active service requests</a> or 
                        <a href="/user/new-service-request" class="alert-link">create a new service request</a>.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($serviceHistory as $history): ?>
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0"><?= htmlspecialchars($history['service_name']) ?></h5>
                                        <small class="text-muted"><?= date('M j, Y', strtotime($history['completion_date'])) ?></small>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($history['request_title']) ?></h6>
                                        <p class="card-text"><?= nl2br(htmlspecialchars($history['service_details'])) ?></p>
                                        
                                        <?php if (!empty($history['feedback'])): ?>
                                            <div class="mb-3 mt-4">
                                                <h6>Your Feedback</h6>
                                                <div class="border-start border-4 ps-3 py-2 bg-light">
                                                    <div class="mb-2">
                                                        <span class="text-warning">
                                                            <?php for ($i = 0; $i < $history['rating']; $i++): ?>
                                                                <i class="bi bi-star-fill"></i>
                                                            <?php endfor; ?>
                                                            <?php for ($i = $history['rating']; $i < 5; $i++): ?>
                                                                <i class="bi bi-star"></i>
                                                            <?php endfor; ?>
                                                        </span>
                                                        <span class="ms-2"><?= $history['rating'] ?>/5</span>
                                                    </div>
                                                    <?= nl2br(htmlspecialchars($history['feedback'])) ?>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="mt-4">
                                                <h6>Provide Feedback</h6>
                                                <form action="/user/service-history/<?= $history['id'] ?>/feedback" method="post">
                                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?? '' ?>">
                                                    
                                                    <div class="mb-3">
                                                        <label for="rating-<?= $history['id'] ?>" class="form-label">Rate our service</label>
                                                        <div class="rating">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="rating" id="star5-<?= $history['id'] ?>" value="5" required>
                                                                <label class="form-check-label" for="star5-<?= $history['id'] ?>">5 <i class="bi bi-star-fill text-warning"></i></label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="rating" id="star4-<?= $history['id'] ?>" value="4">
                                                                <label class="form-check-label" for="star4-<?= $history['id'] ?>">4 <i class="bi bi-star-fill text-warning"></i></label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="rating" id="star3-<?= $history['id'] ?>" value="3">
                                                                <label class="form-check-label" for="star3-<?= $history['id'] ?>">3 <i class="bi bi-star-fill text-warning"></i></label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="rating" id="star2-<?= $history['id'] ?>" value="2">
                                                                <label class="form-check-label" for="star2-<?= $history['id'] ?>">2 <i class="bi bi-star-fill text-warning"></i></label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="rating" id="star1-<?= $history['id'] ?>" value="1">
                                                                <label class="form-check-label" for="star1-<?= $history['id'] ?>">1 <i class="bi bi-star-fill text-warning"></i></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label for="feedback-<?= $history['id'] ?>" class="form-label">Your comments</label>
                                                        <textarea class="form-control" id="feedback-<?= $history['id'] ?>" name="feedback" rows="3" required></textarea>
                                                    </div>
                                                    
                                                    <button type="submit" class="btn btn-primary">Submit Feedback</button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-footer text-muted">
                                        <small>Reference ID: #<?= htmlspecialchars($history['id']) ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
    
    <!-- Footer -->
    <?php include __DIR__ . '/../partials/footer.php'; ?>
    
    <!-- JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
