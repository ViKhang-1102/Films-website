<?php
// Các hàm tiện ích dùng chung

if (!function_exists('sanitize')) {
  function sanitize($s) {
    return htmlspecialchars(trim($s ?? ''), ENT_QUOTES, 'UTF-8');
  }
}

if (!function_exists('redirect')) {
  function redirect($path) {
    // Nếu headers chưa gửi -> dùng header() như bình thường
    if (!headers_sent()) {
      header('Location: ' . $path);
      exit;
    }
    // Nếu headers đã gửi -> fallback: JS + meta refresh (an toàn khi header không thể gửi)
    $url = htmlspecialchars($path, ENT_QUOTES, 'UTF-8');
    echo '<script type="text/javascript">window.location.href = "' . $url . '";</script>';
    echo '<noscript><meta http-equiv="refresh" content="0;url=' . $url . '"></noscript>';
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
    $sql = "
      SELECT 
        m.*,
        GROUP_CONCAT(c.name SEPARATOR ', ') AS category_name
      FROM movies m
      LEFT JOIN movie_categories mc ON m.id = mc.movie_id
      LEFT JOIN categories c ON mc.category_id = c.id
      GROUP BY m.id
      ORDER BY m.created_at DESC
      LIMIT ?
    ";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    return $stmt->get_result();
  }
}

// Lấy phim theo id
if (!function_exists('getMovieById')) {
  function getMovieById($mysqli, $id) {
    $sql = "
      SELECT 
        m.*,
        GROUP_CONCAT(c.name SEPARATOR ', ') AS category_name
      FROM movies m
      LEFT JOIN movie_categories mc ON m.id = mc.movie_id
      LEFT JOIN categories c ON mc.category_id = c.id
      WHERE m.id = ?
      GROUP BY m.id
      LIMIT 1
    ";
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
    $sql = "
      SELECT 
        m.*,
        GROUP_CONCAT(c.name SEPARATOR ', ') AS category_name
      FROM movies m
      LEFT JOIN movie_categories mc ON m.id = mc.movie_id
      LEFT JOIN categories c ON mc.category_id = c.id
      WHERE m.title LIKE ?
      GROUP BY m.id
      ORDER BY m.created_at DESC
      LIMIT ?
    ";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('si', $k, $limit);
    $stmt->execute();
    return $stmt->get_result();
  }
}

// Lấy phim theo thể loại
if (!function_exists('getMoviesByCategory')) {
  function getMoviesByCategory($mysqli, $categoryId, $limit = 24) {
    $sql = "
      SELECT 
        m.*,
        GROUP_CONCAT(c.name SEPARATOR ', ') AS category_name
      FROM movies m
      JOIN movie_categories mc ON m.id = mc.movie_id
      JOIN categories c ON mc.category_id = c.id
      WHERE mc.category_id = ?
      GROUP BY m.id
      ORDER BY m.created_at DESC
      LIMIT ?
    ";
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

if (!function_exists('userHasWatchedMovie')) {
  function userHasWatchedMovie($mysqli, $userId, $movieId) {
    $stmt = $mysqli->prepare('SELECT 1 FROM watched WHERE user_id = ? AND movie_id = ? LIMIT 1');
    $stmt->bind_param('ii', $userId, $movieId);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return (bool)$res;
  }
}

if (!function_exists('userHasWatchedEpisode')) {
  function userHasWatchedEpisode($mysqli, $userId, $episodeId) {
    $stmt = $mysqli->prepare('SELECT 1 FROM watched WHERE user_id = ? AND episode_id = ? LIMIT 1');
    $stmt->bind_param('ii', $userId, $episodeId);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return (bool)$res;
  }
}

if (!function_exists('markWatched')) {
  function markWatched($mysqli, $userId, $movieId = null, $episodeId = null) {
    // upsert-like: INSERT IGNORE then skip
    $stmt = $mysqli->prepare('INSERT IGNORE INTO watched (user_id, movie_id, episode_id, watched_at) VALUES (?, ?, ?, NOW())');
    $stmt->bind_param('iii', $userId, $movieId, $episodeId);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
  }
}

if (!function_exists('unmarkWatched')) {
  function unmarkWatched($mysqli, $userId, $movieId = null, $episodeId = null) {
    if ($episodeId) {
      $stmt = $mysqli->prepare('DELETE FROM watched WHERE user_id = ? AND episode_id = ?');
      $stmt->bind_param('ii', $userId, $episodeId);
    } else {
      $stmt = $mysqli->prepare('DELETE FROM watched WHERE user_id = ? AND movie_id = ?');
      $stmt->bind_param('ii', $userId, $movieId);
    }
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
  }
}
?>

