<?php include __DIR__ . '/includes/header.php'; ?>
<div class="admin-layout">
<?php include __DIR__ . '/includes/sidebar.php'; ?>
<div>
<?php include __DIR__ . '/includes/topbar.php'; ?>

<?php
$action = $_GET['action'] ?? '';
if ($action === 'delete') {
  $id = (int)($_GET['id'] ?? 0);
  // Không xóa admin mặc định (email admin@gmail.com)
  $stmt = $mysqli->prepare('SELECT email FROM users WHERE id=?');
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $user = $stmt->get_result()->fetch_assoc();
  if ($user && $user['email'] !== 'admin@gmail.com') {
    $del = $mysqli->prepare('DELETE FROM users WHERE id=?');
    $del->bind_param('i', $id);
    $del->execute();
  }
  redirect(BASE_PATH . '/admin/manage_users.php');
}

$users = $mysqli->query('SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC');
?>

<main class="container-fluid my-4 my-md-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="section-title m-0">Quản lý người dùng</h3>
  </div>

  <div class="card bg-black border-secondary">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-dark table-striped align-middle">
          <thead><tr><th>#</th><th>Tên</th><th>Email</th><th>Role</th><th>Ngày tạo</th><th class="text-end">Thao tác</th></tr></thead>
          <tbody>
            <?php $i=1; while($u=$users->fetch_assoc()): ?>
            <tr>
              <td><?php echo $i++; ?></td>
              <td><?php echo htmlspecialchars($u['username']); ?></td>
              <td class="text-secondary small"><?php echo htmlspecialchars($u['email']); ?></td>
              <td><span class="badge text-bg-dark border border-secondary"><?php echo $u['role']; ?></span></td>
              <td class="small text-secondary"><?php echo $u['created_at']; ?></td>
              <td class="text-end">
                <?php if ($u['email'] !== 'admin@gmail.com'): ?>
                  <a class="btn btn-sm btn-outline-danger" href="?action=delete&id=<?php echo $u['id']; ?>" onclick="return confirm('Xóa người dùng này?');"><i class="fa-regular fa-trash-can"></i></a>
                <?php endif; ?>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</div>
</div>

