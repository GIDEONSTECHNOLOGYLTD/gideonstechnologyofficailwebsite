<?php
/**
 * GTech Templates Page
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Templates | <?= $appName ?? 'Gideons Technology' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <style>
        .template-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        .template-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .template-img {
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <!-- Include header -->
    <?php include_once dirname(__DIR__) . '/partials/header.php'; ?>
    
    <!-- Main Content -->
    <div class="container py-5">
        <header class="text-center mb-5">
            <h1>Website Templates</h1>
            <p class="lead">Professional, responsive templates for your next web project</p>
        </header>
        
        <!-- Template Categories -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    <div class="btn-group" role="group">
                        <a href="/gtech/templates" class="btn btn-primary active">All Templates</a>
                        <a href="/gtech/templates/ecommerce" class="btn btn-outline-primary">E-commerce</a>
                        <a href="/gtech/templates/portfolio" class="btn btn-outline-primary">Portfolio</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Templates Grid -->
        <div class="row g-4">
            <?php foreach ($templates as $template): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card template-card">
                    <img src="https://via.placeholder.com/600x400?text=<?= urlencode($template['name']) ?>" class="card-img-top template-img" alt="<?= htmlspecialchars($template['name']) ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($template['name']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($template['description']) ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-secondary"><?= htmlspecialchars(ucfirst($template['category'])) ?></span>
                            <span class="fw-bold">$<?= number_format($template['price'], 2) ?></span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <a href="/gtech/templates/<?= $template['id'] ?>" class="btn btn-primary w-100">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Call to Action -->
    <section class="bg-light py-5">
        <div class="container text-center">
            <h2>Need a Custom Template?</h2>
            <p class="lead mb-4">Our team can create a custom template tailored to your specific requirements</p>
            <a href="/gtech/consultation" class="btn btn-lg btn-primary">Request a Consultation</a>
        </div>
    </section>
    
    <!-- Include footer -->
    <?php include_once dirname(__DIR__) . '/partials/footer.php'; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
