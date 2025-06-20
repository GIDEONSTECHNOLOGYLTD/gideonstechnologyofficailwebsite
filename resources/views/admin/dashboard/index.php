<?php $this->layout('admin/layouts/main', ['title' => 'Dashboard', 'active_menu' => 'dashboard']) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Dashboard</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active" aria-current="page">Home</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex align-items-center">
        <div class="dropdown me-3">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="dashboardFilter" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-calendar-alt me-1"></i> This Week
            </button>
            <ul class="dropdown-menu" aria-labelledby="dashboardFilter">
                <li><a class="dropdown-item active" href="#" data-period="week">This Week</a></li>
                <li><a class="dropdown-item" href="#" data-period="month">This Month</a></li>
                <li><a class="dropdown-item" href="#" data-period="year">This Year</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" data-period="all">All Time</a></li>
            </ul>
        </div>
        <span class="badge bg-light text-dark"><?= date('l, F j, Y') ?></span>
    </div>
</div>

<!-- Welcome Card -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="card-title mb-1">Welcome back, <?= $this->e($auth->user->name) ?>! 👋</h5>
                <p class="text-muted mb-0">Here's what's happening with your store today.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <button class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> New Order
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Total Revenue -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                        <i class="fas fa-dollar-sign fs-4"></i>
                    </div>
                    <span class="badge bg-soft-success text-success">
                        <i class="fas fa-arrow-up me-1"></i> 12%
                    </span>
                </div>
                <h3 class="mb-1">$<?= number_format($stats['total_revenue'] ?? 0, 2) ?></h3>
                <p class="text-muted mb-0">Total Revenue</p>
                <div class="mt-3">
                    <span class="text-muted small">
                        <i class="fas fa-calendar-day me-1"></i> Today: 
                        <strong>$<?= number_format($stats['today_revenue'] ?? 0, 2) ?></strong>
                    </span>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pt-0">
                <a href="/admin/reports" class="text-primary text-decoration-none small">
                    View reports <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-3">
                        <i class="fas fa-shopping-cart fs-4"></i>
                    </div>
                    <span class="badge bg-soft-success text-success">
                        <i class="fas fa-arrow-up me-1"></i> 24%
                    </span>
                </div>
                <h3 class="mb-1"><?= number_format($stats['total_orders'] ?? 0) ?></h3>
                <p class="text-muted mb-0">Total Orders</p>
                <div class="mt-3">
                    <span class="text-muted small">
                        <i class="fas fa-calendar-day me-1"></i> Today: 
                        <strong><?= $stats['today_orders'] ?? 0 ?></strong>
                    </span>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pt-0">
                <a href="/admin/orders" class="text-success text-decoration-none small">
                    View all orders <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Total Users -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-shape bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                        <i class="fas fa-users fs-4"></i>
                    </div>
                    <span class="badge bg-soft-<?= ($stats['user_growth'] ?? 0) >= 0 ? 'success' : 'danger' ?> text-<?= ($stats['user_growth'] ?? 0) >= 0 ? 'success' : 'danger' ?>">
                        <i class="fas fa-<?= ($stats['user_growth'] ?? 0) >= 0 ? 'arrow-up' : 'arrow-down' ?> me-1"></i> 
                        <?= abs($stats['user_growth'] ?? 0) ?>%
                    </span>
                </div>
                <h3 class="mb-1"><?= number_format($stats['total_users'] ?? 0) ?></h3>
                <p class="text-muted mb-0">Total Users</p>
                <div class="mt-3">
                    <span class="text-muted small">
                        <i class="fas fa-calendar-week me-1"></i> This week: 
                        <strong>+<?= $stats['new_users_this_week'] ?? 0 ?></strong>
                    </span>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pt-0">
                <a href="/admin/users" class="text-warning text-decoration-none small">
                    View all users <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Products -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="icon-shape bg-info bg-opacity-10 text-info rounded-3 p-3">
                        <i class="fas fa-boxes fs-4"></i>
                    </div>
                    <div>
                        <?php if (($stats['low_stock_products'] ?? 0) > 0): ?>
                            <span class="badge bg-soft-danger text-danger" data-bs-toggle="tooltip" title="Low stock items">
                                <i class="fas fa-exclamation-triangle me-1"></i> <?= $stats['low_stock_products'] ?? 0 ?>
                            </span>
                        <?php else: ?>
                            <span class="badge bg-soft-success text-success">
                                <i class="fas fa-check-circle me-1"></i> In Stock
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <h3 class="mb-1"><?= number_format($stats['total_products'] ?? 0) ?></h3>
                <p class="text-muted mb-0">Total Products</p>
                <div class="mt-3">
                    <span class="text-muted small">
                        <?php if (($stats['out_of_stock_products'] ?? 0) > 0): ?>
                            <span class="text-danger">
                                <i class="fas fa-times-circle me-1"></i> 
                                <?= $stats['out_of_stock_products'] ?> out of stock
                            </span>
                        <?php else: ?>
                            <span class="text-success">
                                <i class="fas fa-check-circle me-1"></i> All in stock
                            </span>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pt-0">
                <a href="/admin/products" class="text-info text-decoration-none small">
                    Manage products <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Chart -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 pb-0">
        <h5 class="mb-0">Revenue Overview</h5>
        <div class="btn-group btn-group-sm" role="group" aria-label="Revenue period">
            <button type="button" class="btn btn-outline-secondary active" data-period="week">Week</button>
            <button type="button" class="btn btn-outline-secondary" data-period="month">Month</button>
            <button type="button" class="btn btn-outline-secondary" data-period="year">Year</button>
        </div>
    </div>
    <div class="card-body pt-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="mb-0">$<?= number_format($stats['total_revenue'] ?? 0, 2) ?></h3>
                <span class="text-muted small">Total Revenue</span>
            </div>
            <div class="text-end">
                <div class="d-flex align-items-center justify-content-end">
                    <span class="badge bg-soft-success text-success p-1 me-2">
                        <i class="fas fa-arrow-up"></i> 12.5%
                    </span>
                    <span class="text-muted small">vs last period</span>
                </div>
            </div>
        </div>
        <div id="revenue-chart" style="min-height: 300px;"></div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Orders -->
    <div class="col-lg-8">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 pb-0">
                <h5 class="mb-0">Recent Orders</h5>
                <div>
                    <a href="/admin/orders" class="btn btn-sm btn-outline-secondary me-2">
                        <i class="fas fa-list me-1"></i> View All
                    </a>
                    <a href="/admin/orders/create" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i> New Order
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-top-0 ps-4">Order #</th>
                                <th class="border-top-0">Customer</th>
                                <th class="border-top-0">Date</th>
                                <th class="border-top-0 text-end">Amount</th>
                                <th class="border-top-0">Status</th>
                                <th class="border-top-0 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_orders) && count($recent_orders) > 0): ?>
                                <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <a href="/admin/orders/<?= $order['id'] ?>" class="text-primary fw-medium d-inline-flex align-items-center">
                                                <span class="me-2">#<?= $this->e($order['order_number']) ?></span>
                                                <?php if (strtotime($order['created_at']) > strtotime('-1 day')): ?>
                                                    <span class="badge bg-soft-primary text-primary rounded-pill small">New</span>
                                                <?php endif; ?>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <div class="avatar-initial bg-light rounded-circle text-dark fw-medium">
                                                        <?= strtoupper(substr($order['customer_name'], 0, 1)) ?>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0"><?= $this->e($order['customer_name']) ?></h6>
                                                    <small class="text-muted"><?= $this->e($order['customer_email']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-muted small"><?= date('M j, Y', strtotime($order['created_at'])) ?></span>
                                                <small class="text-muted"><?= date('h:i A', strtotime($order['created_at'])) ?></small>
                                            </div>
                                        </td>
                                        <td class="text-end fw-medium">$<?= number_format($order['total_amount'], 2) ?></td>
                                        <td>
                                            <span class="badge rounded-pill bg-<?= $this->e($order['status_class']) ?>-subtle text-<?= $this->e($order['status_class']) ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= date('M j, Y h:i A', strtotime($order['updated_at'])) ?>">
                                                <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                                <?= ucfirst($this->e($order['status'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-light rounded-circle" type="button" id="orderActions<?= $order['id'] ?>" data-bs-toggle="dropdown" aria-expanded="false" data-bs-offset="0,10">
                                                    <i class="fas fa-ellipsis-v text-muted"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="orderActions<?= $order['id'] ?>">
                                                    <li>
                                                        <a class="dropdown-item" href="/admin/orders/<?= $order['id'] ?>">
                                                            <i class="fas fa-eye fa-fw me-2 text-primary"></i> View Details
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="/admin/orders/<?= $order['id'] ?>/invoice" target="_blank">
                                                            <i class="fas fa-file-invoice fa-fw me-2 text-success"></i> Invoice
                                                        </a>
                                                    </li>
                                                    <?php if ($order['status'] === 'processing' || $order['status'] === 'shipped'): ?>
                                                    <li>
                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#trackOrderModal" data-order-id="<?= $order['id'] ?>">
                                                            <i class="fas fa-truck fa-fw me-2 text-info"></i> Track Order
                                                        </a>
                                                    </li>
                                                    <?php endif; ?>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#cancelOrderModal" data-order-id="<?= $order['id'] ?>">
                                                            <i class="fas fa-times-circle fa-fw me-2"></i> Cancel Order
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="py-4">
                                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No recent orders</h5>
                                            <p class="text-muted small mb-4">New orders will appear here as they come in</p>
                                            <a href="/admin/orders/create" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i> Create New Order
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if (!empty($recent_orders) && count($recent_orders) > 0): ?>
            <div class="card-footer bg-white border-0 pt-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing <span class="fw-medium">1-<?= count($recent_orders) ?></span> of <span class="fw-medium"><?= $stats['total_orders'] ?? 0 ?></span> orders
                    </div>
                    <a href="/admin/orders" class="btn btn-sm btn-link">
                        View all orders <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Quick Stats & Recent Activity -->
    <div class="col-lg-4">
        <!-- Quick Stats -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Quick Stats</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Today's Orders</h6>
                                <small class="text-muted"><?= $stats['today_orders'] ?? 0 ?> orders</small>
                            </div>
                        </div>
                        <span class="badge bg-primary rounded-pill"><?= number_format($stats['today_revenue'] ?? 0, 2) ?>$</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2 me-3">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">New Customers</h6>
                                <small class="text-muted"><?= $stats['new_customers'] ?? 0 ?> this week</small>
                            </div>
                        </div>
                        <span class="badge bg-success rounded-pill">+<?= $stats['customer_growth'] ?? 0 ?>%</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="icon-shape bg-warning bg-opacity-10 text-warning rounded-3 p-2 me-3">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Conversion Rate</h6>
                                <small class="text-muted">From <?= $stats['total_visitors'] ?? 0 ?> visitors</small>
                            </div>
                        </div>
                        <span class="badge bg-warning rounded-pill"><?= number_format($stats['conversion_rate'] ?? 0, 1) ?>%</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="icon-shape bg-info bg-opacity-10 text-info rounded-3 p-2 me-3">
                                <i class="fas fa-box"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Low Stock Items</h6>
                                <small class="text-muted">Need attention</small>
                            </div>
                        </div>
                        <span class="badge bg-danger rounded-pill"><?= $stats['low_stock_items'] ?? 0 ?> items</span>
                                        <?php if ($product['stock'] > 0 && $product['stock'] <= 10): ?>
                                            <div class="progress mt-2" style="height: 6px;">
                                                <div class="progress-bar bg-warning" role="progressbar" 
                                                     style="width: <?= min(($product['stock'] / $product['initial_stock']) * 100, 100) ?>%" 
                                                     aria-valuenow="<?= $product['stock'] ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="<?= $product['initial_stock'] ?>">
                                                </div>
                                            </div>
                                            <small class="text-warning">Low stock (<?= $product['stock'] ?> left)</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-3 text-muted">
                            No popular products to display
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->push('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
// Format currency
const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value);
};

// Format number
const formatNumber = (value) => {
    return new Intl.NumberFormat().format(value);
};

// Initialize tooltips and popovers
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"], [data-bs-toggle="popover"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

// Revenue Chart
let revenueChart;

function initRevenueChart(data, categories) {
    const options = {
        series: [{
            name: 'Revenue',
            data: data
        }],
        chart: {
            type: 'area',
            height: 300,
            zoom: { enabled: false },
            toolbar: { show: false },
            fontFamily: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: {
                    enabled: true,
                    delay: 150
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 350
                }
            },
            dropShadow: {
                enabled: true,
                color: '#000',
                top: 10,
                left: 7,
                blur: 10,
                opacity: 0.1
            },
            foreColor: '#6c757d'
        },
        colors: ['#5e72e4'],
        dataLabels: { enabled: false },
        stroke: {
            curve: 'smooth',
            width: 3,
            lineCap: 'round'
        },
        xaxis: {
            categories: categories,
            labels: {
                style: {
                    colors: '#6c757d',
                    fontSize: '11px',
                    fontFamily: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                    cssClass: 'text-muted small'
                }
            },
            axisBorder: { show: false },
            axisTicks: { show: false },
            tooltip: { enabled: false }
        },
        yaxis: {
            labels: {
                formatter: function(value) {
                    return '$' + formatNumber(value);
                },
                style: {
                    colors: ['#6c757d'],
                    fontSize: '11px',
                    fontFamily: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                    cssClass: 'text-muted small'
                }
            },
            min: 0
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.2,
                stops: [0, 90, 100]
            }
        },
        grid: {
            borderColor: 'rgba(0, 0, 0, 0.05)',
            strokeDashArray: 4,
            xaxis: { lines: { show: true } },
            yaxis: { lines: { show: true } },
            padding: { top: 0, right: 0, bottom: 0, left: 0 }
        },
        tooltip: {
            enabled: true,
            x: { show: false },
            y: {
                formatter: function(value) {
                    return formatCurrency(value);
                },
                title: {
                    formatter: function() {
                        return '';
                    }
                }
            },
            marker: { show: false },
            style: { fontSize: '12px' },
            fixed: {
                enabled: false,
                position: 'topRight',
                offsetX: 0,
                offsetY: 0,
            }
        },
        states: {
            hover: { filter: { type: 'none' } },
            active: { filter: { type: 'none' } }
        },
        markers: {
            size: 4,
            colors: ['#5e72e4'],
            strokeColors: '#fff',
            strokeWidth: 2,
            hover: { size: 5 }
        }
    };

    // Destroy existing chart if it exists
    if (revenueChart) {
        revenueChart.destroy();
    }

    // Create new chart
    revenueChart = new ApexCharts(document.querySelector("#revenue-chart"), options);
    revenueChart.render();
}

// Initialize chart with data from PHP
const revenueData = [<?= implode(',', array_column($weekly_revenue, 'revenue')) ?>];
const revenueCategories = [<?= '"' . implode('", "', array_column($weekly_revenue, 'day')) . '"' ?>];
initRevenueChart(revenueData, revenueCategories);

// Handle period filter buttons
document.querySelectorAll('[data-period]').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Update active state
        document.querySelectorAll('[data-period]').forEach(btn => {
            btn.classList.remove('active');
        });
        this.classList.add('active');
        
        // Update button text
        const periodText = this.textContent.trim();
        const filterBtn = document.querySelector('#dashboardFilter .dropdown-toggle');
        const icon = filterBtn.querySelector('i');
        filterBtn.innerHTML = icon.outerHTML + ' ' + periodText;
        
        // Simulate API call to get new data
        const period = this.getAttribute('data-period');
        fetch(`/admin/api/dashboard/revenue?period=${period}`)
            .then(response => response.json())
            .then(data => {
                // Update chart with new data
                revenueChart.updateOptions({
                    xaxis: { categories: data.categories },
                    series: [{ data: data.series }]
                });
                
                // Update stats
                if (data.stats) {
                    document.querySelector('[data-total-revenue]').textContent = formatCurrency(data.stats.total_revenue);
                    document.querySelector('[data-today-revenue]').textContent = formatCurrency(data.stats.today_revenue);
                    
                    // Update trend indicator
                    const trendEl = document.querySelector('[data-revenue-trend]');
                    trendEl.className = `badge rounded-pill bg-${data.stats.trend >= 0 ? 'success' : 'danger'}-subtle text-${data.stats.trend >= 0 ? 'success' : 'danger'}`;
                    trendEl.innerHTML = `
                        <i class="fas fa-${data.stats.trend >= 0 ? 'arrow-up' : 'arrow-down'} me-1"></i>
                        ${Math.abs(data.stats.trend)}%
                    `;
                }
            })
            .catch(error => {
                console.error('Error fetching revenue data:', error);
            });
    });
});

// Handle activity filter
document.querySelectorAll('[data-filter]').forEach(item => {
    item.addEventListener('click', function(e) {
        e.preventDefault();
        const filter = this.getAttribute('data-filter');
        
        // Update active state
        document.querySelectorAll('[data-filter]').forEach(btn => {
            btn.classList.remove('active');
        });
        this.classList.add('active');
        
        // Filter activities
        const activities = document.querySelectorAll('.activity-item');
        activities.forEach(activity => {
            if (filter === 'all' || activity.getAttribute('data-type') === filter) {
                activity.style.display = '';
            } else {
                activity.style.display = 'none';
            }
        });
    });
});

// Initialize any other interactive components
$(document).ready(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Initialize popovers
    $('[data-bs-toggle="popover"]').popover({
        trigger: 'hover',
        html: true,
        container: 'body'
    });
    
    // Handle dropdown menus
    $('.dropdown-menu a.dropdown-item').on('click', function(e) {
        e.stopPropagation();
    });
    
    // Handle sidebar toggle
    $('[data-bs-toggle="offcanvas"]').on('click', function() {
        $('.sidebar').toggleClass('show');
    });
    
    // Handle theme toggle
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-theme');
            const isDark = document.body.classList.contains('dark-theme');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            
            // Update icon
            const icon = this.querySelector('i');
            icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
        });
        
        // Check for saved theme preference
        if (localStorage.getItem('theme') === 'dark' || 
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.body.classList.add('dark-theme');
            const icon = themeToggle.querySelector('i');
            if (icon) icon.className = 'fas fa-sun';
        }
    }
});

// Handle window resize
function debounce(func, wait, immediate) {
    var timeout;
    return function() {
        var context = this, args = arguments;
        var later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

// Handle responsive behavior
window.addEventListener('resize', debounce(function() {
    if (revenueChart) {
        revenueChart.updateOptions({
            chart: {
                height: window.innerWidth < 768 ? 250 : 300
            }
        });
    }
}, 250));

// Initialize any charts or other components that depend on window size
if (revenueChart) {
    revenueChart.updateOptions({
        chart: {
            height: window.innerWidth < 768 ? 250 : 300
        }
    });
}
</script>
<?php $this->end() ?>
