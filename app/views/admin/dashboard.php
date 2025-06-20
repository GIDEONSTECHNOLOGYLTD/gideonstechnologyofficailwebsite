<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Gideon's Technology</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <div class="logo">
                <h2>Admin Panel</h2>
            </div>
            <nav>
                <ul>
                    <li class="active"><a href="/admin/dashboard">Dashboard</a></li>
                    <li><a href="/admin/users">Users</a></li>
                    <li><a href="/admin/products">Products</a></li>
                    <li><a href="/admin/orders">Orders</a></li>
                    <li><a href="/logout">Logout</a></li>
                </ul>
            </nav>
        </div>
        <div class="main-content">
            <header>
                <h1>Dashboard</h1>
                <div class="user-info">
                    Welcome, Admin
                </div>
            </header>
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <p class="count">123</p>
                </div>
                <div class="stat-card">
                    <h3>Total Products</h3>
                    <p class="count">45</p>
                </div>
                <div class="stat-card">
                    <h3>Total Orders</h3>
                    <p class="count">67</p>
                </div>
                <div class="stat-card">
                    <h3>Revenue</h3>
                    <p class="count">$12,345</p>
                </div>
            </div>
            <div class="recent-activities">
                <h2>Recent Activities</h2>
                <div class="activity-list">
                    <div class="activity-item">
                        <span class="time">Today, 10:30 AM</span>
                        <span class="description">New order placed #12345</span>
                    </div>
                    <div class="activity-item">
                        <span class="time">Yesterday, 3:45 PM</span>
                        <span class="description">New user registered: John Doe</span>
                    </div>
                    <div class="activity-item">
                        <span class="time">Yesterday, 1:15 PM</span>
                        <span class="description">Product updated: Smartphone X</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/assets/js/admin.js"></script>
</body>
</html>