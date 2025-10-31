<?php include __DIR__ . '/includes/header.php'; ?>
<div class="admin-layout">
<?php include __DIR__ . '/includes/sidebar.php'; ?>
<div>
<?php include __DIR__ . '/includes/topbar.php'; ?>

<?php
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $old = $_POST['old_password'] ?? '';
  $new = $_POST['new_password'] ?? '';
  $re  = $_POST['re_password'] ?? '';
  if ($new !== $re) {
    $msg = 'Xác nhận mật khẩu không khớp';
  } else {
    $uid = (int)$_SESSION['user']['id'];
    $stmt = $mysqli->prepare('SELECT password FROM users WHERE id=?');
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $ok = $row && (password_verify($old, $row['password']) || md5($old) === $row['password']);
    if (!$ok) {
      $msg = 'Mật khẩu cũ không đúng';
    } else {
      $hash = password_hash($new, PASSWORD_BCRYPT);
      $upd = $mysqli->prepare('UPDATE users SET password=? WHERE id=?');
      $upd->bind_param('si', $hash, $uid);
      $msg = $upd->execute() ? 'Đổi mật khẩu thành công!' : 'Có lỗi xảy ra';
    }
  }
}
?>

<main class="container-fluid my-4 my-md-5">
  <h3 class="section-title mb-3">Hồ sơ admin</h3>

  <?php if (!empty($msg)): ?>
    <div class="alert <?php echo $msg==='Đổi mật khẩu thành công!' ? 'alert-success' : 'alert-danger'; ?>"><?php echo $msg; ?></div>
  <?php endif; ?>

  <div class="row g-4">
    <div class="col-lg-4">
      <div class="card bg-black border-secondary text-center p-4">
        <img src="https://i.pravatar.cc/150?img=12" class="rounded-circle mx-auto mb-3" width="96" height="96" alt="avatar">
        <h5 class="mb-1"><?php echo htmlspecialchars($_SESSION['user']['username']); ?></h5>
        <p class="text-secondary small mb-0"><?php echo htmlspecialchars($_SESSION['user']['email']); ?></p>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="card bg-black border-secondary">
        <div class="card-header">Đổi mật khẩu</div>
        <div class="card-body">
          <form method="post" action="">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Mật khẩu cũ</label>
                <input type="password" name="old_password" class="form-control bg-dark text-light border-secondary" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Mật khẩu mới</label>
                <input type="password" name="new_password" class="form-control bg-dark text-light border-secondary" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Xác nhận</label>
                <input type="password" name="re_password" class="form-control bg-dark text-light border-secondary" required>
              </div>
            </div>
            <div class="mt-3">
              <button class="btn btn-warning" type="submit">Cập nhật</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</div>
</div>

