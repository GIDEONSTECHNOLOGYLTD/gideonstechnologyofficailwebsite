<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/blog">Blog</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($post['title']) ?></li>
        </ol>
    </nav>
    
    <article class="blog-post">
        <header class="mb-4">
            <h1><?= htmlspecialchars($post['title']) ?></h1>
            <div class="text-muted mb-3">
                <span>Posted on <?= htmlspecialchars($post['date']) ?> by <?= htmlspecialchars($post['author']) ?></span>
                <span class="ms-3">
                    Categories: 
                    <?php foreach ($post['categories'] as $index => $category): ?>
                        <a href="/blog/category/<?= htmlspecialchars($category) ?>" class="text-decoration-none">
                            <?= htmlspecialchars(str_replace('-', ' ', $category)) ?>
                        </a><?= ($index < count($post['categories']) - 1) ? ', ' : '' ?>
                    <?php endforeach; ?>
                </span>
            </div>
        </header>
        
        <?php if (!empty($post['image'])): ?>
            <div class="text-center mb-4">
                <img src="<?= htmlspecialchars($post['image']) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($post['title']) ?>">
            </div>
        <?php endif; ?>
        
        <div class="blog-content">
            <?= $post['content'] ?>
        </div>
    </article>
    
    <div class="mt-5">
        <a href="/blog" class="btn btn-secondary">&larr; Back to Blog</a>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>