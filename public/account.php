<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_once 'includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit;
}

// Fetch current user record
$user = getUserByEmail($_SESSION['user_email']);
if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$page_title = "My Account - " . SITE_NAME;
$meta_description = "Manage your Gideons Technology account settings and view your services";
$meta_keywords = "account, profile, settings, services";

require_once 'includes/header.php';

$errors = [];
$success = false;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle password change
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validate inputs
        if (empty($current_password)) {
            $errors['current_password'] = 'Current password is required';
        }
        if (empty($new_password)) {
            $errors['new_password'] = 'New password is required';
        } elseif (strlen($new_password) < 8) {
            $errors['new_password'] = 'New password must be at least 8 characters';
        }
        if ($new_password !== $confirm_password) {
            $errors['confirm_password'] = 'Passwords do not match';
        }

        if (empty($errors)) {
            // Verify current password
            if (password_verify($current_password, $user['password'])) {
                // Update password
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                if ($stmt->execute([$hashed_password, $user['id']])) {
                    $success = true;
                } else {
                    $errors['general'] = 'Failed to update password. Please try again.';
                }
            } else {
                $errors['current_password'] = 'Current password is incorrect';
            }
        }
    }

    // Handle profile update
    if (isset($_POST['update_profile'])) {
        // Validate and sanitize input
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        // Validate required fields
        if (empty($first_name)) {
            $errors['first_name'] = 'First name is required';
        }
        if (empty($last_name)) {
            $errors['last_name'] = 'Last name is required';
        }

        // Handle avatar upload
        if (!empty($_FILES['avatar']['name'])) {
            $allowed = ['jpg', 'jpeg', 'png'];
            $filename = $_FILES['avatar']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                $errors['avatar'] = 'Invalid file type. Only JPG and PNG are allowed.';
            } elseif ($_FILES['avatar']['size'] > 5 * 1024 * 1024) { // 5MB
                $errors['avatar'] = 'File size too large. Maximum size is 5MB.';
            } else {
                $avatar = 'avatar_' . time() . '_' . $user['id'] . '.' . $ext;
                $target = __DIR__ . '/assets/img/' . $avatar;
                
                if (move_uploaded_file($_FILES['avatar']['tmp_file'], $target)) {
                    // Delete old avatar if exists
                    if (!empty($user['avatar'])) {
                        $old_avatar = __DIR__ . '/assets/img/' . $user['avatar'];
                        if (file_exists($old_avatar)) {
                            unlink($old_avatar);
                        }
                    }
                } else {
                    $errors['avatar'] = 'Failed to upload image. Please try again.';
                }
            }
        }

        // Update profile if no errors
        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("
                    UPDATE users 
                    SET first_name = ?, last_name = ?, phone = ?, address = ?
                    " . (!empty($avatar) ? ", avatar = ?" : "") . "
                    WHERE id = ?
                ");

                $params = [$first_name, $last_name, $phone, $address];
                if (!empty($avatar)) {
                    $params[] = $avatar;
                }
                $params[] = $user['id'];

                if ($stmt->execute($params)) {
                    // Update session name
                    $_SESSION['user_name'] = trim($first_name . ' ' . $last_name);
                    
                    // Refresh user data
                    $user = getUserByEmail($_SESSION['user_email']);
                    $success = true;
                } else {
                    $errors['general'] = 'Failed to update profile. Please try again.';
                }
            } catch(PDOException $e) {
                $errors['general'] = 'Database error. Please try again.';
            }
        }
        // Handle avatar upload
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['avatar'];
            if (!in_array($file['type'], ALLOWED_FILE_TYPES)) {
                $errors['avatar'] = 'Invalid file type. Allowed types: ' . implode(', ', array_map(function($type) { 
                    return str_replace(['application/', 'image/'], '', $type);
                }, ALLOWED_FILE_TYPES));
            } elseif ($file['size'] > MAX_FILE_SIZE) {
                $errors['avatar'] = 'File size too large. Maximum size is ' . (MAX_FILE_SIZE / (1024 * 1024)) . 'MB';
            } else {
                $filename = uniqid('avatar_') . '_' . basename($file['name']);
                $uploadPath = UPLOADS_PATH . '/avatars/' . $filename;
                
                // Create avatars directory if it doesn't exist
                if (!is_dir(UPLOADS_PATH . '/avatars')) {
                    mkdir(UPLOADS_PATH . '/avatars', 0755, true);
                }
                
                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    // Update user's avatar in database
                    $stmt = $pdo->prepare('UPDATE users SET avatar = ? WHERE id = ?');
                    $stmt->execute([$filename, $_SESSION['user_id']]);
                    $success = true;
                } else {
                    $errors['avatar'] = 'Failed to upload file. Please try again.';
                }
            }
        }

        $name = $_POST['name'] ?? '';
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Update name
        if (!empty($name) && $name !== $_SESSION['user_name']) {
            // Update name in database
            // updateUserName($_SESSION['user_id'], $name);
            $_SESSION['user_name'] = $name;
            $success = true;
        }

        // Update password
        if (!empty($current_password)) {
            $user = getUserByEmail($_SESSION['user_email']);
            if (!password_verify($current_password, $user['password_hash'])) {
                $errors[] = "Current password is incorrect";
            } elseif (empty($new_password)) {
                $errors[] = "New password is required";
            } elseif (strlen($new_password) < 8) {
                $errors[] = "New password must be at least 8 characters long";
            } elseif ($new_password !== $confirm_password) {
                $errors[] = "New passwords do not match";
            } else {
                updateUserPassword($user['id'], $new_password);
                $success = true;
            }
        }
    }
}
?>

<!-- Account Section -->
<section class="account-section py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <img src="<?php echo isset($user['avatar']) && $user['avatar'] ? SITE_URL . '/assets/img/' . htmlspecialchars($user['avatar']) : SITE_URL . '/assets/img/avatar-placeholder.png'; ?>" alt="Profile"
                                     class="rounded-circle" width="50" height="50">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-0"><?php echo htmlspecialchars($_SESSION['user_name']); ?></h5>
                                <small class="text-muted"><?php echo htmlspecialchars($_SESSION['user_email']); ?></small>
                            </div>
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="#profile" class="list-group-item list-group-item-action active" 
                               data-bs-toggle="list">Profile</a>
                            <a href="#security" class="list-group-item list-group-item-action" 
                               data-bs-toggle="list">Security</a>
                            <a href="#templates" class="list-group-item list-group-item-action" data-bs-toggle="list">My Templates</a>
                            <a href="#services" class="list-group-item list-group-item-action" 
                               data-bs-toggle="list">My Services</a>
                            <a href="#billing" class="list-group-item list-group-item-action"
                               data-bs-toggle="list">Billing</a>
                            <a href="#notifications" class="list-group-item list-group-item-action" 
                               data-bs-toggle="list">Notifications</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Your changes have been saved successfully.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="tab-content">
                    <!-- Profile Tab -->
                    <div class="tab-pane fade show active" id="profile">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h3 class="card-title">Profile Information</h3>
                                <form method="POST" action="" enctype="multipart/form-data">
                                    <input type="hidden" name="update_profile" value="1">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Profile Picture</label>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo isset($user['avatar']) && $user['avatar'] ? SITE_URL . '/assets/img/' . htmlspecialchars($user['avatar']) : SITE_URL . '/assets/img/avatar-placeholder.svg'; ?>" 
                                                 alt="Profile" class="rounded-circle me-3" width="100" height="100">
                                            <div class="flex-grow-1">
                                                <input type="file" class="form-control" name="avatar" accept="image/*">
                                                <small class="text-muted">Max file size: 5MB. Supported formats: JPG, PNG</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">First Name</label>
                                            <input type="text" class="form-control" name="first_name" 
                                                   value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" class="form-control" name="last_name" 
                                                   value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="tel" class="form-control" name="phone" 
                                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <textarea class="form-control" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                    </div>

                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>

                                <script>
                                document.querySelector('input[name="avatar"]').addEventListener('change', function(e) {
                                    var file = e.target.files[0];
                                    if (file) {
                                        var img = this.closest('.d-flex').querySelector('img');
                                        img.src = URL.createObjectURL(file);
                                    }
                                });
                                </script>

                            </div>
                        </div>
                    </div>

                    <!-- Security Tab -->
                    <div class="tab-pane fade" id="security">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h3 class="card-title">Change Password</h3>
                                <form method="POST" action="">
                                    <input type="hidden" name="change_password" value="1">

                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" 
                                               name="current_password" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" 
                                               name="new_password" minlength="8" required>
                                        <small class="text-muted">Minimum 8 characters</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" 
                                               name="confirm_password" required>
                                    </div>

                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Change Password</button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>

                    <!-- Security Tab -->
                    <div class="tab-pane fade" id="security">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h3 class="card-title">Security Settings</h3>
                                <div class="mb-4">
                                    <h5>Two-Factor Authentication</h5>
                                    <p class="text-muted">Add additional security to your account using two-factor authentication.</p>
                                    <button class="btn btn-primary">Enable 2FA</button>
                                </div>

                                <div class="mb-4">
                                    <h5>Recent Activity</h5>
                                    <div class="list-group">
                                        <div class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">Login from Chrome on Windows</h6>
                                                <small>3 days ago</small>
                                            </div>
                                            <p class="mb-1">IP: 192.168.1.1</p>
                                        </div>
                                        <!-- Add more activity items here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Templates Tab -->
                    <div class="tab-pane fade" id="templates">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h3 class="card-title">My Templates</h3>
                                <?php
                                require_once 'includes/purchase_history.php';
                                $purchased = getPurchasedTemplates($_SESSION['user_id']);
                                if (empty($purchased)): ?>
                                    <div class="alert alert-info">
                                        <p class="mb-0">You haven't purchased any templates yet. Browse our <a href="<?php echo SITE_URL; ?>/templates">templates page</a> to get started!</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Template</th>
                                                    <th>Price</th>
                                                    <th>Purchased On</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($purchased as $item): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($item->name); ?></td>
                                                        <td>$<?php echo number_format($item->price,2); ?></td>
                                                        <td><?php echo date('M j, Y', strtotime($item->purchase_date)); ?></td>
                                                        <td>
                                                            <a href="<?php echo SITE_URL; ?>/templates/<?php echo $item->id; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Services Tab -->
                    <div class="tab-pane fade" id="services">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h3 class="card-title">My Services</h3>
                                
                                <?php
                                require_once 'includes/service_history.php';
                                $services = getServiceHistory($_SESSION['user_id']);
                                
                                if (empty($services)): ?>
                                    <div class="alert alert-info">
                                        <p class="mb-0">You haven't used any services yet. Check out our <a href="services.php">services page</a> to get started!</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Service</th>
                                                    <th>Project</th>
                                                    <th>Status</th>
                                                    <th>Started</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($services as $service): ?>
                                                    <tr>
                                                        <td>
                                                            <i class="<?php echo getServiceIcon($service['type']); ?> me-2"></i>
                                                            <?php echo htmlspecialchars($service['name']); ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($service['project_name'] ?? 'N/A'); ?></td>
                                                        <td><?php echo getServiceStatusBadge($service['project_status'] ?? 'pending'); ?></td>
                                                        <td><?php echo date('M j, Y', strtotime($service['created_at'])); ?></td>
                                                        <td>
                                                            <a href="project.php?id=<?php echo $service['project_id']; ?>" 
                                                               class="btn btn-sm btn-outline-primary">
                                                                View Details
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Billing Tab -->
                    <div class="tab-pane fade" id="billing">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h3 class="card-title">Billing & Payments</h3>
                                
                                <?php
                                require_once 'includes/billing.php';
                                $upcomingPayments = getUpcomingPayments($_SESSION['user_id']);
                                $billingHistory = getBillingHistory($_SESSION['user_id']);
                                
                                if (!empty($upcomingPayments)): ?>
                                    <div class="upcoming-payments mb-4">
                                        <h4>Upcoming Payments</h4>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Service</th>
                                                        <th>Amount</th>
                                                        <th>Due Date</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($upcomingPayments as $payment): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($payment['service_name']); ?></td>
                                                            <td><?php echo formatCurrency($payment['amount']); ?></td>
                                                            <td><?php echo date('M j, Y', strtotime($payment['due_date'])); ?></td>
                                                            <td>
                                                                <a href="payment.php?invoice=<?php echo $payment['id']; ?>" 
                                                                   class="btn btn-sm btn-primary">Pay Now</a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="billing-history">
                                    <h4>Billing History</h4>
                                    <?php if (empty($billingHistory)): ?>
                                        <div class="alert alert-info">
                                            <p class="mb-0">No billing history available.</p>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Invoice #</th>
                                                        <th>Service</th>
                                                        <th>Amount</th>
                                                        <th>Date</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($billingHistory as $invoice): ?>
                                                        <tr>
                                                            <td>INV-<?php echo str_pad($invoice['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                                            <td><?php echo htmlspecialchars($invoice['service_name']); ?></td>
                                                            <td><?php echo formatCurrency($invoice['amount']); ?></td>
                                                            <td><?php echo date('M j, Y', strtotime($invoice['created_at'])); ?></td>
                                                            <td><?php echo getPaymentStatusBadge($invoice['status']); ?></td>
                                                            <td>
                                                                <div class="btn-group btn-group-sm">
                                                                    <a href="invoice.php?id=<?php echo $invoice['id']; ?>" 
                                                                       class="btn btn-outline-secondary" title="View Invoice">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                    <a href="invoice.php?id=<?php echo $invoice['id']; ?>&download=pdf" 
                                                                       class="btn btn-outline-secondary" title="Download PDF">
                                                                        <i class="fas fa-download"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications Tab -->
                    <div class="tab-pane fade" id="notifications">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h3 class="card-title">Notification Settings</h3>
                                <form>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="emailNotifications" checked>
                                        <label class="form-check-label" for="emailNotifications">
                                            Email Notifications
                                        </label>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="smsNotifications">
                                        <label class="form-check-label" for="smsNotifications">
                                            SMS Notifications
                                        </label>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save Preferences</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
