<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/navbar.php'; ?>
<?php require __DIR__ . '/includes/config.php'; ?>
<?php require __DIR__ . '/includes/functions.php'; ?>

<?php
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = sanitize($_POST['name'] ?? '');
  $email = sanitize($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $password2 = $_POST['password2'] ?? '';

  if (!$name || !$email || !$password || !$password2) {
    $error = 'Vui lòng nhập đầy đủ thông tin.';
  } elseif ($password !== $password2) {
    $error = 'Mật khẩu xác nhận không khớp.';
  } else {
    // kiểm tra email trùng
    $stmt = $mysqli->prepare('SELECT id FROM users WHERE email=?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
      $error = 'Email đã tồn tại.';
    } else {
      // Mã hóa mật khẩu (dùng password_hash an toàn hơn MD5)
      $hash = password_hash($password, PASSWORD_BCRYPT);
      $stmt = $mysqli->prepare('INSERT INTO users (username, email, password, role) VALUES (?,?,?,\'user\')');
      $stmt->bind_param('sss', $name, $email, $hash);
      if ($stmt->execute()) {
        // tự động đăng nhập
        $_SESSION['user'] = [
          'id' => $stmt->insert_id,
          'username' => $name,
          'email' => $email,
          'role' => 'user'
        ];
        redirect(BASE_PATH . '/index.php');
      } else {
        $error = 'Đăng ký thất bại. Vui lòng thử lại.';
      }
    }
  }
}
?>

<main class="container py-5" style="max-width: 560px;">
  <div class="card bg-black border-secondary shadow-sm">
    <div class="card-body p-4">
      <h4 class="text-center mb-3"><i class="fa-solid fa-user-plus text-warning me-2"></i>Đăng ký</h4>
      <form method="post" action="">
        <div class="mb-3">
          <label class="form-label">Họ và tên</label>
          <div class="input-group">
            <span class="input-group-text bg-dark text-secondary border-secondary"><i class="fa-regular fa-user"></i></span>
            <input name="name" type="text" class="form-control bg-dark text-light border-secondary" placeholder="Nguyễn Văn A" required>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <div class="input-group">
            <span class="input-group-text bg-dark text-secondary border-secondary"><i class="fa-regular fa-envelope"></i></span>
            <input name="email" type="email" class="form-control bg-dark text-light border-secondary" placeholder="you@example.com" required>
          </div>
        </div>
        <div class="row g-3">
          <div class="col-sm-6">
            <label class="form-label">Mật khẩu</label>
            <div class="input-group">
              <span class="input-group-text bg-dark text-secondary border-secondary"><i class="fa-solid fa-lock"></i></span>
              <input name="password" type="password" class="form-control bg-dark text-light border-secondary" placeholder="••••••••" required>
            </div>
          </div>
          <div class="col-sm-6">
            <label class="form-label">Xác nhận mật khẩu</label>
            <div class="input-group">
              <span class="input-group-text bg-dark text-secondary border-secondary"><i class="fa-solid fa-lock"></i></span>
              <input name="password2" type="password" class="form-control bg-dark text-light border-secondary" placeholder="••••••••" required>
            </div>
          </div>
        </div>
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger mt-3 py-2"><?php echo $error; ?></div>
        <?php endif; ?>
        <div class="form-check my-3">
          <input class="form-check-input" type="checkbox" id="terms">
          <label class="form-check-label" for="terms">Tôi đồng ý với điều khoản sử dụng</label>
        </div>
        <button type="submit" class="btn btn-warning w-100">Tạo tài khoản</button>
      </form>
      <hr class="border-secondary my-4">
      <p class="text-center text-secondary m-0">Đã có tài khoản? <a href="/film_website/login.php" class="text-warning">Đăng nhập</a></p>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

