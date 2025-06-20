<?php
/**
 * Service Request Detail Template
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Service Request Detail' ?> | <?= $appName ?? 'Gideons Technology' ?></title>
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
                    <h1 class="h2">Service Request: <?= htmlspecialchars($serviceRequest['title'] ?? 'Unknown') ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="/user/service-requests" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-arrow-left"></i> Back to Requests
                        </a>
                        <?php if ($serviceRequest['status'] === 'pending'): ?>
                            <form action="/user/service-request/<?= $serviceRequest['id'] ?>/cancel" method="post" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?? '' ?>">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this service request?')">
                                    <i class="bi bi-x-circle"></i> Cancel Request
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php include __DIR__ . '/../partials/flash.php'; ?>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Request Details</h5>
                                <?php 
                                    $statusClass = 'secondary';
                                    switch ($serviceRequest['status']) {
                                        case 'pending':
                                            $statusClass = 'warning';
                                            break;
                                        case 'in_progress':
                                            $statusClass = 'primary';
                                            break;
                                        case 'completed':
                                            $statusClass = 'success';
                                            break;
                                        case 'cancelled':
                                            $statusClass = 'danger';
                                            break;
                                    }
                                ?>
                                <span class="badge bg-<?= $statusClass ?>">
                                    <?= ucfirst(str_replace('_', ' ', htmlspecialchars($serviceRequest['status']))) ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <dl class="row">
                                    <dt class="col-sm-3">ID</dt>
                                    <dd class="col-sm-9">#<?= htmlspecialchars($serviceRequest['id']) ?></dd>
                                    
                                    <dt class="col-sm-3">Service Type</dt>
                                    <dd class="col-sm-9"><?= htmlspecialchars($serviceRequest['service_name'] ?? 'Unknown') ?></dd>
                                    
                                    <dt class="col-sm-3">Priority</dt>
                                    <dd class="col-sm-9">
                                        <?php 
                                            $priorityClass = 'info';
                                            switch ($serviceRequest['priority']) {
                                                case 'low':
                                                    $priorityClass = 'info';
                                                    break;
                                                case 'medium':
                                                    $priorityClass = 'secondary';
                                                    break;
                                                case 'high':
                                                    $priorityClass = 'warning';
                                                    break;
                                                case 'urgent':
                                                    $priorityClass = 'danger';
                                                    break;
                                            }
                                        ?>
                                        <span class="badge bg-<?= $priorityClass ?>">
                                            <?= ucfirst(htmlspecialchars($serviceRequest['priority'])) ?>
                                        </span>
                                    </dd>
                                    
                                    <dt class="col-sm-3">Created</dt>
                                    <dd class="col-sm-9"><?= date('F j, Y, g:i a', strtotime($serviceRequest['created_at'])) ?></dd>
                                    
                                    <?php if ($serviceRequest['updated_at'] && $serviceRequest['updated_at'] != $serviceRequest['created_at']): ?>
                                    <dt class="col-sm-3">Last Updated</dt>
                                    <dd class="col-sm-9"><?= date('F j, Y, g:i a', strtotime($serviceRequest['updated_at'])) ?></dd>
                                    <?php endif; ?>
                                </dl>
                                
                                <h6 class="mt-4">Description</h6>
                                <p class="border-start border-4 ps-3 py-2 bg-light"><?= nl2br(htmlspecialchars($serviceRequest['description'])) ?></p>
                            </div>
                        </div>
                        
                        <?php if (!empty($project)): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Project Status</h5>
                            </div>
                            <div class="card-body">
                                <dl class="row">
                                    <dt class="col-sm-3">Project Name</dt>
                                    <dd class="col-sm-9"><?= htmlspecialchars($project['title']) ?></dd>
                                    
                                    <dt class="col-sm-3">Status</dt>
                                    <dd class="col-sm-9">
                                        <?php 
                                            $projectStatusClass = 'secondary';
                                            switch ($project['status']) {
                                                case 'planning':
                                                    $projectStatusClass = 'info';
                                                    break;
                                                case 'in_progress':
                                                    $projectStatusClass = 'primary';
                                                    break;
                                                case 'review':
                                                    $projectStatusClass = 'warning';
                                                    break;
                                                case 'completed':
                                                    $projectStatusClass = 'success';
                                                    break;
                                                case 'on_hold':
                                                    $projectStatusClass = 'danger';
                                                    break;
                                            }
                                        ?>
                                        <span class="badge bg-<?= $projectStatusClass ?>">
                                            <?= ucfirst(str_replace('_', ' ', htmlspecialchars($project['status']))) ?>
                                        </span>
                                    </dd>
                                    
                                    <?php if (!empty($project['start_date'])): ?>
                                    <dt class="col-sm-3">Start Date</dt>
                                    <dd class="col-sm-9"><?= date('F j, Y', strtotime($project['start_date'])) ?></dd>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($project['estimated_completion_date'])): ?>
                                    <dt class="col-sm-3">Est. Completion</dt>
                                    <dd class="col-sm-9"><?= date('F j, Y', strtotime($project['estimated_completion_date'])) ?></dd>
                                    <?php endif; ?>
                                </dl>
                                
                                <?php if (!empty($project['milestones'])): ?>
                                <h6 class="mb-3 mt-4">Project Milestones</h6>
                                <div class="timeline">
                                    <?php foreach ($project['milestones'] as $milestone): ?>
                                        <div class="timeline-item">
                                            <?php 
                                                $milestoneStatusClass = 'secondary';
                                                switch ($milestone['status']) {
                                                    case 'pending':
                                                        $milestoneStatusClass = 'warning';
                                                        $icon = 'hourglass';
                                                        break;
                                                    case 'completed':
                                                        $milestoneStatusClass = 'success';
                                                        $icon = 'check-circle';
                                                        break;
                                                    case 'delayed':
                                                        $milestoneStatusClass = 'danger';
                                                        $icon = 'exclamation-circle';
                                                        break;
                                                }
                                            ?>
                                            <div class="timeline-badge bg-<?= $milestoneStatusClass ?>">
                                                <i class="bi bi-<?= $icon ?>"></i>
                                            </div>
                                            <div class="timeline-panel">
                                                <div class="timeline-heading">
                                                    <h6 class="mb-1"><?= htmlspecialchars($milestone['title']) ?></h6>
                                                    <small class="text-muted">
                                                        Due: <?= date('M j, Y', strtotime($milestone['due_date'])) ?>
                                                    </small>
                                                </div>
                                                <div class="timeline-body">
                                                    <p class="mb-0"><?= htmlspecialchars($milestone['description']) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Service Updates</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($serviceUpdates)): ?>
                                    <div class="list-group">
                                        <?php foreach($serviceUpdates as $update): ?>
                                            <div class="list-group-item list-group-item-action">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1"><?= htmlspecialchars($update['title']) ?></h6>
                                                    <small class="text-muted"><?= date('M j', strtotime($update['created_at'])) ?></small>
                                                </div>
                                                <p class="mb-1"><?= htmlspecialchars($update['message']) ?></p>
                                                <small><?= htmlspecialchars($update['created_by']) ?></small>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info mb-0">
                                        <i class="bi bi-info-circle"></i> No updates available yet. Our team will provide updates as they work on your service request.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Need Help?</h5>
                            </div>
                            <div class="card-body">
                                <p>If you have any questions about this service request, please contact our support team.</p>
                                <a href="/contact" class="btn btn-primary w-100">
                                    <i class="bi bi-envelope"></i> Contact Support
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
    <style>
        .timeline {
            position: relative;
            padding: 20px 0;
        }
        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 20px;
            width: 3px;
            background: #e9ecef;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 30px;
        }
        .timeline-badge {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            position: absolute;
            left: 0;
            top: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .timeline-panel {
            margin-left: 60px;
            padding: 10px 15px;
            background: #f8f9fa;
            border-radius: 4px;
            position: relative;
        }
    </style>
</body>
</html>
