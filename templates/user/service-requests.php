<?php
/**
 * User Service Requests Template
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'My Service Requests' ?> | <?= $appName ?? 'Gideons Technology' ?></title>
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
                    <h1 class="h2">My Service Requests</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="/user/new-service-request" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> New Service Request
                        </a>
                    </div>
                </div>
                
                <?php include __DIR__ . '/../partials/flash.php'; ?>
                
                <?php if (empty($serviceRequests)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> You don't have any service requests yet. 
                        <a href="/user/new-service-request" class="alert-link">Create your first service request</a>.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($serviceRequests as $request): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($request['id']) ?></td>
                                        <td><?= htmlspecialchars($request['title']) ?></td>
                                        <td><?= htmlspecialchars($request['service_name'] ?? 'Unknown') ?></td>
                                        <td>
                                            <?php 
                                                $statusClass = 'secondary';
                                                switch ($request['status']) {
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
                                                <?= ucfirst(str_replace('_', ' ', htmlspecialchars($request['status']))) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                                $priorityClass = 'info';
                                                switch ($request['priority']) {
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
                                                <?= ucfirst(htmlspecialchars($request['priority'])) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M j, Y', strtotime($request['created_at'])) ?></td>
                                        <td>
                                            <a href="/user/service-request/<?= $request['id'] ?>" class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
