<?php
/**
 * Order detail template for Gideons Technology
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $appName ?? 'Gideons Technology' ?> - Order Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/index.php"><?= $appName ?? 'Gideons Technology' ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/index.php">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Services</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/services/web-development/applications">Web Applications</a></li>
                            <li><a class="dropdown-item" href="/services/web-development/ecommerce">E-commerce</a></li>
                            <li><a class="dropdown-item" href="/services/web-development/design">Web Design</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gstore">Store</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/order">Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Order Details -->
    <section class="py-5">
        <div class="container">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/order">My Orders</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Order #<?= htmlspecialchars($order['order_number']) ?></li>
                </ol>
            </nav>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Order Details</h5>
                            <span class="badge 
                                <?php if ($order['status'] == 'Completed'): ?>
                                    bg-success
                                <?php elseif ($order['status'] == 'Processing'): ?>
                                    bg-info
                                <?php else: ?>
                                    bg-warning text-dark
                                <?php endif; ?>
                            ">
                                <?= htmlspecialchars($order['status']) ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Order Number:</strong></p>
                                    <p><?= htmlspecialchars($order['order_number']) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Order Date:</strong></p>
                                    <p><?= htmlspecialchars($order['date']) ?></p>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <h6>Items</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Item</th>
                                                <th>Description</th>
                                                <th class="text-end">Price</th>
                                                <th class="text-center">Quantity</th>
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($order['items'] as $item): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($item['name']) ?></td>
                                                    <td><?= htmlspecialchars($item['description'] ?? 'N/A') ?></td>
                                                    <td class="text-end">$<?= number_format($item['price'], 2) ?></td>
                                                    <td class="text-center"><?= $item['quantity'] ?></td>
                                                    <td class="text-end">$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="4" class="text-end">Total:</th>
                                                <th class="text-end">$<?= number_format($order['total'], 2) ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Payment Information</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-1"><strong>Payment Method:</strong></p>
                            <p><?= htmlspecialchars($order['payment_method'] ?? 'N/A') ?></p>
                            
                            <p class="mb-1"><strong>Total Amount:</strong></p>
                            <p class="fs-5 fw-bold">$<?= number_format($order['total'], 2) ?></p>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Shipping Address</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($order['shipping_address'])): ?>
                                <p><?= htmlspecialchars($order['shipping_address']['name']) ?></p>
                                <p><?= htmlspecialchars($order['shipping_address']['address']) ?></p>
                                <p>
                                    <?= htmlspecialchars($order['shipping_address']['city']) ?>, 
                                    <?= htmlspecialchars($order['shipping_address']['state']) ?> 
                                    <?= htmlspecialchars($order['shipping_address']['zip']) ?>
                                </p>
                                <p><?= htmlspecialchars($order['shipping_address']['country']) ?></p>
                            <?php else: ?>
                                <p>No shipping address available.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <a href="/order" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i> Back to Orders
                </a>
                
                <a href="#" class="btn btn-primary ms-2">
                    <i class="bi bi-printer me-2"></i> Print Order
                </a>
                
                <a href="/contact" class="btn btn-outline-primary ms-2">
                    <i class="bi bi-question-circle me-2"></i> Need Help?
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?= $currentYear ?? date('Y') ?> <?= $appName ?? 'Gideons Technology' ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="/index.php" class="text-white me-3">Home</a>
                    <a href="/contact" class="text-white me-3">Contact</a>
                    <a href="/privacy" class="text-white">Privacy Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>