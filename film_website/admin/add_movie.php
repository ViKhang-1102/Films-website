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
  $year = (int)($_POST['year'] ?? 0);
  $duration = sanitize($_POST['duration'] ?? '');
  $thumbnail = sanitize($_POST['thumbnail'] ?? '');
  $video_url = sanitize($_POST['video_url'] ?? ''); // <-- thêm
  $categories = $_POST['categories'] ?? [];
  $episodes = $_POST['episodes'] ?? [];

  // Bắt đầu transaction
  $mysqli->begin_transaction();
  
  try {
    // Thêm phim (bổ sung video_url)
    $stmt = $mysqli->prepare('INSERT INTO movies (title, description, thumbnail, year, duration, video_url) VALUES (?,?,?,?,?,?)');
    $stmt->bind_param('sssiss', $title, $description, $thumbnail, $year, $duration, $video_url);
    $stmt->execute();
    $movie_id = $mysqli->insert_id;
    $stmt->close();

    // Thêm các thể loại
    if (!empty($categories)) {
      $stmt = $mysqli->prepare('INSERT INTO movie_categories (movie_id, category_id) VALUES (?, ?)');
      foreach ($categories as $category_id) {
        $category_id = (int)$category_id;
        if ($category_id > 0) {
          $stmt->bind_param('ii', $movie_id, $category_id);
          $stmt->execute();
        }
      }
      $stmt->close();
    }

    // Thêm các tập phim
    if (!empty($episodes)) {
      $stmt = $mysqli->prepare('INSERT INTO episodes (movie_id, title, episode_number, description, video_url, duration) VALUES (?, ?, ?, ?, ?, ?)');
      foreach ($episodes as $episode) {
        if (!empty($episode['title']) && !empty($episode['episode_number'])) {
          $episode_title = sanitize($episode['title']);
          $episode_number = (int)$episode['episode_number'];
          $episode_description = sanitize($episode['description'] ?? '');
          $episode_video_url = sanitize($episode['video_url'] ?? '');
          $episode_duration = sanitize($episode['duration'] ?? '');
          
          // Chuyển link Google Drive sang dạng uc?id=
          if (preg_match('#https?://drive\.google\.com/file/d/([^/]+)/view#', $episode_video_url, $m)) {
            $episode_video_url = 'https://drive.google.com/uc?id=' . $m[1];
          }
          
          $stmt->bind_param('isisss', $movie_id, $episode_title, $episode_number, $episode_description, $episode_video_url, $episode_duration);
          $stmt->execute();
        }
      }
      $stmt->close();
    }

    $mysqli->commit();
    $msg = 'Thêm phim thành công!';
  } catch (Exception $e) {
    $mysqli->rollback();
    $msg = 'Thêm phim thất bại: ' . $e->getMessage();
  }
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
      <form method="post" action="" id="movieForm">
        <!-- Thông tin cơ bản -->
        <div class="row g-3 mb-4">
          <div class="col-12">
            <h5 class="text-warning mb-3"><i class="fa-solid fa-film me-2"></i>Thông tin cơ bản</h5>
          </div>
          <div class="col-md-6">
            <label class="form-label">Tên phim <span class="text-danger">*</span></label>
            <input name="title" class="form-control bg-dark text-light border-secondary" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Năm</label>
            <input name="year" type="number" min="1900" max="2099" class="form-control bg-dark text-light border-secondary">
          </div>
          <div class="col-md-3">
            <label class="form-label">Thời lượng</label>
            <input name="duration" class="form-control bg-dark text-light border-secondary" placeholder="120 phút">
          </div>
          <div class="col-12">
            <label class="form-label">Mô tả phim</label>
            <textarea name="description" rows="4" class="form-control bg-dark text-light border-secondary" placeholder="Mô tả ngắn về nội dung phim..."></textarea>
          </div>
          <div class="col-12">
            <label class="form-label">Ảnh poster (URL)</label>
            <input name="thumbnail" class="form-control bg-dark text-light border-secondary" placeholder="https://...">
          </div>

          <div class="col-12">
            <label class="form-label">Link video (YouTube hoặc Google Drive)</label>
            <input name="video_url" class="form-control bg-dark text-light border-secondary" placeholder="https://youtu.be/... hoặc https://drive.google.com/file/d/ID/...">
            <div class="form-text text-muted">Hệ thống sẽ tự nhận diện YouTube hoặc Google Drive và nhúng trong trang phim.</div>
          </div>
        </div>

        <!-- Thể loại -->
        <div class="row g-3 mb-4">
          <div class="col-12">
            <h5 class="text-warning mb-3"><i class="fa-solid fa-tags me-2"></i>Thể loại</h5>
            <div class="row">
              <?php while($c=$cats->fetch_assoc()): ?>
              <div class="col-md-3 col-sm-4 col-6 mb-2">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="categories[]" value="<?php echo $c['id']; ?>" id="cat_<?php echo $c['id']; ?>">
                  <label class="form-check-label text-light" for="cat_<?php echo $c['id']; ?>">
                    <?php echo htmlspecialchars($c['name']); ?>
                  </label>
                </div>
              </div>
              <?php endwhile; ?>
            </div>
          </div>
        </div>

        <!-- Tập phim -->
        <div class="row g-3 mb-4">
          <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="text-warning mb-0"><i class="fa-solid fa-list me-2"></i>Tập phim</h5>
              <button type="button" class="btn btn-outline-success btn-sm" id="addEpisode">
                <i class="fa-solid fa-plus me-1"></i>Thêm tập mới
              </button>
            </div>
            <div id="episodesContainer">
              <!-- Tập phim sẽ được thêm động ở đây -->
            </div>
          </div>
        </div>

        <div class="mt-4 d-flex gap-2 align-items-center">
          <button class="btn btn-warning" type="submit">
            <i class="fa-solid fa-save me-2"></i>Lưu phim
          </button>
          <small class="text-secondary">Hệ thống tự chuyển link Drive sang dạng nhúng.</small>
        </div>
      </form>
    </div>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
  let episodeCount = 0;
  
  // Thêm tập phim mới
  document.getElementById('addEpisode').addEventListener('click', function() {
    episodeCount++;
    const container = document.getElementById('episodesContainer');
    
    const episodeHtml = `
      <div class="card bg-dark border-secondary mb-3 episode-item" data-episode="${episodeCount}">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="mb-0 text-warning">Tập ${episodeCount}</h6>
          <button type="button" class="btn btn-sm btn-outline-danger remove-episode">
            <i class="fa-solid fa-trash"></i>
          </button>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Tên tập</label>
              <input type="text" name="episodes[${episodeCount}][title]" class="form-control bg-dark text-light border-secondary" placeholder="Tập 1: Khởi đầu">
            </div>
            <div class="col-md-3">
              <label class="form-label">Số tập</label>
              <input type="number" name="episodes[${episodeCount}][episode_number]" class="form-control bg-dark text-light border-secondary" value="${episodeCount}" min="1">
            </div>
            <div class="col-md-3">
              <label class="form-label">Thời lượng</label>
              <input type="text" name="episodes[${episodeCount}][duration]" class="form-control bg-dark text-light border-secondary" placeholder="45 phút">
            </div>
            <div class="col-12">
              <label class="form-label">Mô tả ngắn</label>
              <textarea name="episodes[${episodeCount}][description]" rows="2" class="form-control bg-dark text-light border-secondary" placeholder="Mô tả ngắn về tập này..."></textarea>
            </div>
            <div class="col-12">
              <label class="form-label">Link video</label>
              <input type="url" name="episodes[${episodeCount}][video_url]" class="form-control bg-dark text-light border-secondary" placeholder="https://drive.google.com/file/d/ID/view?... hoặc URL">
            </div>
          </div>
        </div>
      </div>
    `;
    
    container.insertAdjacentHTML('beforeend', episodeHtml);
  });
  
  // Xóa tập phim
  document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-episode')) {
      e.target.closest('.episode-item').remove();
    }
  });
  
  // Thêm tập đầu tiên mặc định
  document.getElementById('addEpisode').click();
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
</div>
</div>

