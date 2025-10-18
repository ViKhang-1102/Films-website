<?php include __DIR__ . '/includes/header.php'; ?>
<div class="admin-layout">
<?php include __DIR__ . '/includes/sidebar.php'; ?>
<div>
<?php include __DIR__ . '/includes/topbar.php'; ?>

<?php
// Thêm / sửa / xóa thể loại cơ bản
$action = $_GET['action'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = sanitize($_POST['name'] ?? '');
  $desc = sanitize($_POST['description'] ?? '');
  if ($action === 'edit') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $mysqli->prepare('UPDATE categories SET name=?, description=? WHERE id=?');
    $stmt->bind_param('ssi', $name, $desc, $id);
    $stmt->execute();
    redirect(BASE_PATH . '/admin/manage_categories.php');
  } else {
    $stmt = $mysqli->prepare('INSERT INTO categories (name, description) VALUES (?,?)');
    $stmt->bind_param('ss', $name, $desc);
    $stmt->execute();
    redirect(BASE_PATH . '/admin/manage_categories.php');
  }
}
if ($action === 'delete') {
  $id = (int)($_GET['id'] ?? 0);
  $stmt = $mysqli->prepare('DELETE FROM categories WHERE id=?');
  $stmt->bind_param('i', $id);
  $stmt->execute();
  redirect(BASE_PATH . '/admin/manage_categories.php');
}

$editItem = null;
if ($action === 'edit') {
  $id = (int)($_GET['id'] ?? 0);
  $stmt = $mysqli->prepare('SELECT * FROM categories WHERE id=?');
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $editItem = $stmt->get_result()->fetch_assoc();
}

// Lấy danh sách thể loại có cả mô tả
$cats = $mysqli->query('SELECT id, name, description FROM categories ORDER BY name ASC');
?>

<main class="container-fluid my-4 my-md-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="section-title m-0">Quản lý thể loại</h3>
    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addCatModal"><i class="fa-solid fa-plus me-2"></i>Thêm</button>
  </div>

  <div class="row g-4">
    <div class="col-12">
      <div class="card bg-black border-secondary">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-dark table-striped align-middle">
              <thead class="table-dark">
                <tr>
                  <th>#</th>
                  <th>Tên</th>
                  <th>Mô tả</th>
                  <th>Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <?php $i = 1; while ($c = $cats->fetch_assoc()): ?>
                <tr>
                  <td><?php echo $i++; ?></td>
                  <td><?php echo htmlspecialchars($c['name']); ?></td>
                  <td><?php echo htmlspecialchars($c['description']); ?></td>
                  <td>
                    <a class="btn btn-sm btn-outline-warning" href="?action=edit&id=<?php echo $c['id']; ?>"><i class="fa-solid fa-pen"></i></a>
                    <form method="post" style="display:inline" onsubmit="return confirm('Xóa thể loại?');">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                      <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                    </form>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal thêm nhanh -->
  <div class="modal fade" id="addCatModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content bg-black border border-secondary">
        <form method="post" action="">
          <div class="modal-header border-secondary">
            <h5 class="modal-title">Thêm danh mục</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Tên</label>
              <input name="name" class="form-control bg-dark text-light border-secondary" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Mô tả</label>
              <textarea name="description" class="form-control bg-dark text-light border-secondary" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer border-secondary">
            <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Hủy</button>
            <button class="btn btn-warning" type="submit">Lưu</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</div>
</div>

