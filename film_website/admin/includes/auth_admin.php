<?php
// Chặn truy cập nếu không phải admin
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
  header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '/film_website') . '/login.php');
  exit;
}
?>

