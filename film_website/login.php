<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/navbar.php'; ?>
<?php require __DIR__ . '/includes/config.php'; ?>
<?php require __DIR__ . '/includes/functions.php'; ?>

<?php
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = sanitize($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  if (!$email || !$password) {
    $error = 'Vui lòng nhập email và mật khẩu.';
  } else {
    $stmt = $mysqli->prepare('SELECT id, username, email, password, role FROM users WHERE email=?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && (password_verify($password, $user['password']) || md5($password) === $user['password'])) {
      $_SESSION['user'] = [
        'id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'role' => $user['role']
      ];
      if ($user['role'] === 'admin') {
        redirect(BASE_PATH . '/admin/dashboard.php');
      } else {
        redirect(BASE_PATH . '/index.php');
      }
    } else {
      $error = 'Email hoặc mật khẩu không đúng.';
    }
  }
}
?>

<main class="container py-5" style="max-width: 520px;">
  <div class="card bg-black border-secondary shadow-sm">
    <div class="card-body p-4">
      <h4 class="text-center mb-3"><i class="fa-regular fa-circle-user text-warning me-2"></i>Đăng nhập</h4>
      <form method="post" action="">
        <div class="mb-3">
          <label class="form-label">Email</label>
          <div class="input-group">
            <span class="input-group-text bg-dark text-secondary border-secondary"><i class="fa-regular fa-envelope"></i></span>
            <input name="email" type="email" class="form-control bg-dark text-light border-secondary" placeholder="you@example.com" required>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Mật khẩu</label>
          <div class="input-group">
            <span class="input-group-text bg-dark text-secondary border-secondary"><i class="fa-solid fa-lock"></i></span>
            <input name="password" type="password" class="form-control bg-dark text-light border-secondary" placeholder="••••••••" required>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember" />
            <label class="form-check-label" for="remember">Nhớ đăng nhập</label>
          </div>
          <a href="#" class="text-warning small">Quên mật khẩu?</a>
        </div>
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger mb-3 py-2"><?php echo $error; ?></div>
        <?php endif; ?>
        <button type="submit" class="btn btn-warning w-100">Đăng nhập</button>
      </form>
      <hr class="border-secondary my-4">
      <p class="text-center text-secondary m-0">Chưa có tài khoản? <a href="/film_website/register.php" class="text-warning">Đăng ký</a></p>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

