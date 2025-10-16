<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/navbar.php'; ?>
<?php require __DIR__ . '/includes/config.php'; ?>
<?php require __DIR__ . '/includes/functions.php'; ?>

<?php
$id = (int)($_GET['id'] ?? 0);
$movie = $id ? getMovieById($mysqli, $id) : null;
if (!$movie) {
  echo '<div class="container my-5"><div class="alert alert-warning">Phim không tồn tại.</div></div>';
  include __DIR__ . '/includes/footer.php';
  exit;
}
?>

<main class="container my-4 my-md-5">
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card movie-card">
        <div class="movie-thumb">
          <img src="<?php echo htmlspecialchars($movie['thumbnail'] ?: 'https://picsum.photos/500/750'); ?>" alt="Poster phim">
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <h2 class="fw-bold"><?php echo htmlspecialchars($movie['title']); ?></h2>
      <p class="text-secondary"><?php echo nl2br(htmlspecialchars($movie['description'] ?? '')); ?></p>

      <div class="row g-3 small">
        <div class="col-sm-6 col-lg-4"><span class="text-secondary">Thời lượng:</span> <strong><?php echo htmlspecialchars($movie['duration'] ?: '—'); ?></strong></div>
        <div class="col-sm-6 col-lg-4"><span class="text-secondary">Năm:</span> <strong><?php echo htmlspecialchars($movie['year'] ?: '—'); ?></strong></div>
        <div class="col-sm-6 col-lg-4"><span class="text-secondary">Thể loại:</span> <span class="badge text-bg-dark border border-secondary"><?php echo htmlspecialchars($movie['category_name'] ?? '—'); ?></span></div>
        <div class="col-sm-6 col-lg-4"><span class="text-secondary">Đạo diễn:</span> <strong>Nguyễn Văn A</strong></div>
        <div class="col-sm-6 col-lg-4"><span class="text-secondary">Diễn viên:</span> <strong>Trần B, Lê C</strong></div>
        <div class="col-sm-6 col-lg-4"><span class="text-secondary">Quốc gia:</span> <strong>Mỹ</strong></div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <a href="<?php echo htmlspecialchars($movie['video_url'] ?: '#'); ?>" class="btn btn-warning"><i class="fa-solid fa-play me-2"></i>Xem ngay</a>
        <?php if (isset($_SESSION['user'])): ?>
          <?php $fav = userHasFavorite($mysqli, (int)$_SESSION['user']['id'], (int)$movie['id']); ?>
          <form method="post" action="<?php echo BASE_PATH . '/favorite.php'; ?>">
            <input type="hidden" name="movie_id" value="<?php echo (int)$movie['id']; ?>">
            <input type="hidden" name="redirect" value="<?php echo BASE_PATH . '/movie.php?id=' . (int)$movie['id']; ?>">
            <input type="hidden" name="action" value="<?php echo $fav ? 'remove' : 'add'; ?>">
            <button class="btn <?php echo $fav ? 'btn-warning' : 'btn-outline-warning'; ?>" type="submit">
              <i class="fa-regular fa-bookmark me-2"></i><?php echo $fav ? 'Bỏ yêu thích' : 'Lưu yêu thích'; ?>
            </button>
          </form>
        <?php else: ?>
          <a class="btn btn-outline-warning" href="<?php echo BASE_PATH . '/login.php'; ?>"><i class="fa-regular fa-bookmark me-2"></i>Đăng nhập để lưu</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Gợi ý phim tương tự -->
  <div class="mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="section-title m-0">Phim tương tự</h4>
      <a href="/film_website/category.php" class="text-warning">Xem thêm <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="row g-3 g-md-4">
      <?php $similar = getMoviesByCategory($mysqli, (int)($movie['category_id'] ?? 0), 6); while($m=$similar->fetch_assoc()): if ((int)$m['id']===(int)$movie['id']) continue; ?>
      <div class="col-6 col-md-4 col-lg-2">
        <div class="card movie-card h-100">
          <a href="/film_website/movie.php?id=<?php echo $m['id']; ?>" class="text-decoration-none text-light">
            <div class="movie-thumb">
              <img src="<?php echo htmlspecialchars($m['thumbnail'] ?: 'https://picsum.photos/400/600'); ?>" alt="<?php echo htmlspecialchars($m['title']); ?>">
            </div>
            <div class="card-body py-3">
              <h6 class="card-title mb-1"><?php echo htmlspecialchars($m['title']); ?></h6>
              <div class="movie-meta"><i class="fa-regular fa-calendar me-1"></i><?php echo htmlspecialchars($m['year']); ?></div>
            </div>
          </a>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

