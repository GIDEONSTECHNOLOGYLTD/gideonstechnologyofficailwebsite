<?php
// Profile index page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $appName ?> - Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        Profile Information
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <img src="https://via.placeholder.com/150" class="rounded-circle" alt="Profile Picture">
                        </div>
                        <h3 class="text-center"><?= htmlspecialchars($user['name']) ?></h3>
                        <p class="text-center text-muted">Member since <?= date('F Y', strtotime($user['joined'])) ?></p>
                        
                        <hr>
                        
                        <div class="mb-3">
                            <label class="form-label">Email:</label>
                            <div><?= htmlspecialchars($user['email']) ?></div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="/profile/edit" class="btn btn-primary">Edit Profile</a>
                            <a href="/user/dashboard" class="btn btn-secondary">Back to Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Account Activity
                    </div>
                    <div class="card-body">
                        <h5>Recent Orders</h5>
                        <p>You haven't placed any orders yet.</p>
                        
                        <hr>
                        
                        <h5>Services Used</h5>
                        <p>You haven't used any services yet.</p>
                        
                        <hr>
                        
                        <h5>Account Settings</h5>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Two-Factor Authentication
                                <span class="badge bg-danger rounded-pill">Disabled</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Email Notifications
                                <span class="badge bg-success rounded-pill">Enabled</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>