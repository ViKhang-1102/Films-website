<?php include __DIR__ . '/includes/header.php'; ?>
<div class="admin-layout">
<?php include __DIR__ . '/includes/sidebar.php'; ?>
<div>
<?php include __DIR__ . '/includes/topbar.php'; ?>

<?php
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: ' . BASE_PATH . '/admin/manage_movies.php'); exit; }

$cats = getCategories($mysqli);

$stmt = $mysqli->prepare('SELECT * FROM movies WHERE id=?');
$stmt->bind_param('i', $id);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();
if (!$movie) { header('Location: ' . BASE_PATH . '/admin/manage_movies.php'); exit; }

// Lấy các thể loại hiện tại của phim
$stmt = $mysqli->prepare('SELECT category_id FROM movie_categories WHERE movie_id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$current_categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$current_category_ids = array_column($current_categories, 'category_id');
$stmt->close();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = sanitize($_POST['title'] ?? '');
  $description = sanitize($_POST['description'] ?? '');
  $year = (int)($_POST['year'] ?? 0);
  $duration = sanitize($_POST['duration'] ?? '');
  $thumbnail = sanitize($_POST['thumbnail'] ?? '');
  $categories = $_POST['categories'] ?? [];

  // Bắt đầu transaction
  $mysqli->begin_transaction();
  
  try {
    // Cập nhật thông tin phim
    $stmt = $mysqli->prepare('UPDATE movies SET title=?, description=?, thumbnail=?, year=?, duration=? WHERE id=?');
    $stmt->bind_param('sssisi', $title, $description, $thumbnail, $year, $duration, $id);
    $stmt->execute();
    $stmt->close();

    // Xóa các thể loại cũ
    $stmt = $mysqli->prepare('DELETE FROM movie_categories WHERE movie_id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    // Thêm các thể loại mới
    if (!empty($categories)) {
      $stmt = $mysqli->prepare('INSERT INTO movie_categories (movie_id, category_id) VALUES (?, ?)');
      foreach ($categories as $category_id) {
        $category_id = (int)$category_id;
        if ($category_id > 0) {
          $stmt->bind_param('ii', $id, $category_id);
          $stmt->execute();
        }
      }
      $stmt->close();
    }

    $mysqli->commit();
    $msg = 'Cập nhật phim thành công!';
  } catch (Exception $e) {
    $mysqli->rollback();
    $msg = 'Cập nhật phim thất bại: ' . $e->getMessage();
  }
}
?>

<main class="container-fluid my-4 my-md-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="section-title m-0">Sửa phim</h3>
    <a class="btn btn-outline-warning" href="<?php echo BASE_PATH; ?>/admin/manage_movies.php">Quay lại</a>
  </div>

  <?php if (!empty($msg)): ?>
    <div class="alert <?php echo $msg==='Cập nhật thành công!' ? 'alert-success' : 'alert-danger'; ?>"><?php echo $msg; ?></div>
  <?php endif; ?>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card bg-black border-secondary">
        <div class="card-body">
          <form method="post" action="">
            <!-- Thông tin cơ bản -->
            <div class="row g-3 mb-4">
              <div class="col-12">
                <h5 class="text-warning mb-3"><i class="fa-solid fa-film me-2"></i>Thông tin cơ bản</h5>
              </div>
              <div class="col-md-6">
                <label class="form-label">Tên phim <span class="text-danger">*</span></label>
                <input name="title" class="form-control bg-dark text-light border-secondary" value="<?php echo htmlspecialchars($movie['title']); ?>" required>
              </div>
              <div class="col-md-3">
                <label class="form-label">Năm</label>
                <input name="year" type="number" min="1900" max="2099" class="form-control bg-dark text-light border-secondary" value="<?php echo htmlspecialchars($movie['year']); ?>">
              </div>
              <div class="col-md-3">
                <label class="form-label">Thời lượng</label>
                <input name="duration" class="form-control bg-dark text-light border-secondary" value="<?php echo htmlspecialchars($movie['duration']); ?>">
              </div>
              <div class="col-12">
                <label class="form-label">Mô tả phim</label>
                <textarea name="description" rows="4" class="form-control bg-dark text-light border-secondary"><?php echo htmlspecialchars($movie['description']); ?></textarea>
              </div>
              <div class="col-12">
                <label class="form-label">Ảnh poster (URL)</label>
                <input name="thumbnail" class="form-control bg-dark text-light border-secondary" value="<?php echo htmlspecialchars($movie['thumbnail']); ?>">
              </div>
            </div>

            <!-- Thể loại -->
            <div class="row g-3 mb-4">
              <div class="col-12">
                <h5 class="text-warning mb-3"><i class="fa-solid fa-tags me-2"></i>Thể loại</h5>
                <div class="row">
                  <?php 
                  // Reset pointer để lặp lại
                  $cats->data_seek(0);
                  while($c=$cats->fetch_assoc()): 
                  ?>
                  <div class="col-md-3 col-sm-4 col-6 mb-2">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="categories[]" value="<?php echo $c['id']; ?>" id="cat_<?php echo $c['id']; ?>" 
                             <?php echo in_array($c['id'], $current_category_ids) ? 'checked' : ''; ?>>
                      <label class="form-check-label text-light" for="cat_<?php echo $c['id']; ?>">
                        <?php echo htmlspecialchars($c['name']); ?>
                      </label>
                    </div>
                  </div>
                  <?php endwhile; ?>
                </div>
              </div>
            </div>

            <div class="mt-4 d-flex gap-2 align-items-center">
              <button class="btn btn-warning" type="submit">
                <i class="fa-solid fa-save me-2"></i>Lưu thay đổi
              </button>
              <a class="btn btn-outline-info" href="<?php echo BASE_PATH; ?>/admin/manage_episodes.php?movie_id=<?php echo $id; ?>">
                <i class="fa-solid fa-list me-1"></i>Quản lý tập phim
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card bg-black border-secondary">
        <div class="card-header">
          <h6 class="mb-0"><i class="fa-solid fa-eye me-2"></i>Preview</h6>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <img class="w-100 rounded" style="aspect-ratio: 2/3; object-fit: cover;" 
                 src="<?php echo htmlspecialchars($movie['thumbnail'] ?: 'https://picsum.photos/300/450'); ?>" 
                 alt="Poster phim">
          </div>
          <h6 class="text-warning"><?php echo htmlspecialchars($movie['title']); ?></h6>
          <p class="text-secondary small"><?php echo htmlspecialchars($movie['description']); ?></p>
          
          <!-- Hiển thị các thể loại hiện tại -->
          <div class="mb-3">
            <small class="text-muted">Thể loại hiện tại:</small><br>
            <?php if (!empty($current_category_ids)): ?>
              <?php 
              $stmt = $mysqli->prepare('SELECT name FROM categories WHERE id IN (' . implode(',', array_fill(0, count($current_category_ids), '?')) . ')');
              $stmt->bind_param(str_repeat('i', count($current_category_ids)), ...$current_category_ids);
              $stmt->execute();
              $category_names = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
              $stmt->close();
              ?>
              <?php foreach ($category_names as $cat): ?>
                <span class="badge bg-secondary me-1 mb-1"><?php echo htmlspecialchars($cat['name']); ?></span>
              <?php endforeach; ?>
            <?php else: ?>
              <span class="text-muted">Chưa phân loại</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</div>
</div>

