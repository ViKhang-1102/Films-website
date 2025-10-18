<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';

$type = $_GET['type'] ?? '';        // 'single' hoặc 'series'
$categoryId = (int)($_GET['id'] ?? 0);

$whereClause = '';
$params = [];
$types = ''; // bind types string for mysqli

// --- NEW: load thông tin thể loại nếu có id (để $currentCat không undefined)
$currentCat = null;
if ($categoryId > 0) {
  $stmtCat = $mysqli->prepare('SELECT * FROM categories WHERE id = ? LIMIT 1');
  $stmtCat->bind_param('i', $categoryId);
  $stmtCat->execute();
  $currentCat = $stmtCat->get_result()->fetch_assoc();
  $stmtCat->close();

  $whereClause = 'WHERE mc.category_id = ?';
  $params[] = $categoryId;
  $types .= 'i';
}

$having = '';
if ($type === 'single') {
  // phim không có tập nào
  $having = 'HAVING COUNT(DISTINCT e.id) = 0';
} elseif ($type === 'series') {
  // phim có 2 tập trở lên (bạn có thể đổi >=1 nếu muốn)
  $having = 'HAVING COUNT(DISTINCT e.id) >= 2';
}

// SQL tổng hợp categories + đếm episode (DISTINCT để tránh nhân bản do nhiều thể loại)
$sql = "
  SELECT
    m.*,
    GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') AS category_name,
    COUNT(DISTINCT e.id) AS episode_count
  FROM movies m
  LEFT JOIN movie_categories mc ON m.id = mc.movie_id
  LEFT JOIN categories c ON mc.category_id = c.id
  LEFT JOIN episodes e ON m.id = e.movie_id
  {$whereClause}
  GROUP BY m.id
  {$having}
  ORDER BY m.created_at DESC
";

// prepare + bind nếu cần
if ($whereClause) {
  $stmt = $mysqli->prepare($sql);
  $stmt->bind_param($types, ...$params);
  $stmt->execute();
  $movies = $stmt->get_result();
  $stmt->close();
} else {
  $movies = $mysqli->query($sql);
}
?>

<main class="container my-4 my-md-5">
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <h3 class="section-title m-0"><?php echo $currentCat ? 'Thể loại: ' . htmlspecialchars($currentCat['name']) : 'Tất cả phim'; ?></h3>
    <div class="d-flex gap-2">
      <select class="form-select form-select-sm bg-dark text-light border-secondary">
        <option>Mới nhất</option>
        <option>Đánh giá cao</option>
        <option>Xem nhiều</option>
      </select>
      <select class="form-select form-select-sm bg-dark text-light border-secondary">
        <option>Tất cả năm</option>
        <option>2025</option>
        <option>2024</option>
        <option>2023</option>
      </select>
    </div>
  </div>

  <div class="row g-3 g-md-4">
    <?php while($m=$movies->fetch_assoc()): ?>
    <div class="col-6 col-md-4 col-lg-3">
      <div class="card movie-card h-100">
        <a href="/film_website/movie.php?id=<?php echo $m['id']; ?>" class="text-decoration-none text-light">
          <div class="movie-thumb">
            <img src="<?php echo htmlspecialchars($m['thumbnail'] ?: 'https://picsum.photos/400/600'); ?>" alt="<?php echo htmlspecialchars($m['title']); ?>">
          </div>
          <div class="card-body">
            <h6 class="card-title"><?php echo htmlspecialchars($m['title']); ?></h6>
            <div class="movie-meta d-flex justify-content-between align-items-center">
              <span><i class="fa-regular fa-calendar me-1"></i><?php echo htmlspecialchars($m['year']); ?></span>
              <span class="badge text-bg-dark border border-secondary"><?php echo htmlspecialchars($m['category_name'] ?? ''); ?></span>
            </div>
          </div>
        </a>
      </div>
    </div>
    <?php endwhile; ?>
  </div>

  <nav class="mt-4" aria-label="Pagination">
    <ul class="pagination pagination-sm justify-content-center">
      <li class="page-item disabled"><a class="page-link bg-dark text-secondary border-secondary" href="#">Trước</a></li>
      <li class="page-item"><a class="page-link bg-dark text-light border-secondary" href="#">1</a></li>
      <li class="page-item active"><a class="page-link bg-warning border-warning text-dark" href="#">2</a></li>
      <li class="page-item"><a class="page-link bg-dark text-light border-secondary" href="#">3</a></li>
      <li class="page-item"><a class="page-link bg-dark text-light border-secondary" href="#">Sau</a></li>
    </ul>
  </nav>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

