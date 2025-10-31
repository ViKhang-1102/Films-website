<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';

if (!isset($_SESSION['user'])) {
  header('Location: ' . BASE_PATH . '/login.php');
  exit;
}

$userId = (int)$_SESSION['user']['id'];
$movieId = (int)($_POST['movie_id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($movieId > 0) {
  if ($action === 'add') {
    addFavorite($mysqli, $userId, $movieId);
  } elseif ($action === 'remove') {
    removeFavorite($mysqli, $userId, $movieId);
  }
}

$redirect = $_POST['redirect'] ?? (BASE_PATH . '/profile.php');
header('Location: ' . $redirect);
exit;
?>

