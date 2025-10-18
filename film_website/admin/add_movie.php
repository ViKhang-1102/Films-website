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
          <div class="text-secondary ms-2">Ghi chú: hệ thống tự chuyển link Drive sang dạng nhúng khi lưu phim.</div>
        </div>
      </form>
    </div>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
  let episodeCount = 0;
  const container = document.getElementById('episodesContainer');

  // Helper: nhận diện YouTube/Drive nhanh
  function isValidVideoUrl(url) {
    if (!url) return false;
    url = url.trim();
    if (/youtube\.com|youtu\.be/.test(url)) return true;
    if (/drive\.google\.com/.test(url)) return true;
    // cho phép các url khác (mp4...) -> return true nếu cần
    return true;
  }

  // Hàm tạo HTML cho 1 tập (bao gồm nút Lưu tập)
  function createEpisodeHtml(n) {
    return `
      <div class="card bg-dark border-secondary mb-3 episode-item" data-episode="${n}">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="mb-0 text-warning">Tập ${n}</h6>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-success save-episode" title="Lưu tạm tập">
              <i class="fa-solid fa-check"></i> Lưu tập
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger remove-episode" title="Xóa tập">
              <i class="fa-solid fa-trash"></i>
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label text-light fw-semibold">Tên tập <span class="text-secondary small">(ví dụ: Tập 1 - Giới thiệu)</span></label>
              <input type="text" name="episodes[${n}][title]" class="form-control bg-dark text-light border-secondary" placeholder="Nhập tiêu đề tập, ví dụ: Tập 1 - Giới thiệu" aria-label="Tiêu đề tập ${n}">
            </div>
            <div class="col-md-3">
              <label class="form-label text-light fw-semibold">Số tập</label>
              <input type="number" name="episodes[${n}][episode_number]" class="form-control bg-dark text-light border-secondary" value="${n}" min="1" aria-label="Số tập">
            </div>
            <div class="col-md-3">
              <label class="form-label text-light fw-semibold">Thời lượng</label>
              <input type="text" name="episodes[${n}][duration]" class="form-control bg-dark text-light border-secondary" placeholder="Ví dụ: 45 phút" aria-label="Thời lượng">
            </div>
            <div class="col-12">
              <label class="form-label text-light fw-semibold">Mô tả ngắn</label>
              <textarea name="episodes[${n}][description]" rows="2" class="form-control bg-dark text-light border-secondary" placeholder="Mô tả ngắn về nội dung tập..." aria-label="Mô tả tập"></textarea>
            </div>
            <div class="col-12">
              <label class="form-label text-light fw-semibold">Link video (YouTube hoặc Google Drive)</label>
              <div class="input-group">
                <input type="url" name="episodes[${n}][video_url]" class="form-control bg-dark text-light border-secondary episode-video-url" placeholder="https://drive.google.com/file/d/ID/view hoặc https://youtu.be/..." aria-label="Link video tập ${n}">
                <button type="button" class="btn btn-outline-light btn-sm check-url" title="Kiểm tra URL"><i class="fa-solid fa-link"></i></button>
              </div>
              <div class="form-text text-muted">Nhập link YouTube hoặc Google Drive. Hệ thống sẽ chuyển Drive sang dạng nhúng khi lưu.</div>
              <!-- hidden saved flag -->
              <input type="hidden" name="episodes[${n}][saved]" class="episode-saved" value="0">
            </div>
          </div>
        </div>
      </div>
    `;
  }

  // Thêm tập mới
  document.getElementById('addEpisode').addEventListener('click', function() {
    episodeCount++;
    container.insertAdjacentHTML('beforeend', createEpisodeHtml(episodeCount));
  });

  // Xử lý click toàn cục: Xóa, Lưu tập, Kiểm tra URL
  document.addEventListener('click', function(e) {
    // Xóa tập
    if (e.target.closest('.remove-episode')) {
      const card = e.target.closest('.episode-item');
      if (card) card.remove();
      return;
    }

    // Lưu tập (client-side mark)
    if (e.target.closest('.save-episode')) {
      const card = e.target.closest('.episode-item');
      if (!card) return;
      const title = card.querySelector('input[name^="episodes"][name$="[title]"]').value.trim();
      const videoInput = card.querySelector('.episode-video-url');
      const url = videoInput ? videoInput.value.trim() : '';
      if (!title) {
        alert('Vui lòng nhập tên tập trước khi lưu.');
        return;
      }
      if (!isValidVideoUrl(url)) {
        if (!confirm('URL có vẻ không phải YouTube/Drive. Vẫn lưu chứ?')) return;
      }
      // Đánh dấu đã lưu tạm
      const savedInput = card.querySelector('.episode-saved');
      if (savedInput) savedInput.value = '1';
      card.classList.remove('border-secondary');
      card.classList.add('border-success');
      // hiển thị tạm badge "Đã lưu"
      if (!card.querySelector('.saved-badge')) {
        const badge = document.createElement('span');
        badge.className = 'badge bg-success ms-2 saved-badge';
        badge.innerText = 'Đã lưu';
        card.querySelector('.card-header h6').appendChild(badge);
      }
      return;
    }

    // Kiểm tra URL (mở preview nhỏ nếu muốn)
    if (e.target.closest('.check-url')) {
      const btn = e.target.closest('.check-url');
      const row = btn.closest('.card-body') || btn.closest('.episode-item');
      const urlInput = row.querySelector('.episode-video-url');
      const url = urlInput ? urlInput.value.trim() : '';
      if (!url) {
        alert('Chưa nhập URL video.');
        return;
      }
      if (/drive\.google\.com/.test(url)) {
        alert('Drive link detected — hệ thống sẽ chuyển sang dạng nhúng khi lưu.');
      } else if (/youtu(be)?\.com|youtu\.be/.test(url)) {
        alert('YouTube link detected.');
      } else {
        alert('URL đã nhập: ' + url);
      }
    }
  });

  // Thêm 1 tập mặc định lúc load
  document.getElementById('addEpisode').click();
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
</div>
</div>

