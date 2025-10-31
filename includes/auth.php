<?php
// Kiểm tra phân quyền vào khu vực admin
function requireAdmin() {
  if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
    header('Location: ' . BASE_PATH . '/index.php');
    exit;
  }
}

// Chỉ cho phép người dùng đã đăng nhập
function requireLogin() {
  if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_PATH . '/login.php');
    exit;
  }
}
?>

