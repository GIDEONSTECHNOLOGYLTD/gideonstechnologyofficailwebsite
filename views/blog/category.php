<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/blog">Blog</a></li>
            <li class="breadcrumb-item active" aria-current="page">Category: <?= htmlspecialchars(str_replace('-', ' ', $category)) ?></li>
        </ol>
    </nav>

    <h1 class="mb-4">Category: <?= htmlspecialchars(ucfirst(str_replace('-', ' ', $category))) ?></h1>
    
    <div class="row">
        <?php if (empty($posts)): ?>
            <div class="col-12">
                <div class="alert alert-info">
                    No posts found in this category.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <?php if (!empty($post['image'])): ?>
                            <img src="<?= htmlspecialchars($post['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($post['title']) ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($post['title']) ?></h5>
                            <p class="card-text text-muted">
                                <small>
                                    Posted on <?= htmlspecialchars($post['date']) ?> by <?= htmlspecialchars($post['author']) ?>
                                </small>
                            </p>
                            <p class="card-text"><?= htmlspecialchars($post['excerpt']) ?></p>
                            <a href="/blog/<?= htmlspecialchars($post['slug']) ?>" class="btn btn-primary">Read More</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- Pagination -->
    <?php if (!empty($posts)): ?>
        <div class="row mt-4">
            <div class="col-12">
                <nav aria-label="Blog pagination">
                    <ul class="pagination justify-content-center">
                        <?php if ($currentPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="/blog/category/<?= htmlspecialchars($category) ?>?page=<?= $currentPage - 1 ?>">Previous</a>
                            </li>
                        <?php endif; ?>
                        
                        <li class="page-item active">
                            <span class="page-link"><?= $currentPage ?></span>
                        </li>
                        
                        <li class="page-item">
                            <a class="page-link" href="/blog/category/<?= htmlspecialchars($category) ?>?page=<?= $currentPage + 1 ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="mt-4">
        <a href="/blog" class="btn btn-secondary">&larr; Back to All Posts</a>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>