<?php
require_once __DIR__ . '/../../app/bootstrap.php';

// Add billing helper functions
function getUpcomingPayments($userId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT i.*, s.name as service_name FROM invoices i JOIN services s ON i.service_id = s.id WHERE i.user_id = ? AND i.status = 'pending' ORDER BY i.due_date ASC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching upcoming payments: " . $e->getMessage());
        return [];
    }
}

function getBillingHistory($userId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT o.*, s.name as service_name FROM orders o LEFT JOIN services s ON o.service_id = s.id WHERE o.user_id = ? ORDER BY o.created_at DESC LIMIT 5");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching billing history: " . $e->getMessage());
        return [];
    }
}

// Get billing history
$billingHistory = getBillingHistory($_SESSION['user_id']);

// Get saved payment methods
$paymentMethods = [];
try {
    $stmt = $pdo->prepare("
        SELECT * FROM payment_methods
        WHERE user_id = ?
        ORDER BY is_default DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $paymentMethods = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching payment methods: " . $e->getMessage());
}
?>

<!-- Payment Methods -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h3 class="h6 mb-0">Payment Methods</h3>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentMethod">
                    <i class="fas fa-plus me-1"></i> Add New
                </button>
            </div>
            <div class="card-body">
                <?php if (empty($paymentMethods)): ?>
                <p class="text-muted">No payment methods added yet.</p>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($paymentMethods as $method): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-<?php echo $method->type === 'card' ? 'credit-card' : 'university'; ?> me-2"></i>
                            <?php if ($method->type === 'card'): ?>
                            •••• •••• •••• <?php echo $method->last4; ?>
                            <small class="text-muted ms-2">Expires <?php echo $method->exp_month; ?>/<?php echo $method->exp_year; ?></small>
                            <?php else: ?>
                            <?php echo htmlspecialchars($method->bank_name); ?>
                            <small class="text-muted ms-2">••••<?php echo $method->last4; ?></small>
                            <?php endif; ?>
                            <?php if ($method->is_default): ?>
                            <span class="badge bg-primary ms-2">Default</span>
                            <?php endif; ?>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary" onclick="setDefaultPayment(<?php echo $method->id; ?>)">
                                Set Default
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="removePayment(<?php echo $method->id; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h3 class="h6 mb-0">Billing Address</h3>
            </div>
            <div class="card-body">
                <form id="billingAddressForm">
                    <div class="mb-3">
                        <label class="form-label">Street Address</label>
                        <input type="text" class="form-control" name="street" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="city" required>
                        </div>
                        <div class="col">
                            <label class="form-label">State</label>
                            <input type="text" class="form-control" name="state" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">ZIP Code</label>
                            <input type="text" class="form-control" name="zip" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Country</label>
                            <select class="form-select" name="country" required>
                                <option value="US">United States</option>
                                <option value="CA">Canada</option>
                                <option value="GB">United Kingdom</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save Address</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Billing History -->
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h3 class="h6 mb-0">Billing History</h3>
    </div>
    <div class="card-body">
        <?php if (empty($billingHistory)): ?>
        <p class="text-muted">No billing history available.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Service</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Invoice</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($billingHistory as $item): ?>
                    <tr>
                        <td><?php echo date('M j, Y', strtotime($item->created_at)); ?></td>
                        <td><?php echo htmlspecialchars($item->service_name); ?></td>
                        <td>$<?php echo number_format($item->total_amount, 2); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $item->status === 'completed' ? 'success' : 
                                ($item->status === 'processing' ? 'warning' : 
                                ($item->status === 'cancelled' ? 'danger' : 'info')); ?>">
                                <?php echo ucfirst($item->status); ?>
                            </span>
                        </td>
                        <td>
                            <a href="/invoice/<?php echo $item->id; ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download me-1"></i> Invoice
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

<!-- Add Payment Method Modal -->
<div class="modal fade" id="addPaymentMethod" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Payment Method</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-pills nav-justified mb-3">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="pill" href="#cardForm">Credit Card</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="pill" href="#bankForm">Bank Account</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="cardForm">
                        <form id="creditCardForm">
                            <div class="mb-3">
                                <label class="form-label">Card Number</label>
                                <input type="text" class="form-control" required>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control" placeholder="MM/YY" required>
                                </div>
                                <div class="col">
                                    <label class="form-label">CVV</label>
                                    <input type="text" class="form-control" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Add Card</button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="bankForm">
                        <form id="bankAccountForm">
                            <div class="mb-3">
                                <label class="form-label">Account Holder Name</label>
                                <input type="text" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Routing Number</label>
                                <input type="text" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Account Number</label>
                                <input type="text" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Add Bank Account</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setDefaultPayment(id) {
    // Implementation for setting default payment method
}

function removePayment(id) {
    if (confirm('Are you sure you want to remove this payment method?')) {
        // Implementation for removing payment method
    }
}

document.getElementById('billingAddressForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // Implementation for saving billing address
});

document.getElementById('creditCardForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // Implementation for adding credit card
});

document.getElementById('bankAccountForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // Implementation for adding bank account
});
</script>

function generateInvoicePDF($invoiceId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT i.*, u.name as client_name, u.email as client_email,
                   s.name as service_name, s.description as service_description
            FROM invoices i
            JOIN users u ON i.user_id = u.id
            JOIN services s ON i.service_id = s.id
            WHERE i.id = ?
        ");
        $stmt->execute([$invoiceId]);
        $invoice = $stmt->fetch();
        
        if (!$invoice) {
            return false;
        }
        
        // TODO: Implement PDF generation using a library like TCPDF or FPDF
        // For now, we'll just return true
        return true;
    } catch (PDOException $e) {
        error_log("Error generating invoice PDF: " . $e->getMessage());
        return false;
    }
}

function processPayment($invoiceId, $amount, $paymentMethod) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Get invoice details
        $stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = ? FOR UPDATE");
        $stmt->execute([$invoiceId]);
        $invoice = $stmt->fetch();
        
        if (!$invoice || $invoice['status'] === 'paid') {
            $pdo->rollBack();
            return false;
        }
        
        // Record payment
        $stmt = $pdo->prepare("
            INSERT INTO payments (invoice_id, amount, method, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$invoiceId, $amount, $paymentMethod]);
        
        // Update invoice status
        $stmt = $pdo->prepare("
            UPDATE invoices 
            SET status = 'paid', paid_at = NOW(), updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$invoiceId]);
        
        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Error processing payment: " . $e->getMessage());
        return false;
    }
}

function getUpcomingPayments($userId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT i.*, s.name as service_name
            FROM invoices i
            JOIN services s ON i.service_id = s.id
            WHERE i.user_id = ? AND i.status = 'pending'
            ORDER BY i.due_date ASC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching upcoming payments: " . $e->getMessage());
        return [];
    }
}
?>
