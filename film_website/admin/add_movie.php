<?php include __DIR__ . '/includes/header.php'; ?>
<div class="admin-layout">
<?php include __DIR__ . '/includes/sidebar.php'; ?>
<div>
<?php include __DIR__ . '/includes/topbar.php'; ?>

<?php
$cats = getCategories($mysqli);
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = sanitize($_POST['title'] ?? '');
  $description = sanitize($_POST['description'] ?? '');
  $category_id = (int)($_POST['category_id'] ?? 0);
  $year = (int)($_POST['year'] ?? 0);
  $duration = sanitize($_POST['duration'] ?? '');
  $thumbnail = sanitize($_POST['thumbnail'] ?? '');
  $video_url = sanitize($_POST['video_url'] ?? '');

  // Chuyển link Google Drive sang dạng uc?id=
  if (preg_match('#https?://drive\.google\.com/file/d/([^/]+)/view#', $video_url, $m)) {
    $video_url = 'https://drive.google.com/uc?id=' . $m[1];
  }

  $stmt = $mysqli->prepare('INSERT INTO movies (title, description, category_id, thumbnail, year, duration, video_url) VALUES (?,?,?,?,?,?,?)');
  $stmt->bind_param('ssissis', $title, $description, $category_id, $thumbnail, $year, $duration, $video_url);
  $msg = $stmt->execute() ? 'Thêm phim thành công!' : 'Thêm phim thất bại!';
}
?>

<main class="container-fluid my-4 my-md-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="section-title m-0">Thêm phim</h3>
    <a class="btn btn-outline-warning" href="<?php echo BASE_PATH; ?>/admin/manage_movies.php">Quay lại</a>
  </div>

  <?php if (!empty($msg)): ?>
    <div class="alert <?php echo $msg==='Thêm phim thành công!' ? 'alert-success' : 'alert-danger'; ?>"><?php echo $msg; ?></div>
  <?php endif; ?>

  <div class="card bg-black border-secondary">
    <div class="card-body">
      <form method="post" action="">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Tiêu đề</label>
            <input name="title" class="form-control bg-dark text-light border-secondary" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Thể loại</label>
            <select name="category_id" class="form-select bg-dark text-light border-secondary" required>
              <option value="">-- chọn --</option>
              <?php while($c=$cats->fetch_assoc()): ?>
                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Năm</label>
            <input name="year" type="number" min="1900" max="2099" class="form-control bg-dark text-light border-secondary">
          </div>
          <div class="col-12">
            <label class="form-label">Mô tả</label>
            <textarea name="description" rows="4" class="form-control bg-dark text-light border-secondary"></textarea>
          </div>
          <div class="col-md-4">
            <label class="form-label">Thời lượng</label>
            <input name="duration" class="form-control bg-dark text-light border-secondary" placeholder="120 phút">
          </div>
          <div class="col-md-4">
            <label class="form-label">Ảnh thumbnail (URL)</label>
            <input name="thumbnail" class="form-control bg-dark text-light border-secondary" placeholder="https://...">
          </div>
          <div class="col-md-4">
            <label class="form-label">Link video (Drive/URL)</label>
            <input name="video_url" class="form-control bg-dark text-light border-secondary" placeholder="https://drive.google.com/file/d/ID/view?... hoặc URL">
          </div>
        </div>
        <div class="mt-3 d-flex gap-2 align-items-center">
          <button class="btn btn-warning" type="submit">Lưu</button>
          <small class="text-secondary">Hệ thống tự chuyển link Drive sang dạng nhúng.</small>
        </div>
      </form>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</div>
</div>

