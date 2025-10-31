<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/navbar.php'; ?>
<?php require __DIR__ . '/includes/config.php'; ?>
<?php require __DIR__ . '/includes/functions.php'; ?>

<?php $q = isset($_GET['q']) ? sanitize($_GET['q']) : ''; ?>

<main class="container my-4 my-md-5">
  <h3 class="section-title"><?php echo $q ? 'Kết quả tìm kiếm cho: "' . $q . '"' : 'Tất cả phim'; ?></h3>
  <?php $results = $q ? searchMovies($mysqli, $q, 48) : getMovies($mysqli, 48); ?>
  <p class="text-secondary">Tìm thấy <strong><?php echo $results->num_rows; ?></strong> kết quả phù hợp.</p>

  <div class="row g-3 g-md-4">
    <?php while($m=$results->fetch_assoc()): ?>
    <div class="col-6 col-md-3">
      <div class="card movie-card h-100">
        <a href="/movie.php?id=<?php echo $m['id']; ?>" class="text-decoration-none text-light">
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
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

