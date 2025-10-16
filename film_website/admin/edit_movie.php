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

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = sanitize($_POST['title'] ?? '');
  $description = sanitize($_POST['description'] ?? '');
  $category_id = (int)($_POST['category_id'] ?? 0);
  $year = (int)($_POST['year'] ?? 0);
  $duration = sanitize($_POST['duration'] ?? '');
  $thumbnail = sanitize($_POST['thumbnail'] ?? '');
  $video_url = sanitize($_POST['video_url'] ?? '');

  if (preg_match('#https?://drive\.google\.com/file/d/([^/]+)/view#', $video_url, $m)) {
    $video_url = 'https://drive.google.com/uc?id=' . $m[1];
  }

  $update = $mysqli->prepare('UPDATE movies SET title=?, description=?, category_id=?, thumbnail=?, year=?, duration=?, video_url=? WHERE id=?');
  $update->bind_param('ssissisi', $title, $description, $category_id, $thumbnail, $year, $duration, $video_url, $id);
  $msg = $update->execute() ? 'Cập nhật thành công!' : 'Cập nhật thất bại!';
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
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Tiêu đề</label>
                <input name="title" class="form-control bg-dark text-light border-secondary" value="<?php echo htmlspecialchars($movie['title']); ?>" required>
              </div>
              <div class="col-md-3">
                <label class="form-label">Thể loại</label>
                <select name="category_id" class="form-select bg-dark text-light border-secondary" required>
                  <?php while($c=$cats->fetch_assoc()): ?>
                  <option value="<?php echo $c['id']; ?>" <?php echo (int)$movie['category_id']===(int)$c['id']?'selected':''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                  <?php endwhile; ?>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Năm</label>
                <input name="year" type="number" min="1900" max="2099" class="form-control bg-dark text-light border-secondary" value="<?php echo htmlspecialchars($movie['year']); ?>">
              </div>
              <div class="col-12">
                <label class="form-label">Mô tả</label>
                <textarea name="description" rows="4" class="form-control bg-dark text-light border-secondary"><?php echo htmlspecialchars($movie['description']); ?></textarea>
              </div>
              <div class="col-md-4">
                <label class="form-label">Thời lượng</label>
                <input name="duration" class="form-control bg-dark text-light border-secondary" value="<?php echo htmlspecialchars($movie['duration']); ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label">Ảnh thumbnail (URL)</label>
                <input name="thumbnail" class="form-control bg-dark text-light border-secondary" value="<?php echo htmlspecialchars($movie['thumbnail']); ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label">Link video (Drive/URL)</label>
                <input name="video_url" class="form-control bg-dark text-light border-secondary" value="<?php echo htmlspecialchars($movie['video_url']); ?>">
              </div>
            </div>
            <div class="mt-3">
              <button class="btn btn-warning" type="submit">Lưu thay đổi</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card bg-black border-secondary">
        <div class="card-header">Preview</div>
        <div class="card-body">
          <div class="mb-3">
            <img class="w-100 rounded" style="aspect-ratio: 2/3; object-fit: cover;" src="<?php echo htmlspecialchars($movie['thumbnail']); ?>" alt="thumb">
          </div>
          <?php if (!empty($movie['video_url'])): ?>
          <div class="ratio ratio-16x9">
            <iframe src="<?php echo htmlspecialchars($movie['video_url']); ?>" allowfullscreen></iframe>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</div>
</div>

