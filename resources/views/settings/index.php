<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $appName ?> - Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="/css/dashboard.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><?= $appName ?></h3>
            </div>

            <ul class="list-unstyled components">
                <li>
                    <a href="/dashboard"><i class="fas fa-home"></i> Dashboard</a>
                </li>
                <li>
                    <a href="#projectsSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fas fa-project-diagram"></i> Projects
                    </a>
                    <ul class="collapse list-unstyled" id="projectsSubmenu">
                        <li>
                            <a href="/projects">View All</a>
                        </li>
                        <li>
                            <a href="/projects/create">Create New</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="/clients"><i class="fas fa-users"></i> Clients</a>
                </li>
                <li>
                    <a href="/invoices"><i class="fas fa-file-invoice-dollar"></i> Invoices</a>
                </li>
                <li class="active">
                    <a href="/settings"><i class="fas fa-cog"></i> Settings</a>
                </li>
                <li>
                    <a href="/logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-primary">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="ms-auto d-flex align-items-center">
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i> Admin User
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/profile">Profile</a></li>
                                <li><a class="dropdown-item" href="/settings">Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/logout">Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="container-fluid">
                <h2 class="mb-4">Settings</h2>

                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="list-group">
                            <a href="#general" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                                General Settings
                            </a>
                            <a href="#company" class="list-group-item list-group-item-action" data-bs-toggle="list">
                                Company Information
                            </a>
                            <a href="#payment" class="list-group-item list-group-item-action" data-bs-toggle="list">
                                Payment Integration
                            </a>
                            <a href="#notifications" class="list-group-item list-group-item-action" data-bs-toggle="list">
                                Notifications
                            </a>
                            <a href="#users" class="list-group-item list-group-item-action" data-bs-toggle="list">
                                User Management
                            </a>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="general">
                                <div class="card">
                                    <div class="card-header">
                                        General Settings
                                    </div>
                                    <div class="card-body">
                                        <form>
                                            <div class="mb-3">
                                                <label for="appName" class="form-label">Application Name</label>
                                                <input type="text" class="form-control" id="appName" value="Gideons Technology">
                                            </div>
                                            <div class="mb-3">
                                                <label for="timezone" class="form-label">Default Timezone</label>
                                                <select class="form-select" id="timezone">
                                                    <option value="UTC" selected>UTC</option>
                                                    <option value="America/New_York">Eastern Time</option>
                                                    <option value="America/Chicago">Central Time</option>
                                                    <option value="America/Denver">Mountain Time</option>
                                                    <option value="America/Los_Angeles">Pacific Time</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="dateFormat" class="form-label">Date Format</label>
                                                <select class="form-select" id="dateFormat">
                                                    <option value="Y-m-d" selected>2023-05-01</option>
                                                    <option value="m/d/Y">05/01/2023</option>
                                                    <option value="d/m/Y">01/05/2023</option>
                                                    <option value="M j, Y">May 1, 2023</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="company">
                                <div class="card">
                                    <div class="card-header">
                                        Company Information
                                    </div>
                                    <div class="card-body">
                                        <form>
                                            <div class="mb-3">
                                                <label for="companyName" class="form-label">Company Name</label>
                                                <input type="text" class="form-control" id="companyName" value="Gideons Technology">
                                            </div>
                                            <div class="mb-3">
                                                <label for="address" class="form-label">Address</label>
                                                <textarea class="form-control" id="address" rows="3">123 Tech Street, Suite 456, San Francisco, CA 94107</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="phone" class="form-label">Phone</label>
                                                <input type="text" class="form-control" id="phone" value="+1 (555) 123-4567">
                                            </div>
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email" value="info@gideonstech.com">
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="payment">
                                <!-- Payment integration settings would go here -->
                                <div class="card">
                                    <div class="card-header">
                                        Payment Integration
                                    </div>
                                    <div class="card-body">
                                        <form>
                                            <h5 class="mb-3">Stripe Integration</h5>
                                            <div class="mb-3">
                                                <label for="stripeKey" class="form-label">API Key</label>
                                                <input type="text" class="form-control" id="stripeKey" value="pk_test_****************************">
                                            </div>
                                            <div class="mb-3">
                                                <label for="stripeSecret" class="form-label">Secret Key</label>
                                                <input type="password" class="form-control" id="stripeSecret" value="sk_test_****************************">
                                            </div>
                                            <hr>
                                            <h5 class="mb-3">PayPal Integration</h5>
                                            <div class="mb-3">
                                                <label for="paypalClientId" class="form-label">Client ID</label>
                                                <input type="text" class="form-control" id="paypalClientId">
                                            </div>
                                            <div class="mb-3">
                                                <label for="paypalSecret" class="form-label">Client Secret</label>
                                                <input type="password" class="form-control" id="paypalSecret">
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="notifications">
                                <!-- Notification settings would go here -->
                                <div class="card">
                                    <div class="card-header">
                                        Notification Settings
                                    </div>
                                    <div class="card-body">
                                        <form>
                                            <div class="mb-3 form-check">
                                                <input type="checkbox" class="form-check-input" id="emailNotifications" checked>
                                                <label class="form-check-label" for="emailNotifications">Email Notifications</label>
                                            </div>
                                            <div class="mb-3 form-check">
                                                <input type="checkbox" class="form-check-input" id="projectUpdates" checked>
                                                <label class="form-check-label" for="projectUpdates">Project Updates</label>
                                            </div>
                                            <div class="mb-3 form-check">
                                                <input type="checkbox" class="form-check-input" id="paymentNotifications" checked>
                                                <label class="form-check-label" for="paymentNotifications">Payment Notifications</label>
                                            </div>
                                            <div class="mb-3 form-check">
                                                <input type="checkbox" class="form-check-input" id="systemAlerts" checked>
                                                <label class="form-check-label" for="systemAlerts">System Alerts</label>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="users">
                                <!-- User management would go here -->
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <span>User Management</span>
                                        <button class="btn btn-sm btn-primary">Add User</button>
                                    </div>
                                    <div class="card-body">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Role</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Admin User</td>
                                                    <td>admin@gideonstech.com</td>
                                                    <td>Administrator</td>
                                                    <td><span class="badge bg-success">Active</span></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></button>
                                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Manager User</td>
                                                    <td>manager@gideonstech.com</td>
                                                    <td>Manager</td>
                                                    <td><span class="badge bg-success">Active</span></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></button>
                                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                                    </td>
                                                </tr>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('sidebarCollapse').addEventListener('click', function() {
                document.getElementById('sidebar').classList.toggle('active');
            });
        });
    </script>
</body>
</html>