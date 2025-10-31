<?php
// Chặn truy cập nếu không phải admin
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
  $base = defined('BASE_PATH') ? rtrim(BASE_PATH, '/') : '';
  header('Location: ' . $base . '/login.php');
  exit;
}
?>

