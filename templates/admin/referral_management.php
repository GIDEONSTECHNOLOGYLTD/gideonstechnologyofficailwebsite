<?php
/**
 * Admin Referral Management Template
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referral Management | Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h5 class="text-white">Gideons Technology</h5>
                        <p class="text-white-50">Admin Dashboard</p>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/admin/dashboard">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/admin/users">
                                <i class="bi bi-people me-2"></i>
                                User Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/admin/products">
                                <i class="bi bi-box me-2"></i>
                                Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/admin/orders">
                                <i class="bi bi-bag me-2"></i>
                                Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/admin/services">
                                <i class="bi bi-tools me-2"></i>
                                Services
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/admin/tickets">
                                <i class="bi bi-ticket me-2"></i>
                                Support Tickets
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white active" href="/admin/referrals">
                                <i class="bi bi-share me-2"></i>
                                Referrals
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/admin/payments">
                                <i class="bi bi-credit-card me-2"></i>
                                Payments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/admin/settings">
                                <i class="bi bi-gear me-2"></i>
                                Settings
                            </a>
                        </li>
                    </ul>
                    <hr class="text-white-50">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="/admin/logout">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Referral Management</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                                <i class="bi bi-printer"></i> Print
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="exportBtn">
                                <i class="bi bi-file-excel"></i> Export
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Referral Program Stats -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="me-3">
                                        <div class="text-white-75">Total Rewards Paid</div>
                                        <div class="text-lg fw-bold">$<?= number_format($totalRewardsPaid ?? 0, 2) ?></div>
                                    </div>
                                    <i class="bi bi-currency-dollar fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="me-3">
                                        <div class="text-dark">Pending Approvals</div>
                                        <div class="text-lg fw-bold"><?= $pendingRewards ? count($pendingRewards) : 0 ?></div>
                                    </div>
                                    <i class="bi bi-hourglass-split fs-1 text-dark"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="me-3">
                                        <div class="text-white-75">Total Referrals</div>
                                        <div class="text-lg fw-bold"><?= $totalReferrals ?? 0 ?></div>
                                    </div>
                                    <i class="bi bi-people fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-danger text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="me-3">
                                        <div class="text-white-75">Conversion Rate</div>
                                        <div class="text-lg fw-bold"><?= $conversionRate ?? 0 ?>%</div>
                                    </div>
                                    <i class="bi bi-graph-up-arrow fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Referral Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-gear-fill me-1"></i>
                        Referral Program Settings
                    </div>
                    <div class="card-body">
                        <form id="referralSettingsForm" method="POST" action="/admin/referrals/settings">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="signupReward" class="form-label">Signup Reward ($)</label>
                                    <input type="number" class="form-control" id="signupReward" name="signupReward" value="<?= $settings['signup'] ?? '5.00' ?>" step="0.01" min="0">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="purchaseReward" class="form-label">First Purchase Reward ($)</label>
                                    <input type="number" class="form-control" id="purchaseReward" name="purchaseReward" value="<?= $settings['first_purchase'] ?? '10.00' ?>" step="0.01" min="0">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="serviceReward" class="form-label">Service Booking Reward ($)</label>
                                    <input type="number" class="form-control" id="serviceReward" name="serviceReward" value="<?= $settings['service_booking'] ?? '15.00' ?>" step="0.01" min="0">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="autoApprove" name="autoApprove" <?= ($settings['auto_approve'] ?? false) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="autoApprove">Auto-approve rewards</label>
                                    </div>
                                </div>
                                <div class="col-md-6 text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Save Settings
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Pending Rewards Table -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-hourglass me-1"></i>
                        Pending Reward Approvals
                    </div>
                    <div class="card-body">
                        <?php if (isset($pendingRewards) && count($pendingRewards)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Referrer</th>
                                        <th>Referred User</th>
                                        <th>Action Type</th>
                                        <th>Amount</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingRewards as $reward): ?>
                                    <tr>
                                        <td><?= date('M d, Y', strtotime($reward->created_at)) ?></td>
                                        <td>
                                            <?= $reward->user->name ?? 'Unknown' ?>
                                            <br><small class="text-muted"><?= $reward->user->email ?? '' ?></small>
                                        </td>
                                        <td>
                                            <?= $reward->referredUser->name ?? 'Unknown' ?>
                                            <br><small class="text-muted"><?= $reward->referredUser->email ?? '' ?></small>
                                        </td>
                                        <td><?= ucfirst(str_replace('_', ' ', $reward->action_type)) ?></td>
                                        <td>$<?= number_format($reward->reward_amount, 2) ?></td>
                                        <td>
                                            <button onclick="approveReward(<?= $reward->id ?>)" class="btn btn-sm btn-success me-1">
                                                <i class="bi bi-check-lg"></i> Approve
                                            </button>
                                            <button onclick="rejectReward(<?= $reward->id ?>)" class="btn btn-sm btn-danger">
                                                <i class="bi bi-x-lg"></i> Reject
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                            <p class="text-center py-3">No pending rewards to approve.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Top Referrers Table -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-trophy me-1"></i>
                        Top Referrers
                    </div>
                    <div class="card-body">
                        <?php if (isset($topReferrers) && count($topReferrers)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>User</th>
                                        <th>Total Referrals</th>
                                        <th>Total Rewards</th>
                                        <th>Last Referral</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topReferrers as $index => $referrer): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <a href="/admin/users/view/<?= $referrer->id ?>"><?= $referrer->name ?></a>
                                            <br><small class="text-muted"><?= $referrer->email ?></small>
                                        </td>
                                        <td><?= $referrer->referral_count ?></td>
                                        <td>$<?= number_format($referrer->total_rewards, 2) ?></td>
                                        <td><?= date('M d, Y', strtotime($referrer->last_referral)) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                            <p class="text-center py-3">No referral data to display.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Approve reward function
        function approveReward(rewardId) {
            if (confirm('Are you sure you want to approve this reward?')) {
                fetch('/admin/referrals/approve/' + rewardId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Reward approved successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        }
        
        // Reject reward function
        function rejectReward(rewardId) {
            if (confirm('Are you sure you want to reject this reward?')) {
                fetch('/admin/referrals/reject/' + rewardId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Reward rejected successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        }
        
        // Export data function
        document.getElementById('exportBtn').addEventListener('click', function() {
            window.location.href = '/admin/referrals/export';
        });
    </script>
</body>
</html>
