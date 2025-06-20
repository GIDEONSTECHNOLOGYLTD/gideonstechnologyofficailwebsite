<?php
/**
 * User Consultations Template
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'My Consultations' ?> | <?= $appName ?? 'Gideons Technology' ?></title>
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
                            <a class="nav-link" href="/user/service-history">
                                <i class="bi bi-clock-history me-1"></i> Service History
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="/user/consultations">
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
                    <h1 class="h2">My Consultations</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="/user/new-consultation" class="btn btn-primary">
                            <i class="bi bi-calendar-plus"></i> Schedule Consultation
                        </a>
                    </div>
                </div>
                
                <?php include __DIR__ . '/../partials/flash.php'; ?>
                
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link <?= (!isset($status) || $status === 'upcoming') ? 'active' : '' ?>" href="/user/consultations?status=upcoming">
                            <i class="bi bi-calendar-event"></i> Upcoming
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($status) && $status === 'past') ? 'active' : '' ?>" href="/user/consultations?status=past">
                            <i class="bi bi-calendar-check"></i> Past
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($status) && $status === 'cancelled') ? 'active' : '' ?>" href="/user/consultations?status=cancelled">
                            <i class="bi bi-calendar-x"></i> Cancelled
                        </a>
                    </li>
                </ul>
                
                <?php if (empty($consultations)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        <?php if (isset($status) && $status === 'past'): ?>
                            You don't have any past consultations.
                        <?php elseif (isset($status) && $status === 'cancelled'): ?>
                            You don't have any cancelled consultations.
                        <?php else: ?>
                            You don't have any upcoming consultations scheduled. 
                            <a href="/user/new-consultation" class="alert-link">Schedule a consultation</a> with our experts.
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($consultations as $consultation): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <?php 
                                        $statusClass = 'primary';
                                        $statusIcon = 'calendar-check';
                                        
                                        switch ($consultation['status']) {
                                            case 'scheduled':
                                                $statusClass = 'primary';
                                                $statusIcon = 'calendar-check';
                                                break;
                                            case 'completed':
                                                $statusClass = 'success';
                                                $statusIcon = 'check-circle';
                                                break;
                                            case 'cancelled':
                                                $statusClass = 'danger';
                                                $statusIcon = 'calendar-x';
                                                break;
                                        }
                                    ?>
                                    <div class="card-header bg-<?= $statusClass ?> bg-opacity-10 d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <i class="bi bi-<?= $statusIcon ?> me-2 text-<?= $statusClass ?>"></i>
                                            <?= date('l, F j', strtotime($consultation['date'])) ?>
                                        </h5>
                                        <span class="badge bg-<?= $statusClass ?>">
                                            <?= ucfirst($consultation['status']) ?>
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-3 text-muted">
                                            <?= date('g:i A', strtotime($consultation['time'])) ?>
                                            <?= !empty($consultation['duration']) ? '(' . $consultation['duration'] . ' minutes)' : '' ?>
                                        </h6>
                                        
                                        <dl class="row mb-0">
                                            <dt class="col-sm-4">Type</dt>
                                            <dd class="col-sm-8"><?= htmlspecialchars($consultation['type']) ?></dd>
                                            
                                            <?php if (!empty($consultation['service_name'])): ?>
                                            <dt class="col-sm-4">Service</dt>
                                            <dd class="col-sm-8"><?= htmlspecialchars($consultation['service_name']) ?></dd>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($consultation['consultant_name'])): ?>
                                            <dt class="col-sm-4">Consultant</dt>
                                            <dd class="col-sm-8"><?= htmlspecialchars($consultation['consultant_name']) ?></dd>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($consultation['location'])): ?>
                                            <dt class="col-sm-4">Location</dt>
                                            <dd class="col-sm-8"><?= htmlspecialchars($consultation['location']) ?></dd>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($consultation['meeting_link'])): ?>
                                            <dt class="col-sm-4">Meeting</dt>
                                            <dd class="col-sm-8">
                                                <a href="<?= htmlspecialchars($consultation['meeting_link']) ?>" target="_blank">
                                                    Join Online <i class="bi bi-box-arrow-up-right"></i>
                                                </a>
                                            </dd>
                                            <?php endif; ?>
                                        </dl>
                                        
                                        <?php if (!empty($consultation['notes'])): ?>
                                            <h6 class="mt-3">Notes</h6>
                                            <p class="card-text"><?= nl2br(htmlspecialchars($consultation['notes'])) ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-footer">
                                        <?php if ($consultation['status'] === 'scheduled'): ?>
                                            <div class="d-flex justify-content-between">
                                                <a href="/user/consultation/<?= $consultation['id'] ?>/reschedule" class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-calendar-week"></i> Reschedule
                                                </a>
                                                <form action="/user/consultation/<?= $consultation['id'] ?>/cancel" method="post" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?? '' ?>">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to cancel this consultation?')">
                                                        <i class="bi bi-x-circle"></i> Cancel
                                                    </button>
                                                </form>
                                            </div>
                                        <?php else: ?>
                                            <small class="text-muted">Confirmation #<?= htmlspecialchars($consultation['id']) ?></small>
                                        <?php endif; ?>
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
