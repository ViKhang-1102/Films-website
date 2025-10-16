<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/navbar.php'; ?>
<?php require __DIR__ . '/includes/config.php'; ?>
<?php require __DIR__ . '/includes/functions.php'; ?>
<?php require __DIR__ . '/includes/auth.php'; ?>
<?php requireLogin(); ?>

<main class="container my-4 my-md-5">
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card bg-black border-secondary">
        <div class="card-body text-center">
          <img class="avatar mb-3" src="https://i.pravatar.cc/150?img=5" alt="Avatar">
          <h5 class="mb-1"><?php echo htmlspecialchars($_SESSION['user']['username']); ?></h5>
          <p class="text-secondary small mb-3"><i class="fa-regular fa-envelope me-1"></i><?php echo htmlspecialchars($_SESSION['user']['email']); ?></p>
          <div class="d-flex justify-content-center gap-2">
            <span class="badge text-bg-dark border border-secondary">Thành viên</span>
            <span class="badge text-bg-dark border border-secondary">VIP</span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="card bg-black border-secondary">
        <div class="card-header d-flex justify-content-between align-items-center">
          <strong>Lịch sử đã xem</strong>
          <a href="#" class="text-warning small">Xem tất cả</a>
        </div>
        <div class="card-body">
          <div class="row g-3 g-md-4">
            <?php $favs = getUserFavorites($mysqli, (int)$_SESSION['user']['id']); if ($favs->num_rows === 0): ?>
              <div class="col-12"><div class="alert alert-secondary">Chưa có phim yêu thích.</div></div>
            <?php endif; while($m=$favs->fetch_assoc()): ?>
            <div class="col-6 col-lg-4">
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
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

