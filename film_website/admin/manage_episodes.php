<?php include __DIR__ . '/includes/header.php'; ?>
<div class="admin-layout">
<?php include __DIR__ . '/includes/sidebar.php'; ?>
<div>
<?php include __DIR__ . '/includes/topbar.php'; ?>

<?php
$movie_id = (int)($_GET['movie_id'] ?? 0);
if (!$movie_id) {
  redirect(BASE_PATH . '/admin/manage_movies.php');
}

// Lấy thông tin phim
$stmt = $mysqli->prepare('SELECT * FROM movies WHERE id = ?');
$stmt->bind_param('i', $movie_id);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$movie) {
  redirect(BASE_PATH . '/admin/manage_movies.php');
}

$msg = '';

// Xử lý thêm/sửa tập phim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  
  if ($action === 'add' || $action === 'edit') {
    $title = sanitize($_POST['title'] ?? '');
    $episode_number = (int)($_POST['episode_number'] ?? 0);
    $description = sanitize($_POST['description'] ?? '');
    $video_url = sanitize($_POST['video_url'] ?? '');
    $duration = sanitize($_POST['duration'] ?? '');
    
    // Chuyển link Google Drive sang dạng uc?id=
    if (preg_match('#https?://drive\.google\.com/file/d/([^/]+)/view#', $video_url, $m)) {
      $video_url = 'https://drive.google.com/uc?id=' . $m[1];
    }
    
    if ($action === 'edit') {
      $episode_id = (int)($_POST['episode_id'] ?? 0);
      $stmt = $mysqli->prepare('UPDATE episodes SET title=?, episode_number=?, description=?, video_url=?, duration=? WHERE id=? AND movie_id=?');
      $stmt->bind_param('sissisi', $title, $episode_number, $description, $video_url, $duration, $episode_id, $movie_id);
      $msg = $stmt->execute() ? 'Cập nhật tập phim thành công!' : 'Cập nhật tập phim thất bại!';
    } else {
      $stmt = $mysqli->prepare('INSERT INTO episodes (movie_id, title, episode_number, description, video_url, duration) VALUES (?, ?, ?, ?, ?, ?)');
      $stmt->bind_param('isisss', $movie_id, $title, $episode_number, $description, $video_url, $duration);
      $msg = $stmt->execute() ? 'Thêm tập phim thành công!' : 'Thêm tập phim thất bại!';
    }
    $stmt->close();
  } elseif ($action === 'delete') {
    $episode_id = (int)($_POST['episode_id'] ?? 0);
    $stmt = $mysqli->prepare('DELETE FROM episodes WHERE id=? AND movie_id=?');
    $stmt->bind_param('ii', $episode_id, $movie_id);
    $msg = $stmt->execute() ? 'Xóa tập phim thành công!' : 'Xóa tập phim thất bại!';
    $stmt->close();
  }
}

// Lấy danh sách tập phim
$episodes = $mysqli->query("SELECT * FROM episodes WHERE movie_id = $movie_id ORDER BY episode_number ASC");

// Dữ liệu form edit
$editEpisode = null;
if (isset($_GET['edit'])) {
  $episode_id = (int)($_GET['edit'] ?? 0);
  $stmt = $mysqli->prepare('SELECT * FROM episodes WHERE id=? AND movie_id=?');
  $stmt->bind_param('ii', $episode_id, $movie_id);
  $stmt->execute();
  $editEpisode = $stmt->get_result()->fetch_assoc();
  $stmt->close();
}
?>

<main class="container-fluid my-4 my-md-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="section-title m-0">Quản lý tập phim</h3>
      <p class="text-secondary mb-0">Phim: <strong><?php echo htmlspecialchars($movie['title']); ?></strong></p>
    </div>
    <div>
      <a class="btn btn-outline-warning me-2" href="<?php echo BASE_PATH; ?>/admin/manage_movies.php">
        <i class="fa-solid fa-arrow-left me-1"></i>Quay lại
      </a>
      <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#episodeModal">
        <i class="fa-solid fa-plus me-1"></i>Thêm tập mới
      </button>
    </div>
  </div>

  <?php if (!empty($msg)): ?>
    <div class="alert <?php echo strpos($msg, 'thành công') !== false ? 'alert-success' : 'alert-danger'; ?>"><?php echo $msg; ?></div>
  <?php endif; ?>

  <div class="row g-4">
    <div class="col-12">
      <div class="card bg-black border-secondary">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-dark table-striped align-middle">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Tên tập</th>
                  <th>Số tập</th>
                  <th>Thời lượng</th>
                  <th>Video</th>
                  <th>Ngày tạo</th>
                  <th class="text-end">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <?php $i=1; while($episode=$episodes->fetch_assoc()): ?>
                <tr>
                  <td><?php echo $i++; ?></td>
                  <td>
                    <div class="fw-bold"><?php echo htmlspecialchars($episode['title']); ?></div>
                    <?php if ($episode['description']): ?>
                      <small class="text-secondary"><?php echo htmlspecialchars($episode['description']); ?></small>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="badge bg-info">Tập <?php echo $episode['episode_number']; ?></span>
                  </td>
                  <td><?php echo htmlspecialchars($episode['duration'] ?: '—'); ?></td>
                  <td>
                    <?php if ($episode['video_url']): ?>
                      <a target="_blank" class="text-warning" href="<?php echo htmlspecialchars($episode['video_url']); ?>">
                        <i class="fa-solid fa-play"></i> Xem
                      </a>
                    <?php else: ?>
                      <span class="text-muted">Chưa có video</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <small class="text-secondary">
                      <?php echo date('d/m/Y H:i', strtotime($episode['created_at'])); ?>
                    </small>
                  </td>
                  <td class="text-end">
                    <div class="btn-group" role="group">
                      <button class="btn btn-sm btn-outline-warning" 
                              onclick="editEpisode(<?php echo htmlspecialchars(json_encode($episode)); ?>)" 
                              title="Chỉnh sửa">
                        <i class="fa-regular fa-pen-to-square"></i>
                      </button>
                      <form method="post" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tập này không?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="episode_id" value="<?php echo $episode['id']; ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit" title="Xóa">
                          <i class="fa-regular fa-trash-can"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
                <?php endwhile; ?>
                
                <?php if ($episodes->num_rows === 0): ?>
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">
                    <i class="fa-solid fa-film fa-2x mb-2"></i><br>
                    Chưa có tập phim nào. Hãy thêm tập đầu tiên!
                  </td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- Modal thêm/sửa tập phim -->
<div class="modal fade" id="episodeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header border-secondary">
        <h5 class="modal-title" id="episodeModalTitle">Thêm tập phim mới</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="post" id="episodeForm">
        <div class="modal-body">
          <input type="hidden" name="action" id="episodeAction" value="add">
          <input type="hidden" name="episode_id" id="episodeId">
          
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label">Tên tập <span class="text-danger">*</span></label>
              <input type="text" name="title" id="episodeTitle" class="form-control bg-dark text-light border-secondary" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Số tập <span class="text-danger">*</span></label>
              <input type="number" name="episode_number" id="episodeNumber" class="form-control bg-dark text-light border-secondary" min="1" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Thời lượng</label>
              <input type="text" name="duration" id="episodeDuration" class="form-control bg-dark text-light border-secondary" placeholder="45 phút">
            </div>
            <div class="col-12">
              <label class="form-label">Mô tả ngắn</label>
              <textarea name="description" id="episodeDescription" rows="3" class="form-control bg-dark text-light border-secondary" placeholder="Mô tả ngắn về tập này..."></textarea>
            </div>
            <div class="col-12">
              <label class="form-label">Link video</label>
              <input type="url" name="video_url" id="episodeVideoUrl" class="form-control bg-dark text-light border-secondary" placeholder="https://drive.google.com/file/d/ID/view?... hoặc URL">
            </div>
          </div>
        </div>
        <div class="modal-footer border-secondary">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="submit" class="btn btn-warning">
            <i class="fa-solid fa-save me-1"></i>Lưu tập phim
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function editEpisode(episode) {
  document.getElementById('episodeModalTitle').textContent = 'Chỉnh sửa tập phim';
  document.getElementById('episodeAction').value = 'edit';
  document.getElementById('episodeId').value = episode.id;
  document.getElementById('episodeTitle').value = episode.title;
  document.getElementById('episodeNumber').value = episode.episode_number;
  document.getElementById('episodeDuration').value = episode.duration || '';
  document.getElementById('episodeDescription').value = episode.description || '';
  document.getElementById('episodeVideoUrl').value = episode.video_url || '';
  
  const modal = new bootstrap.Modal(document.getElementById('episodeModal'));
  modal.show();
}

// Reset form khi modal đóng
document.getElementById('episodeModal').addEventListener('hidden.bs.modal', function() {
  document.getElementById('episodeForm').reset();
  document.getElementById('episodeModalTitle').textContent = 'Thêm tập phim mới';
  document.getElementById('episodeAction').value = 'add';
  document.getElementById('episodeId').value = '';
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
</div>
</div>
