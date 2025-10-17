<?php
// Kết nối MySQL (mysqli)
// Chỉnh các biến này theo XAMPP của bạn nếu khác
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'film_db';

$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($mysqli->connect_errno) {
  die('Lỗi kết nối MySQL: ' . $mysqli->connect_error);
}

// Thiết lập charset UTF-8
$mysqli->set_charset('utf8mb4');

// Bật output buffering để tránh "headers already sent" do accidental output
if (function_exists('ob_start')) ob_start();

// Khởi động session nếu cần
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Đường dẫn gốc tương đối cho link
if (!defined('BASE_PATH')) {
  define('BASE_PATH', '/film_website');
}
?>

