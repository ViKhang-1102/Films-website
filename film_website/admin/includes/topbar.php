<nav class="admin-topbar navbar navbar-dark bg-black border-bottom border-secondary">
  <div class="container-fluid">
    <button class="btn btn-outline-secondary d-lg-none" id="sidebarToggle"><i class="fa-solid fa-bars"></i></button>
    <form class="d-none d-md-flex" role="search">
      <div class="input-group">
        <span class="input-group-text bg-dark text-secondary border-secondary"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input class="form-control bg-dark text-light border-secondary" type="search" placeholder="Tìm kiếm...">
      </div>
    </form>
    <div class="dropdown">
      <button class="btn btn-dark border border-secondary dropdown-toggle" data-bs-toggle="dropdown">
        <i class="fa-regular fa-circle-user text-warning me-2"></i><?php echo htmlspecialchars($_SESSION['user']['username'] ?? 'Admin'); ?>
      </button>
      <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
        <li><a class="dropdown-item" href="<?php echo BASE_PATH; ?>/admin/profile.php"><i class="fa-regular fa-id-badge me-2"></i>Hồ sơ</a></li>
        <li><a class="dropdown-item" href="<?php echo BASE_PATH; ?>/logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Đăng xuất</a></li>
      </ul>
    </div>
  </div>
</nav>

