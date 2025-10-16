<?php include __DIR__ . '/includes/header.php'; ?>
<div class="admin-layout">
<?php include __DIR__ . '/includes/sidebar.php'; ?>
<div>
<?php include __DIR__ . '/includes/topbar.php'; ?>

<?php
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: ' . BASE_PATH . '/admin/manage_categories.php'); exit; }

$stmt = $mysqli->prepare('SELECT * FROM categories WHERE id=?');
$stmt->bind_param('i', $id);
$stmt->execute();
$cat = $stmt->get_result()->fetch_assoc();
if (!$cat) { header('Location: ' . BASE_PATH . '/admin/manage_categories.php'); exit; }

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = sanitize($_POST['name'] ?? '');
  $description = sanitize($_POST['description'] ?? '');
  $upd = $mysqli->prepare('UPDATE categories SET name=?, description=? WHERE id=?');
  $upd->bind_param('ssi', $name, $description, $id);
  $msg = $upd->execute() ? 'Cập nhật thành công!' : 'Cập nhật thất bại!';
}
?>

<main class="container-fluid my-4 my-md-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="section-title m-0">Sửa danh mục</h3>
    <a class="btn btn-outline-warning" href="<?php echo BASE_PATH; ?>/admin/manage_categories.php">Quay lại</a>
  </div>

  <?php if (!empty($msg)): ?>
    <div class="alert <?php echo $msg==='Cập nhật thành công!' ? 'alert-success' : 'alert-danger'; ?>"><?php echo $msg; ?></div>
  <?php endif; ?>

  <div class="card bg-black border-secondary">
    <div class="card-body">
      <form method="post" action="">
        <div class="mb-3">
          <label class="form-label">Tên</label>
          <input name="name" class="form-control bg-dark text-light border-secondary" value="<?php echo htmlspecialchars($cat['name']); ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Mô tả</label>
          <textarea name="description" class="form-control bg-dark text-light border-secondary" rows="4"><?php echo htmlspecialchars($cat['description']); ?></textarea>
        </div>
        <button class="btn btn-warning" type="submit">Lưu</button>
      </form>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</div>
</div>

