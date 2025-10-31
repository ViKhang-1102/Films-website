<?php include __DIR__ . '/includes/header.php'; ?>
<div class="admin-layout">
<?php include __DIR__ . '/includes/sidebar.php'; ?>
<div>
<?php include __DIR__ . '/includes/topbar.php'; ?>

<?php
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = sanitize($_POST['name'] ?? '');
  $description = sanitize($_POST['description'] ?? '');
  $stmt = $mysqli->prepare('INSERT INTO categories (name, description) VALUES (?,?)');
  $stmt->bind_param('ss', $name, $description);
  $msg = $stmt->execute() ? 'Thêm danh mục thành công!' : 'Thêm danh mục thất bại!';
}
?>

<main class="container-fluid my-4 my-md-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="section-title m-0">Thêm danh mục</h3>
    <a class="btn btn-outline-warning" href="<?php echo BASE_PATH; ?>/admin/manage_categories.php">Quay lại</a>
  </div>

  <?php if (!empty($msg)): ?>
    <div class="alert <?php echo $msg==='Thêm danh mục thành công!' ? 'alert-success' : 'alert-danger'; ?>"><?php echo $msg; ?></div>
  <?php endif; ?>

  <div class="card bg-black border-secondary">
    <div class="card-body">
      <form method="post" action="">
        <div class="mb-3">
          <label class="form-label">Tên</label>
          <input name="name" class="form-control bg-dark text-light border-secondary" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Mô tả</label>
          <textarea name="description" class="form-control bg-dark text-light border-secondary" rows="4"></textarea>
        </div>
        <button class="btn btn-warning" type="submit">Lưu</button>
      </form>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</div>
</div>

