<?php
/**
 * User Referral Dashboard Template
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referral Dashboard | <?= $appName ?? 'Gideons Technology' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <style>
        .referral-card {
            border-left: 4px solid #2563eb;
        }
        .social-share-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            color: white;
            margin-right: 10px;
            text-decoration: none;
            transition: all 0.3s;
        }
        .social-share-btn:hover {
            transform: scale(1.1);
            opacity: 0.9;
        }
        .facebook { background-color: #1877f2; }
        .twitter { background-color: #1da1f2; }
        .whatsapp { background-color: #25d366; }
        .linkedin { background-color: #0077b5; }
        .email { background-color: #6c757d; }
    </style>
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

    <!-- Referral Dashboard Content -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <!-- Sidebar -->
                <div class="list-group mb-4">
                    <a href="/dashboard" class="list-group-item list-group-item-action">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                    <a href="/profile" class="list-group-item list-group-item-action">
                        <i class="bi bi-person me-2"></i> My Profile
                    </a>
                    <a href="/orders" class="list-group-item list-group-item-action">
                        <i class="bi bi-bag me-2"></i> My Orders
                    </a>
                    <a href="/wishlist" class="list-group-item list-group-item-action">
                        <i class="bi bi-heart me-2"></i> Wishlist
                    </a>
                    <a href="/tickets" class="list-group-item list-group-item-action">
                        <i class="bi bi-ticket me-2"></i> Support Tickets
                    </a>
                    <a href="/referrals" class="list-group-item list-group-item-action active">
                        <i class="bi bi-people me-2"></i> My Referrals
                    </a>
                    <a href="/settings" class="list-group-item list-group-item-action">
                        <i class="bi bi-gear me-2"></i> Settings
                    </a>
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                        <h4 class="m-0">My Referrals Dashboard</h4>
                    </div>
                    <div class="card-body">
                        <!-- Referral Stats -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-light referral-card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Rewards</h5>
                                        <h2 class="text-primary">$<?= number_format($rewards['total'], 2) ?></h2>
                                        <p class="text-muted">Earned from referrals</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light referral-card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">Pending Rewards</h5>
                                        <h2 class="text-warning">$<?= number_format($rewards['pending'], 2) ?></h2>
                                        <p class="text-muted">Waiting approval</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light referral-card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Referrals</h5>
                                        <h2 class="text-success"><?= $rewards['count'] ?></h2>
                                        <p class="text-muted">People referred</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Referral Link -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Your Personalized Referral Link</h5>
                            </div>
                            <div class="card-body">
                                <p>Share this link with friends to earn rewards when they sign up or make purchases.</p>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" value="<?= $referralLink ?>" id="referralLink" readonly>
                                    <button class="btn btn-outline-primary" type="button" onclick="copyReferralLink()">
                                        <i class="bi bi-clipboard"></i> Copy
                                    </button>
                                </div>
                                <div class="mt-3">
                                    <p class="fw-bold">Share via social media:</p>
                                    <div>
                                        <a href="<?= $socialLinks['facebook'] ?>" target="_blank" class="social-share-btn facebook">
                                            <i class="bi bi-facebook"></i>
                                        </a>
                                        <a href="<?= $socialLinks['twitter'] ?>" target="_blank" class="social-share-btn twitter">
                                            <i class="bi bi-twitter"></i>
                                        </a>
                                        <a href="<?= $socialLinks['whatsapp'] ?>" target="_blank" class="social-share-btn whatsapp">
                                            <i class="bi bi-whatsapp"></i>
                                        </a>
                                        <a href="<?= $socialLinks['linkedin'] ?>" target="_blank" class="social-share-btn linkedin">
                                            <i class="bi bi-linkedin"></i>
                                        </a>
                                        <a href="<?= $socialLinks['email'] ?>" class="social-share-btn email">
                                            <i class="bi bi-envelope"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Referral Rewards Table -->
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Rewards History</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Referred User</th>
                                                <th>Action</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (isset($rewardHistory) && count($rewardHistory)): ?>
                                                <?php foreach ($rewardHistory as $reward): ?>
                                                <tr>
                                                    <td><?= date('M d, Y', strtotime($reward->created_at)) ?></td>
                                                    <td><?= $reward->referredUser->name ?? 'Unknown' ?></td>
                                                    <td><?= ucfirst(str_replace('_', ' ', $reward->action_type)) ?></td>
                                                    <td>$<?= number_format($reward->reward_amount, 2) ?></td>
                                                    <td>
                                                        <?php if ($reward->status == 'approved'): ?>
                                                            <span class="badge bg-success">Approved</span>
                                                        <?php elseif ($reward->status == 'pending'): ?>
                                                            <span class="badge bg-warning text-dark">Pending</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Declined</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">No rewards history yet.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5>Gideons Technology</h5>
                    <p class="mb-0">Premium technology services and products for individuals and businesses.</p>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="/about" class="text-white">About Us</a></li>
                        <li><a href="/services" class="text-white">Services</a></li>
                        <li><a href="/store" class="text-white">Store</a></li>
                        <li><a href="/contact" class="text-white">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-geo-alt me-2"></i> Lagos, Nigeria</li>
                        <li><i class="bi bi-envelope me-2"></i> info@gideonstechnology.com</li>
                        <li><i class="bi bi-phone me-2"></i> +234 123 456 7890</li>
                    </ul>
                </div>
            </div>
            <hr class="my-3 bg-light">
            <div class="text-center">
                <p class="mb-0">&copy; <?= date('Y') ?> Gideons Technology. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript Resources -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function copyReferralLink() {
        var copyText = document.getElementById("referralLink");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        
        // Show a temporary tooltip
        var btn = document.querySelector('button[onclick="copyReferralLink()"]');
        var originalText = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check2"></i> Copied!';
        setTimeout(function() {
            btn.innerHTML = originalText;
        }, 2000);
    }
    </script>
</body>
</html>
