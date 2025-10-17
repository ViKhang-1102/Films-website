<?php include __DIR__ . '/includes/header.php'; ?>
<div class="admin-layout">
<?php include __DIR__ . '/includes/sidebar.php'; ?>
<div>
<?php include __DIR__ . '/includes/topbar.php'; ?>

<?php
$action = $_GET['action'] ?? '';
// Thêm/sửa phim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = sanitize($_POST['title'] ?? '');
  $description = sanitize($_POST['description'] ?? '');
  $category_id = (int)($_POST['category_id'] ?? 0);
  $thumbnail = sanitize($_POST['thumbnail'] ?? '');
  $year = (int)($_POST['year'] ?? 0);
  $duration = sanitize($_POST['duration'] ?? '');
  $video_url = sanitize($_POST['video_url'] ?? '');

  if ($action === 'edit') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $mysqli->prepare('UPDATE movies SET title=?, description=?, category_id=?, thumbnail=?, year=?, duration=?, video_url=? WHERE id=?');
    $stmt->bind_param('ssissisi', $title, $description, $category_id, $thumbnail, $year, $duration, $video_url, $id);
    $stmt->execute();
  } else {
    $stmt = $mysqli->prepare('INSERT INTO movies (title, description, category_id, thumbnail, year, duration, video_url) VALUES (?,?,?,?,?,?,?)');
    $stmt->bind_param('ssissis', $title, $description, $category_id, $thumbnail, $year, $duration, $video_url);
    $stmt->execute();
  }
  redirect(BASE_PATH . '/admin/manage_movies.php');
}

// Xóa phim
if ($action === 'delete') {
  $id = (int)($_GET['id'] ?? 0);
  if ($id > 0) {
    $mysqli->begin_transaction();
    try {
      // Xóa các liên kết trước (favorites, comments, ... nếu có)
      $stmt = $mysqli->prepare('DELETE FROM favorites WHERE movie_id = ?');
      $stmt->bind_param('i', $id);
      $stmt->execute();
      $stmt->close();

      // Sau đó xóa phim
      $stmt = $mysqli->prepare('DELETE FROM movies WHERE id = ?');
      $stmt->bind_param('i', $id);
      $stmt->execute();
      $stmt->close();

      $mysqli->commit();
    } catch (Exception $e) {
      $mysqli->rollback();
      // (tùy bạn) log lỗi hoặc hiển thị thông báo
    }
  }
  redirect(BASE_PATH . '/admin/manage_movies.php');
}

// Dữ liệu form edit
$editItem = null;
if ($action === 'edit') {
  $id = (int)($_GET['id'] ?? 0);
  $stmt = $mysqli->prepare('SELECT * FROM movies WHERE id=?');
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $editItem = $stmt->get_result()->fetch_assoc();
}

$cats = getCategories($mysqli);
// Lấy danh sách phim với thông tin thể loại và số tập
$movies = $mysqli->query('
  SELECT 
    m.*,
    GROUP_CONCAT(c.name SEPARATOR ", ") AS categories,
    COUNT(e.id) AS episode_count
  FROM movies m 
  LEFT JOIN movie_categories mc ON m.id = mc.movie_id
  LEFT JOIN categories c ON mc.category_id = c.id
  LEFT JOIN episodes e ON m.id = e.movie_id
  GROUP BY m.id
  ORDER BY m.created_at DESC
');
?>

<main class="container-fluid my-4 my-md-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="section-title m-0">Quản lý phim</h3>
    <div>
      <a class="btn btn-warning" href="<?php echo BASE_PATH; ?>/admin/add_movie.php"><i class="fa-solid fa-plus me-2"></i>Thêm phim</a>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-12">
      <div class="card bg-black border-secondary">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-dark table-striped align-middle">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Ảnh poster</th>
                  <th>Tên phim</th>
                  <th>Danh mục</th>
                  <th>Số tập</th>
                  <th>Năm</th>
                  <th>Ngày đăng</th>
                  <th class="text-end">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <?php $i=1; while($m=$movies->fetch_assoc()): ?>
                <tr>
                  <td><?php echo $i++; ?></td>
                  <td>
                    <img src="<?php echo htmlspecialchars($m['thumbnail'] ?: 'https://picsum.photos/100/150'); ?>" 
                         alt="Poster" 
                         style="width:60px;height:80px;object-fit:cover;border-radius:6px">
                  </td>
                  <td>
                    <div class="fw-bold"><?php echo htmlspecialchars($m['title']); ?></div>
                    <small class="text-secondary"><?php echo htmlspecialchars($m['duration'] ?: '—'); ?></small>
                  </td>
                  <td>
                    <?php if ($m['categories']): ?>
                      <?php 
                      $categories = explode(', ', $m['categories']);
                      foreach ($categories as $cat): 
                      ?>
                        <span class="badge bg-secondary me-1 mb-1"><?php echo htmlspecialchars(trim($cat)); ?></span>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <span class="text-muted">Chưa phân loại</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="badge bg-info"><?php echo (int)$m['episode_count']; ?> tập</span>
                  </td>
                  <td><?php echo htmlspecialchars($m['year'] ?: '—'); ?></td>
                  <td>
                    <small class="text-secondary">
                      <?php echo date('d/m/Y', strtotime($m['created_at'])); ?>
                    </small>
                  </td>
                  <td class="text-end">
                    <div class="btn-group" role="group">
                      <a class="btn btn-sm btn-outline-warning" 
                         href="<?php echo BASE_PATH; ?>/admin/edit_movie.php?id=<?php echo $m['id']; ?>" 
                         title="Chỉnh sửa">
                        <i class="fa-regular fa-pen-to-square"></i>
                      </a>
                      <a class="btn btn-sm btn-outline-info" 
                         href="<?php echo BASE_PATH; ?>/admin/manage_episodes.php?movie_id=<?php echo $m['id']; ?>" 
                         title="Quản lý tập">
                        <i class="fa-solid fa-list"></i>
                      </a>
                      <a class="btn btn-sm btn-outline-danger" 
                         href="?action=delete&id=<?php echo $m['id']; ?>" 
                         onclick="return confirm('Bạn có chắc chắn muốn xóa phim này không?');" 
                         title="Xóa">
                        <i class="fa-regular fa-trash-can"></i>
                      </a>
                    </div>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</div>
</div>

