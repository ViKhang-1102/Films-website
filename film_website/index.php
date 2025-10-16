<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/navbar.php'; ?>
<?php require_once __DIR__ . '/includes/config.php'; ?>
<?php require_once __DIR__ . '/includes/functions.php'; ?>

<main>
  <!-- Banner Carousel -->
  <section class="banner-carousel position-relative">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
      </div>
      <div class="carousel-inner rounded-3 overflow-hidden">
        <div class="carousel-item active">
          <img src="https://images.unsplash.com/photo-1485846234645-a62644f84728?q=80&w=1600&auto=format&fit=crop" class="d-block w-100" alt="Banner 1">
          <div class="banner-overlay"></div>
          <div class="banner-caption">
            <h2 class="fw-bold">Bom tấn hành động 2025</h2>
            <p class="text-secondary">Nhịp độ dồn dập, kỹ xảo mãn nhãn. Sẵn sàng bùng nổ cảm xúc.</p>
            <a href="/film_website/movie.php" class="btn btn-warning btn-lg"><i class="fa-solid fa-play me-2"></i>Xem ngay</a>
          </div>
        </div>
        <div class="carousel-item">
          <img src="https://images.unsplash.com/photo-1495566650546-d9e0f2f4e6c9?q=80&w=1600&auto=format&fit=crop" class="d-block w-100" alt="Banner 2">
          <div class="banner-overlay"></div>
          <div class="banner-caption">
            <h2 class="fw-bold">Tình yêu và hi vọng</h2>
            <p class="text-secondary">Câu chuyện chạm đến trái tim, đưa bạn qua mọi cung bậc cảm xúc.</p>
            <a href="/film_website/movie.php" class="btn btn-warning btn-lg"><i class="fa-solid fa-play me-2"></i>Xem ngay</a>
          </div>
        </div>
        <div class="carousel-item">
          <img src="https://images.unsplash.com/photo-1440404653325-ab127d49abc1?q=80&w=1600&auto=format&fit=crop" class="d-block w-100" alt="Banner 3">
          <div class="banner-overlay"></div>
          <div class="banner-caption">
            <h2 class="fw-bold">Hành trình viễn tưởng</h2>
            <p class="text-secondary">Bước vào vũ trụ rộng lớn với những cuộc phiêu lưu kỳ thú.</p>
            <a href="/film_website/movie.php" class="btn btn-warning btn-lg"><i class="fa-solid fa-play me-2"></i>Xem ngay</a>
          </div>
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </section>

  <!-- Recommended Movies -->
  <section class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="section-title m-0">Phim đề xuất</h3>
      <a href="/film_website/category.php" class="text-warning">Xem tất cả <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="row g-3 g-md-4">
      <?php $movies = getMovies($mysqli, 8); while($m = $movies->fetch_assoc()): ?>
      <div class="col-6 col-md-3">
        <div class="card movie-card h-100">
          <a href="/film_website/movie.php?id=<?php echo $m['id']; ?>" class="text-decoration-none text-light">
            <div class="movie-thumb">
              <img src="<?php echo htmlspecialchars($m['thumbnail'] ?: 'https://picsum.photos/400/600'); ?>" alt="<?php echo htmlspecialchars($m['title']); ?>">
            </div>
            <div class="card-body">
              <h6 class="card-title"><?php echo htmlspecialchars($m['title']); ?></h6>
              <div class="movie-meta d-flex justify-content-between align-items-center">
                <span><i class="fa-regular fa-calendar me-1"></i><?php echo htmlspecialchars($m['year']); ?></span>
                <span class="badge text-bg-dark border border-secondary"><i class="fa-regular fa-clock me-1"></i>120'</span>
              </div>
              <div class="mt-3 d-grid">
                <span class="btn btn-warning btn-sm">Xem ngay</span>
              </div>
            </div>
          </a>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

