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
$mysqli->set_charset('utf8mb4');

// Bật output buffering + session sớm
if (function_exists('ob_start')) ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();

// Tính BASE_PATH ổn định (dựa trên folder project so với DOCUMENT_ROOT)
// Project root = một thư mục lên trên folder includes
$projectRoot = str_replace('\\', '/', realpath(__DIR__ . '/..'));
$docRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));

// Nếu project nằm dưới DOCUMENT_ROOT thì trả đường dẫn web tương đối, ngược lại '' (root)
$base = '';
if ($docRoot && strpos($projectRoot, $docRoot) === 0) {
  $base = substr($projectRoot, strlen($docRoot));
  $base = $base === '/' ? '' : rtrim($base, '/');
}
// đảm bảo luôn bắt đầu bằng '' hoặc '/something'
if ($base === '') $base = '';
else $base = '/' . ltrim($base, '/');

if (!defined('BASE_PATH')) define('BASE_PATH', $base);
?>

