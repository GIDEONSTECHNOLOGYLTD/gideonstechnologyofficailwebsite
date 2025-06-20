<?php
require_once __DIR__ . '/../../../app/bootstrap.php';
use App\Repositories\ProductRepository;

// Admin check (simplified)
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php'); exit;
}

$productRepo = new ProductRepository($pdo);

// Handle form submission
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'category'    => trim($_POST['category'] ?? ''),
        'name'        => trim($_POST['name'] ?? ''),
        'slug'        => trim($_POST['slug'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'price'       => floatval($_POST['price'] ?? 0),
        'sale_price'  => $_POST['sale_price'] !== '' ? floatval($_POST['sale_price']) : null,
        'stock'       => intval($_POST['stock'] ?? 0),
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'is_active'   => isset($_POST['is_active']) ? 1 : 0,
        'sort_order'  => intval($_POST['sort_order'] ?? 0),
    ];
    // Basic validations
    if (!$data['category'] || !$data['name'] || !$data['slug'] || $data['price'] <= 0) {
        $errors[] = 'Category, Name, Slug, and Price are required.';
    }
    if (empty($errors)) {
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $tmpFile = $_FILES['image']['tmp_name'];
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = $data['slug'] . '.' . $ext;
            $dest = __DIR__ . '/../../../public/assets/img/services/gstore/products/' . $filename;
            move_uploaded_file($tmpFile, $dest);
            $data['image'] = 'services/gstore/products/' . $filename;
        }
        $productRepo->create($data);
        header('Location: ' . SITE_URL . '/admin/products');
        exit;
    }
}

$page_title = 'Add Product – ' . SITE_NAME;
include_once __DIR__ . '/../../includes/header.php';
?>
<div class="container py-5">
  <h1>Add New Product</h1>
  <?php if ($errors): ?>
    <div class="alert alert-danger"><ul><?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul></div>
  <?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">Category</label>
      <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($_POST['category'] ?? ''); ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Slug</label>
      <input type="text" name="slug" class="form-control" value="<?php echo htmlspecialchars($_POST['slug'] ?? ''); ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea name="description" class="form-control"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
    </div>
    <div class="row">
      <div class="col-md-4 mb-3">
        <label class="form-label">Price</label>
        <input type="number" step="0.01" name="price" class="form-control" value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>">
      </div>
      <div class="col-md-4 mb-3">
        <label class="form-label">Sale Price</label>
        <input type="number" step="0.01" name="sale_price" class="form-control" value="<?php echo htmlspecialchars($_POST['sale_price'] ?? ''); ?>">
      </div>
      <div class="col-md-4 mb-3">
        <label class="form-label">Stock</label>
        <input type="number" name="stock" class="form-control" value="<?php echo htmlspecialchars($_POST['stock'] ?? ''); ?>">
      </div>
    </div>
    <div class="form-check mb-3">
      <input type="checkbox" name="is_featured" class="form-check-input" id="featured" <?php echo isset($_POST['is_featured']) ? 'checked' : ''; ?>>
      <label class="form-check-label" for="featured">Featured</label>
    </div>
    <div class="form-check mb-3">
      <input type="checkbox" name="is_active" class="form-check-input" id="active" <?php echo isset($_POST['is_active']) ? 'checked' : ''; ?>>
      <label class="form-check-label" for="active">Active</label>
    </div>
    <div class="mb-3">
      <label class="form-label">Sort Order</label>
      <input type="number" name="sort_order" class="form-control" value="<?php echo htmlspecialchars($_POST['sort_order'] ?? '0'); ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Image</label>
      <input type="file" name="image" class="form-control">
    </div>
    <button class="btn btn-success">Save Product</button>
    <a href="<?php echo SITE_URL; ?>/admin/products" class="btn btn-secondary">Cancel</a>
  </form>
</div>
<?php include_once __DIR__ . '/../../includes/footer.php';
