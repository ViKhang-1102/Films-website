<?php require_once __DIR__ . '/config.php'; ?>
<?php require_once __DIR__ . '/functions.php'; ?>
<?php $__cats = getCategories($mysqli); ?>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-black border-bottom border-secondary sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-warning" href="/film_website/index.php">
                <i class="fa-solid fa-film"></i>
                FilmsZone+
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="/film_website/index.php">Home</a></li>
                    <?php if ($__cats && $__cats->num_rows > 0): ?>
                      <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="genreDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Thể loại</a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="genreDropdown">
                          <?php while($__c = $__cats->fetch_assoc()): ?>
                            <li><a class="dropdown-item" href="/film_website/category.php?id=<?php echo $__c['id']; ?>"><?php echo htmlspecialchars($__c['name']); ?></a></li>
                          <?php endwhile; ?>
                        </ul>
                      </li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="/film_website/category.php?type=single">Phim lẻ</a></li>
                    <li class="nav-item"><a class="nav-link" href="/film_website/category.php?type=series">Phim bộ</a></li>
                    <li class="nav-item"><a class="nav-link" href="#footer">Liên hệ</a></li>
                </ul>
                <form class="d-flex me-3" role="search" action="/film_website/search.php" method="get">
                    <div class="input-group">
                        <span class="input-group-text bg-dark text-secondary border-secondary"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input class="form-control bg-dark text-light border-secondary" type="search" name="q" placeholder="Tìm phim..." aria-label="Search">
                    </div>
                </form>
                <?php if (!isset($_SESSION['user'])): ?>
                  <a class="btn btn-warning fw-semibold" href="/film_website/login.php">
                    <i class="fa-solid fa-right-to-bracket me-2"></i>Đăng nhập
                  </a>
                <?php else: ?>
                  <div class="dropdown">
                    <button class="btn btn-dark border border-secondary dropdown-toggle text-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="fa-regular fa-circle-user me-2 text-warning"></i><?php echo htmlspecialchars($_SESSION['user']['username']); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                      <li><a class="dropdown-item" href="/film_website/profile.php"><i class="fa-regular fa-user me-2"></i>Hồ sơ</a></li>
                      <?php if (($_SESSION['user']['role'] ?? 'user') === 'admin'): ?>
                      <li><a class="dropdown-item" href="/film_website/admin/dashboard.php"><i class="fa-solid fa-gauge me-2"></i>Quản trị</a></li>
                      <?php endif; ?>
                      <li><hr class="dropdown-divider"></li>
                      <li><a class="dropdown-item" href="/film_website/logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Đăng xuất</a></li>
                    </ul>
                  </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

