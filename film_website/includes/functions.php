<?php
// Các hàm tiện ích dùng chung

if (!function_exists('sanitize')) {
  function sanitize($s) {
    return htmlspecialchars(trim($s ?? ''), ENT_QUOTES, 'UTF-8');
  }
}

if (!function_exists('redirect')) {
  function redirect($path) {
    header('Location: ' . $path);
    exit;
  }
}

if (!function_exists('isLoggedIn')) {
  function isLoggedIn() {
    return isset($_SESSION['user']);
  }
}

if (!function_exists('isAdmin')) {
  function isAdmin() {
    return isLoggedIn() && ($_SESSION['user']['role'] ?? 'user') === 'admin';
  }
}

// Lấy danh sách thể loại
if (!function_exists('getCategories')) {
  function getCategories($mysqli) {
    $sql = "SELECT id, name FROM categories ORDER BY name";
    return $mysqli->query($sql);
  }
}

// Lấy danh sách phim (có thể giới hạn)
if (!function_exists('getMovies')) {
  function getMovies($mysqli, $limit = 12) {
    $sql = "SELECT m.*, c.name AS category_name FROM movies m LEFT JOIN categories c ON m.category_id = c.id ORDER BY m.created_at DESC LIMIT ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    return $stmt->get_result();
  }
}

// Lấy phim theo id
if (!function_exists('getMovieById')) {
  function getMovieById($mysqli, $id) {
    $sql = "SELECT m.*, c.name AS category_name FROM movies m LEFT JOIN categories c ON m.category_id = c.id WHERE m.id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
  }
}

// Tìm kiếm phim theo từ khóa tiêu đề
if (!function_exists('searchMovies')) {
  function searchMovies($mysqli, $keyword, $limit = 24) {
    $k = '%' . $keyword . '%';
    $sql = "SELECT m.*, c.name AS category_name FROM movies m LEFT JOIN categories c ON m.category_id = c.id WHERE m.title LIKE ? ORDER BY m.created_at DESC LIMIT ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('si', $k, $limit);
    $stmt->execute();
    return $stmt->get_result();
  }
}

// Lấy phim theo thể loại
if (!function_exists('getMoviesByCategory')) {
  function getMoviesByCategory($mysqli, $categoryId, $limit = 24) {
    $sql = "SELECT m.*, c.name AS category_name FROM movies m LEFT JOIN categories c ON m.category_id = c.id WHERE c.id = ? ORDER BY m.created_at DESC LIMIT ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ii', $categoryId, $limit);
    $stmt->execute();
    return $stmt->get_result();
  }
}

// Yêu thích
if (!function_exists('getUserFavorites')) {
  function getUserFavorites($mysqli, $userId) {
    $sql = "SELECT m.* FROM favorites f JOIN movies m ON f.movie_id = m.id WHERE f.user_id = ? ORDER BY f.created_at DESC";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    return $stmt->get_result();
  }
}

if (!function_exists('addFavorite')) {
  function addFavorite($mysqli, $userId, $movieId) {
    // tránh trùng bản ghi
    $check = $mysqli->prepare("SELECT id FROM favorites WHERE user_id=? AND movie_id=?");
    $check->bind_param('ii', $userId, $movieId);
    $check->execute();
    if ($check->get_result()->num_rows > 0) return true;

    $stmt = $mysqli->prepare("INSERT INTO favorites (user_id, movie_id) VALUES (?, ?)");
    $stmt->bind_param('ii', $userId, $movieId);
    return $stmt->execute();
  }
}

if (!function_exists('removeFavorite')) {
  function removeFavorite($mysqli, $userId, $movieId) {
    $stmt = $mysqli->prepare("DELETE FROM favorites WHERE user_id=? AND movie_id=?");
    $stmt->bind_param('ii', $userId, $movieId);
    return $stmt->execute();
  }
}

if (!function_exists('userHasFavorite')) {
  function userHasFavorite($mysqli, $userId, $movieId) {
    $stmt = $mysqli->prepare('SELECT id FROM favorites WHERE user_id=? AND movie_id=?');
    $stmt->bind_param('ii', $userId, $movieId);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
  }
}
?>

