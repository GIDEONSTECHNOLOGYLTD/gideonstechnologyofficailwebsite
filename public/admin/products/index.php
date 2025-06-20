<?php
require_once __DIR__ . '/../../../app/bootstrap.php';
use App\Repositories\ProductRepository;

// Only allow admins (you can expand this check)
if (!isset($_SESSION['user_id']) /*|| $_SESSION['role'] !== 'admin'*/) {
    header('Location: ' . SITE_URL . '/login.php'); exit;
}

$productRepo = new ProductRepository($pdo);
$products = $productRepo->getAll();

include_once __DIR__ . '/../../includes/header.php';
?>
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Manage Products</h1>
    <a href="<?php echo SITE_URL; ?>/admin/products/create" class="btn btn-primary">Add New Product</a>
  </div>
  <table class="table table-hover">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Category</th>
        <th>Price</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($products as $prod): ?>
      <tr>
        <td><?php echo $prod->id; ?></td>
        <td><?php echo htmlspecialchars($prod->name); ?></td>
        <td><?php echo htmlspecialchars($prod->category); ?></td>
        <td>$<?php echo number_format($prod->price, 2); ?></td>
        <td><?php echo $prod->is_active ? 'Active' : 'Inactive'; ?></td>
        <td>
          <a href="<?php echo SITE_URL; ?>/admin/products/edit/<?php echo $prod->id; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
          <a href="<?php echo SITE_URL; ?>/admin/products/delete/<?php echo $prod->id; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this product?');">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include_once __DIR__ . '/../../includes/footer.php';
