<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $appName ?> - Invoices</title>
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
                <li class="active">
                    <a href="/invoices"><i class="fas fa-file-invoice-dollar"></i> Invoices</a>
                </li>
                <li>
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Invoices</h2>
                    <a href="/invoices/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> New Invoice
                    </a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Client</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($invoices as $invoice): ?>
                                <tr>
                                    <td><?= $invoice['id'] ?></td>
                                    <td><?= $invoice['client'] ?></td>
                                    <td>$<?= number_format($invoice['amount'], 2) ?></td>
                                    <td><?= $invoice['date'] ?></td>
                                    <td>
                                        <?php
                                            $statusClass = 'bg-secondary';
                                            if ($invoice['status'] === 'Paid') $statusClass = 'bg-success';
                                            if ($invoice['status'] === 'Overdue') $statusClass = 'bg-danger';
                                            if ($invoice['status'] === 'Pending') $statusClass = 'bg-warning';
                                            if ($invoice['status'] === 'Sent') $statusClass = 'bg-primary';
                                        ?>
                                        <span class="badge <?= $statusClass ?>"><?= $invoice['status'] ?></span>
                                    </td>
                                    <td>
                                        <a href="/invoices/<?= $invoice['id'] ?>" class="btn btn-sm btn-info text-white">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/invoices/<?= $invoice['id'] ?>/edit" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/invoices/<?= $invoice['id'] ?>/delete" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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