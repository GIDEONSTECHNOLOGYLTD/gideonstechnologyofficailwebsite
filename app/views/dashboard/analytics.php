<?php require APP_PATH . '/views/layouts/header.php'; ?>

<div class="analytics-dashboard">
    <div class="container">
        <div class="dashboard-header">
            <h1>Analytics Dashboard</h1>
            <div class="dashboard-filters">
                <div class="date-range-picker">
                    <label for="dateRange">Date Range:</label>
                    <input type="text" id="dateRange" class="form-control">
                </div>
                <div class="category-filter">
                    <label for="category">Category:</label>
                    <select id="category" class="form-select">
                        <option value="all">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['id']); ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="analytics-grid">
            <!-- Sales Overview -->
            <div class="analytics-card">
                <h3>Sales Overview</h3>
                <div class="overview-stats">
                    <div class="stat-item">
                        <h4>Total Revenue</h4>
                        <p>$<?php echo number_format($analytics['total_revenue'], 2); ?></p>
                    </div>
                    <div class="stat-item">
                        <h4>Orders</h4>
                        <p><?php echo number_format($analytics['total_orders']); ?></p>
                    </div>
                    <div class="stat-item">
                        <h4>Average Order Value</h4>
                        <p>$<?php echo number_format($analytics['avg_order_value'], 2); ?></p>
                    </div>
                </div>
                <div id="salesChart"></div>
            </div>

            <!-- Customer Analytics -->
            <div class="analytics-card">
                <h3>Customer Analytics</h3>
                <div class="customer-stats">
                    <div class="stat-item">
                        <h4>Total Customers</h4>
                        <p><?php echo number_format($analytics['total_customers']); ?></p>
                    </div>
                    <div class="stat-item">
                        <h4>Returning Customers</h4>
                        <p><?php echo number_format($analytics['returning_customers']); ?></p>
                    </div>
                    <div class="stat-item">
                        <h4>Average Order per Customer</h4>
                        <p><?php echo number_format($analytics['avg_orders_per_customer'], 1); ?></p>
                    </div>
                </div>
                <div id="customerChart"></div>
            </div>

            <!-- Product Performance -->
            <div class="analytics-card">
                <h3>Product Performance</h3>
                <div class="top-products">
                    <?php foreach ($analytics['top_products'] as $product): ?>
                    <div class="product-item">
                        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                        <p>Sales: <?php echo number_format($product['sales']); ?></p>
                        <p>Revenue: $<?php echo number_format($product['revenue'], 2); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div id="productChart"></div>
            </div>

            <!-- Repair Services -->
            <div class="analytics-card">
                <h3>Repair Services</h3>
                <div class="service-stats">
                    <div class="stat-item">
                        <h4>Total Appointments</h4>
                        <p><?php echo number_format($analytics['total_appointments']); ?></p>
                    </div>
                    <div class="stat-item">
                        <h4>Completed Repairs</h4>
                        <p><?php echo number_format($analytics['completed_repairs']); ?></p>
                    </div>
                    <div class="stat-item">
                        <h4>Average Repair Time</h4>
                        <p><?php echo number_format($analytics['avg_repair_time'], 1); ?> days</p>
                    </div>
                </div>
                <div id="serviceChart"></div>
            </div>
        </div>
    </div>
</div>

<style>
.analytics-dashboard {
    padding: 40px 0;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
}

.dashboard-filters {
    display: flex;
    gap: 20px;
}

.analytics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.analytics-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.analytics-card h3 {
    color: #2c3e50;
    margin-bottom: 20px;
}

.overview-stats, .customer-stats, .service-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.stat-item {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 5px;
}

.stat-item h4 {
    color: #666;
    margin-bottom: 5px;
}

.stat-item p {
    color: #2c3e50;
    font-weight: bold;
    font-size: 1.2em;
}

#salesChart, #customerChart, #productChart, #serviceChart {
    height: 300px;
    margin-top: 20px;
}

.top-products {
    margin-bottom: 20px;
}

.product-item {
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.product-item:last-child {
    border-bottom: none;
}

@media (max-width: 768px) {
    .dashboard-filters {
        flex-direction: column;
        gap: 10px;
    }
    
    .analytics-grid {
        grid-template-columns: 1fr;
    }
    
    .overview-stats, .customer-stats, .service-stats {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date range picker
    $('#dateRange').daterangepicker({
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate: moment()
    });

    // Initialize charts
    const salesChart = new Chart(document.getElementById('salesChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: <?php echo json_encode($analytics['sales_data']['labels']); ?>,
            datasets: [{
                label: 'Sales',
                data: <?php echo json_encode($analytics['sales_data']['values']); ?>,
                borderColor: '#3498db',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    const customerChart = new Chart(document.getElementById('customerChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: ['New Customers', 'Returning Customers'],
            datasets: [{
                data: [<?php echo $analytics['new_customers']; ?>, <?php echo $analytics['returning_customers']; ?>],
                backgroundColor: ['#2ecc71', '#3498db']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    const productChart = new Chart(document.getElementById('productChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($analytics['top_products'], 'name')); ?>,
            datasets: [{
                label: 'Sales',
                data: <?php echo json_encode(array_column($analytics['top_products'], 'sales')); ?>,
                backgroundColor: '#3498db'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    const serviceChart = new Chart(document.getElementById('serviceChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Pending', 'In Progress', 'Completed'],
            datasets: [{
                data: [
                    <?php echo $analytics['pending_repairs']; ?>,
                    <?php echo $analytics['in_progress_repairs']; ?>,
                    <?php echo $analytics['completed_repairs']; ?>
                ],
                backgroundColor: ['#f1c40f', '#e74c3c', '#2ecc71']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Add filter event listeners
    $('#dateRange, #category').on('change', function() {
        const dateRange = $('#dateRange').val();
        const category = $('#category').val();
        
        // Update analytics data
        fetch(`/api/analytics?dateRange=${dateRange}&category=${category}`)
            .then(response => response.json())
            .then(data => {
                // Update charts with new data
                updateCharts(data);
            });
    });
});

function updateCharts(data) {
    // Update sales chart
    salesChart.data.labels = data.sales_data.labels;
    salesChart.data.datasets[0].data = data.sales_data.values;
    salesChart.update();

    // Update customer chart
    customerChart.data.datasets[0].data = [data.new_customers, data.returning_customers];
    customerChart.update();

    // Update product chart
    productChart.data.labels = data.top_products.map(p => p.name);
    productChart.data.datasets[0].data = data.top_products.map(p => p.sales);
    productChart.update();

    // Update service chart
    serviceChart.data.datasets[0].data = [
        data.pending_repairs,
        data.in_progress_repairs,
        data.completed_repairs
    ];
    serviceChart.update();
}
</script>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
