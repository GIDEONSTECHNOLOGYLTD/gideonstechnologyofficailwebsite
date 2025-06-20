<?php
/**
 * User Settings Template
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings | <?= $appName ?? 'Gideons Technology' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/"><?= $appName ?? 'Gideons Technology' ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gtech">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gstore">Store</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/logout">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- User Settings Content -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <!-- Sidebar -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-person-circle"></i> Welcome, <?= htmlspecialchars($user['name'] ?? 'User') ?>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="/user/dashboard" class="list-group-item list-group-item-action">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                        <a href="/user/profile" class="list-group-item list-group-item-action">
                            <i class="bi bi-person me-2"></i> Profile
                        </a>
                        <a href="/user/orders" class="list-group-item list-group-item-action">
                            <i class="bi bi-box me-2"></i> Orders
                        </a>
                        <a href="/user/settings" class="list-group-item list-group-item-action active">
                            <i class="bi bi-gear me-2"></i> Settings
                        </a>
                        <a href="/logout" class="list-group-item list-group-item-action text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <!-- Account Settings -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="m-0">Account Settings</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['flash']['success'])): ?>
                            <div class="alert alert-success">
                                <?= $_SESSION['flash']['success'] ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_SESSION['flash']['error'])): ?>
                            <div class="alert alert-danger">
                                <?= $_SESSION['flash']['error'] ?>
                            </div>
                        <?php endif; ?>
                        
                        <form action="/user/settings/update" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                            
                            <h5 class="border-bottom pb-2 mb-4">Notification Preferences</h5>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="email_notifications" name="email_notifications" <?= ($user['email_notifications'] ?? false) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="email_notifications">Receive email notifications</label>
                                <div class="form-text">Get updates about your orders, account activity, and promotions</div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="marketing_emails" name="marketing_emails" <?= ($user['marketing_emails'] ?? false) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="marketing_emails">Receive marketing emails</label>
                                <div class="form-text">Get information about new products, services, and special offers</div>
                            </div>
                            
                            <h5 class="border-bottom pb-2 mb-4 mt-5">Privacy Settings</h5>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="data_collection" name="data_collection" <?= ($user['data_collection'] ?? true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="data_collection">Allow data collection for personalization</label>
                                <div class="form-text">We use this data to improve your experience and recommend relevant products</div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="third_party_sharing" name="third_party_sharing" <?= ($user['third_party_sharing'] ?? false) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="third_party_sharing">Allow sharing data with trusted partners</label>
                                <div class="form-text">We only share anonymized data with trusted partners to improve our services</div>
                            </div>
                            
                            <h5 class="border-bottom pb-2 mb-4 mt-5">Display Settings</h5>
                            
                            <div class="mb-3">
                                <label for="theme_preference" class="form-label">Theme Preference</label>
                                <select class="form-select" id="theme_preference" name="theme_preference">
                                    <option value="system" <?= ($user['theme_preference'] ?? 'system') === 'system' ? 'selected' : '' ?>>System Default</option>
                                    <option value="light" <?= ($user['theme_preference'] ?? '') === 'light' ? 'selected' : '' ?>>Light Mode</option>
                                    <option value="dark" <?= ($user['theme_preference'] ?? '') === 'dark' ? 'selected' : '' ?>>Dark Mode</option>
                                </select>
                                <div class="form-text">Choose your preferred display theme</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="language_preference" class="form-label">Language Preference</label>
                                <select class="form-select" id="language_preference" name="language_preference">
                                    <option value="en" <?= ($user['language_preference'] ?? 'en') === 'en' ? 'selected' : '' ?>>English</option>
                                    <option value="es" <?= ($user['language_preference'] ?? '') === 'es' ? 'selected' : '' ?>>Español</option>
                                    <option value="fr" <?= ($user['language_preference'] ?? '') === 'fr' ? 'selected' : '' ?>>Français</option>
                                </select>
                                <div class="form-text">Choose your preferred language</div>
                            </div>
                            
                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary">Save Settings</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Account Management -->
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h4 class="m-0">Account Management</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h5>Export Your Data</h5>
                                <p>Download a copy of your personal data</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="/user/data/export" class="btn btn-outline-primary">Export Data</a>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <h5>Delete Account</h5>
                                <p class="text-danger">Permanently delete your account and all associated data</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                    Delete Account
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteAccountModalLabel">Delete Account</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/user/account/delete" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Warning:</strong> This action cannot be undone.
                        </div>
                        <p>Deleting your account will:</p>
                        <ul>
                            <li>Permanently remove all your personal information</li>
                            <li>Delete your order history and saved items</li>
                            <li>Cancel any active subscriptions</li>
                            <li>Remove you from all mailing lists</li>
                        </ul>
                        <div class="mb-3">
                            <label for="delete_confirmation" class="form-label">Type "DELETE" to confirm</label>
                            <input type="text" class="form-control" id="delete_confirmation" name="delete_confirmation" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Enter your password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5>Gideons Technology</h5>
                    <p>Providing innovative technology solutions for businesses and individuals.</p>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="/" class="text-white">Home</a></li>
                        <li><a href="/gtech" class="text-white">Services</a></li>
                        <li><a href="/gstore" class="text-white">Store</a></li>
                        <li><a href="/contact" class="text-white">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <address>
                        <p class="mb-1"><i class="bi bi-geo-alt me-2"></i> 123 Tech Street, Silicon Valley</p>
                        <p class="mb-1"><i class="bi bi-envelope me-2"></i> info@gideonstech.com</p>
                        <p class="mb-1"><i class="bi bi-telephone me-2"></i> (123) 456-7890</p>
                    </address>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p class="mb-0">&copy; <?= date('Y') ?> Gideons Technology. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
