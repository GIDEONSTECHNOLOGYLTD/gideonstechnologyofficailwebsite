<?php require APP_PATH . '/views/layouts/header.php'; ?>

<div class="coupons-container">
    <div class="container">
        <div class="coupons-header">
            <h1>Available Coupons</h1>
            <p>Save money with our special offers</p>
        </div>

        <div class="coupons-filters">
            <div class="row">
                <div class="col-md-4">
                    <select class="form-select" id="categoryFilter">
                        <option value="all">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['id']); ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="typeFilter">
                        <option value="all">All Types</option>
                        <option value="percentage">Percentage Discount</option>
                        <option value="fixed">Fixed Amount Discount</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="statusFilter">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="coupons-grid">
            <?php foreach ($coupons as $coupon): ?>
            <div class="coupon-card" data-category="<?php echo htmlspecialchars($coupon['category_id']); ?>"
                 data-type="<?php echo htmlspecialchars($coupon['type']); ?>"
                 data-status="<?php echo $coupon['status'] === 'active' ? 'active' : 'expired'; ?>">
                <div class="coupon-header">
                    <div class="coupon-code">
                        <span class="code-text"><?php echo htmlspecialchars($coupon['code']); ?></span>
                        <button class="btn btn-sm btn-outline-primary" onclick="copyCode('<?php echo htmlspecialchars($coupon['code']); ?>')">
                            <i class="fa fa-copy"></i> Copy Code
                        </button>
                    </div>
                    <div class="coupon-status <?php echo $coupon['status']; ?>">
                        <?php echo ucfirst($coupon['status']); ?>
                    </div>
                </div>
                
                <div class="coupon-details">
                    <div class="coupon-discount">
                        <?php if ($coupon['type'] === 'percentage'): ?>
                        <h3><?php echo htmlspecialchars($coupon['discount']); ?>% Off</h3>
                        <?php else: ?>
                        <h3>$<?php echo number_format($coupon['discount'], 2); ?> Off</h3>
                        <?php endif; ?>
                    </div>
                    
                    <div class="coupon-meta">
                        <div class="meta-item">
                            <i class="fa fa-calendar"></i>
                            <span>Valid until <?php echo date('M d, Y', strtotime($coupon['expiry_date'])); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fa fa-shopping-cart"></i>
                            <span>Minimum purchase: $<?php echo number_format($coupon['minimum_purchase'], 2); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fa fa-user"></i>
                            <span>Uses remaining: <?php echo htmlspecialchars($coupon['remaining_uses']); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="coupon-actions">
                    <button class="btn btn-primary" onclick="applyCoupon('<?php echo htmlspecialchars($coupon['code']); ?>')">
                        <i class="fa fa-tag"></i> Apply Coupon
                    </button>
                    <?php if ($coupon['status'] === 'active'): ?>
                    <button class="btn btn-outline-danger" onclick="redeemCoupon('<?php echo htmlspecialchars($coupon['id']); ?>')">
                        <i class="fa fa-check"></i> Redeem
                    </button>
                    <?php endif; ?>
                </div>
                
                <div class="coupon-terms">
                    <h4>Terms & Conditions</h4>
                    <ul>
                        <?php foreach ($coupon['terms'] as $term): ?>
                        <li><?php echo htmlspecialchars($term); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="my-coupons">
            <h2>My Active Coupons</h2>
            <div class="coupons-list">
                <?php foreach ($userCoupons as $coupon): ?>
                <div class="coupon-item">
                    <div class="coupon-info">
                        <span class="coupon-code"><?php echo htmlspecialchars($coupon['code']); ?></span>
                        <span class="coupon-discount">
                            <?php if ($coupon['type'] === 'percentage'): ?>
                            <?php echo htmlspecialchars($coupon['discount']); ?>% Off
                            <?php else: ?>
                            $<?php echo number_format($coupon['discount'], 2); ?> Off
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="coupon-meta">
                        <span class="expiry-date">
                            Expires <?php echo date('M d, Y', strtotime($coupon['expiry_date'])); ?>
                        </span>
                        <span class="remaining-uses">
                            <?php echo htmlspecialchars($coupon['remaining_uses']); ?> uses left
                        </span>
                    </div>
                    <div class="coupon-actions">
                        <button class="btn btn-sm btn-outline-danger" 
                                onclick="removeCoupon('<?php echo htmlspecialchars($coupon['id']); ?>')">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
.coupons-container {
    padding: 40px 0;
}

.coupons-header {
    text-align: center;
    margin-bottom: 50px;
}

.coupons-header h1 {
    font-size: 2.5em;
    color: #2c3e50;
    margin-bottom: 15px;
}

.coupons-filters {
    margin-bottom: 40px;
}

.coupons-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.coupon-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
}

.coupon-card:hover {
    transform: translateY(-5px);
}

.coupon-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.coupon-code {
    display: flex;
    align-items: center;
    gap: 10px;
}

.code-text {
    font-size: 1.2em;
    font-weight: bold;
    color: #3498db;
    text-transform: uppercase;
}

.coupon-status {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.9em;
}

.coupon-status.active {
    background: #e6f7ff;
    color: #1890ff;
}

.coupon-status.expired {
    background: #ffebee;
    color: #f5222d;
}

.coupon-discount {
    text-align: center;
    margin-bottom: 20px;
}

.coupon-discount h3 {
    color: #e74c3c;
    font-size: 2em;
    margin: 0;
}

.coupon-meta {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 20px;
    color: #666;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.coupon-actions {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.coupon-terms {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
}

.coupon-terms h4 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.coupon-terms ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.coupon-terms li {
    color: #666;
    margin-bottom: 5px;
}

.my-coupons {
    background: white;
    border-radius: 10px;
    padding: 30px;
    margin-top: 50px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.my-coupons h2 {
    color: #2c3e50;
    margin-bottom: 20px;
}

.coupons-list {
    display: grid;
    gap: 15px;
}

.coupon-item {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    align-items: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 5px;
}

@media (max-width: 768px) {
    .coupons-container {
        padding: 20px 0;
    }
    
    .coupons-filters {
        flex-direction: column;
        gap: 15px;
    }
    
    .coupons-grid {
        grid-template-columns: 1fr;
    }
    
    .coupon-item {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .coupon-actions {
        justify-content: center;
    }
}
</style>

<script>
function copyCode(code) {
    navigator.clipboard.writeText(code)
        .then(() => {
            alert('Coupon code copied to clipboard');
        })
        .catch(err => {
            console.error('Failed to copy:', err);
            alert('Failed to copy coupon code');
        });
}

function applyCoupon(code) {
    fetch('/gstore/apply-coupon', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ code: code })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Coupon applied successfully');
            updateCartTotal(data.discount);
        } else {
            alert(data.message || 'Error applying coupon');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error applying coupon');
    });
}

function redeemCoupon(couponId) {
    if (confirm('Are you sure you want to redeem this coupon?')) {
        fetch(`/gstore/redeem-coupon/${couponId}`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error redeeming coupon');
        });
    }
}

function removeCoupon(couponId) {
    if (confirm('Are you sure you want to remove this coupon?')) {
        fetch(`/gstore/remove-coupon/${couponId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error removing coupon');
        });
    }
}

function updateCartTotal(discount) {
    const totalElement = document.getElementById('cart-total');
    if (totalElement) {
        const currentTotal = parseFloat(totalElement.textContent.replace('$', ''));
        const newTotal = currentTotal - discount;
        totalElement.textContent = `$${newTotal.toFixed(2)}`;
    }
}

// Add filter functionality
const categoryFilter = document.getElementById('categoryFilter');
const typeFilter = document.getElementById('typeFilter');
const statusFilter = document.getElementById('statusFilter');
const couponCards = document.querySelectorAll('.coupon-card');

function applyFilters() {
    const category = categoryFilter.value;
    const type = typeFilter.value;
    const status = statusFilter.value;
    
    couponCards.forEach(card => {
        const categoryMatch = category === 'all' || card.dataset.category === category;
        const typeMatch = type === 'all' || card.dataset.type === type;
        const statusMatch = status === 'all' || card.dataset.status === status;
        
        card.style.display = categoryMatch && typeMatch && statusMatch ? 'grid' : 'none';
    });
}

[categoryFilter, typeFilter, statusFilter].forEach(filter => {
    filter.addEventListener('change', applyFilters);
});
</script>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
