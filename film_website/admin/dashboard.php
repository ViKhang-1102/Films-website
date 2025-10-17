<?php include __DIR__ . '/includes/header.php'; ?>
<div class="admin-layout">
<?php include __DIR__ . '/includes/sidebar.php'; ?>
<div>
<?php include __DIR__ . '/includes/topbar.php'; ?>

<main class="container-fluid my-4 my-md-5">
  <h3 class="section-title mb-4">Bảng điều khiển</h3>
  <div class="row g-3 g-md-4">
    <?php
      $moviesCount = $mysqli->query('SELECT COUNT(*) c FROM movies')->fetch_assoc()['c'] ?? 0;
      $usersCount = $mysqli->query('SELECT COUNT(*) c FROM users')->fetch_assoc()['c'] ?? 0;
      $catsCount = $mysqli->query('SELECT COUNT(*) c FROM categories')->fetch_assoc()['c'] ?? 0;
    ?>
    <div class="col-md-4">
      <div class="card bg-black border-secondary">
        <div class="card-body d-flex align-items-center gap-3">
          <i class="fa-solid fa-film text-warning fs-2"></i>
          <div>
            <div class="text-secondary">Tổng số phim</div>
            <div class="fs-4 fw-bold"><?php echo $moviesCount; ?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card bg-black border-secondary">
        <div class="card-body d-flex align-items-center gap-3">
          <i class="fa-regular fa-user text-warning fs-2"></i>
          <div>
            <div class="text-secondary">Người dùng</div>
            <div class="fs-4 fw-bold"><?php echo $usersCount; ?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card bg-black border-secondary">
        <div class="card-body d-flex align-items-center gap-3">
          <i class="fa-solid fa-layer-group text-warning fs-2"></i>
          <div>
            <div class="text-secondary">Thể loại</div>
            <div class="fs-4 fw-bold"><?php echo $catsCount; ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card bg-black border-secondary mt-4">
    <div class="card-header">5 phim mới nhất</div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-dark table-striped align-middle">
          <thead><tr><th>#</th><th>Tiêu đề</th><th>Thể loại</th><th>Năm</th><th>Video</th></tr></thead>
          <tbody>
            <?php
              $latest = $mysqli->query("
                SELECT 
                  m.*,
                  GROUP_CONCAT(c.name SEPARATOR ', ') AS category_name
                FROM movies m
                LEFT JOIN movie_categories mc ON m.id = mc.movie_id
                LEFT JOIN categories c ON mc.category_id = c.id
                GROUP BY m.id
                ORDER BY m.created_at DESC
                LIMIT 5
              ");
              $i=1; while($m=$latest->fetch_assoc()):
            ?>
            <tr>
              <td><?php echo $i++; ?></td>
              <td><?php echo htmlspecialchars($m['title']); ?></td>
              <td class="text-secondary small"><?php echo htmlspecialchars($m['category_name'] ?? ''); ?></td>
              <td><?php echo htmlspecialchars($m['year']); ?></td>
              <td class="small"><a class="text-warning" target="_blank" href="<?php echo htmlspecialchars($m['video_url']); ?>">Mở</a></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</div>
</div>

