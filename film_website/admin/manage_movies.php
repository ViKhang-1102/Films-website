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
  $stmt = $mysqli->prepare('DELETE FROM movies WHERE id=?');
  $stmt->bind_param('i', $id);
  $stmt->execute();
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
$movies = $mysqli->query('SELECT m.*, c.name AS category_name FROM movies m LEFT JOIN categories c ON m.category_id = c.id ORDER BY m.created_at DESC');
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
              <thead><tr><th>#</th><th>Tiêu đề</th><th>Thể loại</th><th>Năm</th><th>Thời lượng</th><th>Video</th><th>Thumbnail</th><th class="text-end">Thao tác</th></tr></thead>
              <tbody>
                <?php $i=1; while($m=$movies->fetch_assoc()): ?>
                <tr>
                  <td><?php echo $i++; ?></td>
                  <td><?php echo htmlspecialchars($m['title']); ?></td>
                  <td class="text-secondary small"><?php echo htmlspecialchars($m['category_name'] ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($m['year']); ?></td>
                  <td><?php echo htmlspecialchars($m['duration']); ?></td>
                  <td class="small"><a target="_blank" class="text-warning" href="<?php echo htmlspecialchars($m['video_url']); ?>">Mở</a></td>
                  <td><img src="<?php echo htmlspecialchars($m['thumbnail']); ?>" alt="thumb" style="width:48px;height:48px;object-fit:cover;border-radius:4px"></td>
                  <td class="text-end">
                    <a class="btn btn-sm btn-outline-warning" href="<?php echo BASE_PATH; ?>/admin/edit_movie.php?id=<?php echo $m['id']; ?>"><i class="fa-regular fa-pen-to-square"></i></a>
                    <a class="btn btn-sm btn-outline-danger" href="?action=delete&id=<?php echo $m['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa không?');"><i class="fa-regular fa-trash-can"></i></a>
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

