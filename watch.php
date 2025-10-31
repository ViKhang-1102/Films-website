<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . ( $_POST['redirect'] ?? BASE_PATH . '/index.php'));
  exit;
}

$action = $_POST['action'] ?? '';
$redirect = $_POST['redirect'] ?? BASE_PATH . '/index.php';
if (!isset($_SESSION['user'])) {
  // chưa login -> redirect to login
  header('Location: ' . BASE_PATH . '/login.php?redirect=' . urlencode($redirect));
  exit;
}
$uid = (int)$_SESSION['user']['id'];

if ($action === 'mark_movie') {
  $mid = (int)($_POST['movie_id'] ?? 0);
  markWatched($mysqli, $uid, $mid, null);
} elseif ($action === 'unmark_movie') {
  $mid = (int)($_POST['movie_id'] ?? 0);
  unmarkWatched($mysqli, $uid, $mid, null);
} elseif ($action === 'mark_episode') {
  $eid = (int)($_POST['episode_id'] ?? 0);
  markWatched($mysqli, $uid, null, $eid);
} elseif ($action === 'unmark_episode') {
  $eid = (int)($_POST['episode_id'] ?? 0);
  unmarkWatched($mysqli, $uid, null, $eid);
}

header('Location: ' . $redirect);
exit;