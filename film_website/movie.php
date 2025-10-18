<?php
require __DIR__ . '/includes/config.php'; ?>
<?php require __DIR__ . '/includes/functions.php'; ?>
<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/navbar.php'; ?>

<?php
$id = (int)($_GET['id'] ?? 0);
$movie = $id ? getMovieById($mysqli, $id) : null;
if (!$movie) {
  echo '<div class="container my-5"><div class="alert alert-warning">Phim không tồn tại.</div></div>';
  include __DIR__ . '/includes/footer.php';
  exit;
}

// Lấy các thể loại của phim
$stmt = $mysqli->prepare('
  SELECT c.name 
  FROM movie_categories mc 
  JOIN categories c ON mc.category_id = c.id 
  WHERE mc.movie_id = ?
');
$stmt->bind_param('i', $id);
$stmt->execute();
$categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Lấy danh sách tập phim
$stmt = $mysqli->prepare('SELECT * FROM episodes WHERE movie_id = ? ORDER BY episode_number ASC');
$stmt->bind_param('i', $id);
$stmt->execute();
$episodes = $stmt->get_result();
$stmt->close();

// --- FIX: đảm bảo $videoUrl luôn được thiết lập từ DB (trước khi gọi getEmbedHtml)
$videoUrl = trim($movie['video_url'] ?? '');

// helper: chuyển Google Drive share link thành link trực tiếp có thể dùng làm src video
function convertPlayableUrl(string $url): string {
  $url = trim($url);
  if ($url === '') return '';
  // Các dạng link Drive thông dụng: /file/d/FILEID/view hoặc open?id=FILEID
  if (preg_match('#drive.google.com/(?:file/d/|open\\?id=)([A-Za-z0-9_-]+)#', $url, $m)) {
    return 'https://drive.google.com/uc?export=download&id=' . $m[1];
  }
  // Nếu đã là link trực tiếp hoặc khác thì trả về nguyên trạng
  return $url;
}

function isDriveLink(string $url): bool {
  return (bool) preg_match('#drive.google.com/(?:file/d/|open\\?id=|uc\\?id=)([A-Za-z0-9_-]+)#', $url);
}
function getDrivePreviewUrl(string $url): string {
  if (preg_match('#drive.google.com/(?:file/d/|open\\?id=)([A-Za-z0-9_-]+)#', $url, $m)) {
    return 'https://drive.google.com/file/d/' . $m[1] . '/preview';
  }
  if (preg_match('#id=([A-Za-z0-9_-]+)#', $url, $m)) {
    return 'https://drive.google.com/file/d/' . $m[1] . '/preview';
  }
  return $url;
}

// New: tạo HTML embed responsive cho YouTube hoặc Google Drive
function getEmbedHtml(string $url, string $title = ''): string {
  $url = trim($url);
  if ($url === '') {
    return '<div class="p-3 text-center text-muted">No video available.</div>';
  }

  // YouTube: nhiều dạng URL -> lấy videoId
  if (preg_match('#(?:youtube\.com/watch\?v=|youtube\.com/embed/|youtu\.be/)([A-Za-z0-9_-]{6,})#', $url, $m)) {
    $vid = $m[1];
    $embed = 'https://www.youtube.com/embed/' . $vid . '?rel=0';
  } elseif (preg_match('#(?:youtube\.com.*[?&]v=)([A-Za-z0-9_-]{6,})#', $url, $m)) {
    $vid = $m[1];
    $embed = 'https://www.youtube.com/embed/' . $vid . '?rel=0';
  }
  // Google Drive
  elseif (isDriveLink($url)) {
    $embed = getDrivePreviewUrl($url);
  } else {
    // Nếu là file media trực tiếp hoặc link khác, giữ nguyên
    $embed = $url;
  }

  // chuẩn bị iframe (gán data-src để script show/hidden có thể quản lý)
  $safeTitle = htmlspecialchars($title ?: 'Video', ENT_QUOTES);
  $iframe = '<iframe class="w-100 h-100 border-0 rounded" src="' . htmlspecialchars($embed, ENT_QUOTES) . '" data-src="' . htmlspecialchars($embed, ENT_QUOTES) . '" title="' . $safeTitle . '" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>';

  // Bootstrap 5 ratio 16:9, bo tròn và shadow-sm
  return '<div class="ratio ratio-16x9 rounded shadow-sm overflow-hidden bg-black">' . $iframe . '</div>';
}

// Sau khi đã có $movie, $episodes, $videoUrl ...
// Thêm trạng thái watched cho user
$watchedMovie = false;
$watchedEpisodes = [];
if (isset($_SESSION['user'])) {
  $uid = (int)$_SESSION['user']['id'];
  $watchedMovie = userHasWatchedMovie($mysqli, $uid, (int)$movie['id']);
  // load watched episodes ids cho movie (1 query)
  $epIds = [];
  $ids = [];
  while($r = $episodes->fetch_assoc()) {
    $ids[] = (int)$r['id'];
    $epIds[$r['id']] = $r; // keep data
  }
  // reset episodes result pointer by recreating result set
  if (!empty($ids)) {
    // tạo danh sách id an toàn (đã cast sang int ở phía trên)
    $in = implode(',', $ids);
    $sql = 'SELECT episode_id FROM watched WHERE user_id = ? AND episode_id IN (' . $in . ')';
    $stmt2 = $mysqli->prepare($sql);
    $stmt2->bind_param('i', $uid); // chỉ bind user_id vì IDS đã là literal trong IN(...)
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    while($w = $res2->fetch_assoc()) {
      $watchedEpisodes[(int)$w['episode_id']] = true;
    }
    $stmt2->close();
  }
  // rebuild episodes result for template: fetch again from DB
  $stmt = $mysqli->prepare('SELECT * FROM episodes WHERE movie_id = ? ORDER BY episode_number ASC');
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $episodes = $stmt->get_result();
  $stmt->close();
}
?>

<main class="container my-4 my-md-5">
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card movie-card">
        <div class="movie-thumb">
          <img src="<?php echo htmlspecialchars($movie['thumbnail'] ?: 'https://picsum.photos/500/750'); ?>" alt="Poster phim">
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <h2 class="fw-bold"><?php echo htmlspecialchars($movie['title']); ?></h2>
      <p class="text-secondary"><?php echo nl2br(htmlspecialchars($movie['description'] ?? '')); ?></p>

      <div class="row g-3 small">
        <div class="col-sm-6 col-lg-4"><span class="text-secondary">Thời lượng:</span> <strong><?php echo htmlspecialchars($movie['duration'] ?: '—'); ?></strong></div>
        <div class="col-sm-6 col-lg-4"><span class="text-secondary">Năm:</span> <strong><?php echo htmlspecialchars($movie['year'] ?: '—'); ?></strong></div>
        <div class="col-sm-6 col-lg-4"><span class="text-secondary">Số tập:</span> <strong><?php echo $episodes->num_rows; ?> tập</strong></div>
        <div class="col-12">
          <span class="text-secondary">Thể loại:</span> 
          <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
              <span class="badge text-bg-dark border border-secondary me-1"><?php echo htmlspecialchars($cat['name']); ?></span>
            <?php endforeach; ?>
          <?php else: ?>
            <span class="text-muted">Chưa phân loại</span>
          <?php endif; ?>
         
        </div>
        <div class="col-sm-6 col-lg-4"><span class="text-secondary">Đạo diễn:</span> <strong>Chưa cập nhật</strong></div>
        <div class="col-sm-6 col-lg-4"><span class="text-secondary">Diễn viên:</span> <strong>Chưa cập nhật</strong></div>
        <div class="col-sm-6 col-lg-4"><span class="text-secondary">Quốc gia:</span> <strong>Chưa cập nhật</strong></div>
      </div>

      <div class="mt-4 d-flex gap-2 flex-wrap">
        <?php if ($episodes->num_rows > 0): ?>
          <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#episodesModal">
            <i class="fa-solid fa-list me-2"></i>Chọn tập phim
          </button>
        <?php else: ?>
          <?php if (!empty($videoUrl)): ?>
            <!-- Nếu movie có video_url: mở modal player (server-side đã in sẵn iframe trong #playerModal) -->
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#playerModal">
              <i class="fa-solid fa-play me-2"></i>Xem ngay
            </button>
          <?php else: ?>
            <!-- Nếu không có video_url thì giữ chức năng đánh dấu đã xem -->
            <form method="post" action="<?php echo BASE_PATH; ?>/watch.php" class="d-inline">
              <input type="hidden" name="movie_id" value="<?php echo (int)$movie['id']; ?>">
              <input type="hidden" name="redirect" value="<?php echo htmlspecialchars(BASE_PATH . '/movie.php?id=' . (int)$movie['id']); ?>">
              <input type="hidden" name="action" value="<?php echo $watchedMovie ? 'unmark_movie' : 'mark_movie'; ?>">
              <button class="btn <?php echo $watchedMovie ? 'btn-outline-success' : 'btn-warning'; ?>" type="submit">
                <i class="fa-solid fa-play me-2"></i><?php echo $watchedMovie ? 'Đã xem' : 'Xem ngay'; ?>
              </button>
            </form>
          <?php endif; ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['user'])): ?>
          <?php $fav = userHasFavorite($mysqli, (int)$_SESSION['user']['id'], (int)$movie['id']); ?>
          <form method="post" action="<?php echo BASE_PATH . '/favorite.php'; ?>">
            <input type="hidden" name="movie_id" value="<?php echo (int)$movie['id']; ?>">
            <input type="hidden" name="redirect" value="<?php echo BASE_PATH . '/movie.php?id=' . (int)$movie['id']; ?>">
            <input type="hidden" name="action" value="<?php echo $fav ? 'remove' : 'add'; ?>">
            <button class="btn <?php echo $fav ? 'btn-warning' : 'btn-outline-warning'; ?>" type="submit">
              <i class="fa-regular fa-bookmark me-2"></i><?php echo $fav ? 'Bỏ yêu thích' : 'Lưu yêu thích'; ?>
            </button>
          </form>
        <?php else: ?>
          <a class="btn btn-outline-warning" href="<?php echo BASE_PATH . '/login.php'; ?>"><i class="fa-regular fa-bookmark me-2"></i>Đăng nhập để lưu</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Gợi ý phim tương tự -->
  <div class="mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="section-title m-0">Phim tương tự</h4>
      <a href="/film_website/category.php" class="text-warning">Xem thêm <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="row g-3 g-md-4">
      <?php $similar = getMoviesByCategory($mysqli, (int)($movie['category_id'] ?? 0), 6); while($m=$similar->fetch_assoc()): if ((int)$m['id']===(int)$movie['id']) continue; ?>
      <div class="col-6 col-md-4 col-lg-2">
        <div class="card movie-card h-100 position-relative">
          <?php $simWatched = isset($_SESSION['user']) ? userHasWatchedMovie($mysqli, $_SESSION['user']['id'], (int)$m['id']) : false; ?>
          <?php if ($simWatched): ?>
            <span class="badge bg-success position-absolute" style="top:8px; right:8px; z-index:5;">Đã xem</span>
          <?php endif; ?>
          <a href="/film_website/movie.php?id=<?php echo $m['id']; ?>" class="text-decoration-none text-light">
            <div class="movie-thumb">
              <img src="<?php echo htmlspecialchars($m['thumbnail'] ?: 'https://picsum.photos/400/600'); ?>" alt="<?php echo htmlspecialchars($m['title']); ?>">
            </div>
            <div class="card-body py-3">
              <h6 class="card-title mb-1"><?php echo htmlspecialchars($m['title']); ?></h6>
              <div class="movie-meta"><i class="fa-regular fa-calendar me-1"></i><?php echo htmlspecialchars($m['year']); ?></div>
            </div>
          </a>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</main>

<!-- Modal chọn tập phim -->
<div class="modal fade" id="episodesModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header border-secondary">
        <h5 class="modal-title mb-0">
          <i class="fa-solid fa-list me-2"></i>Chọn tập phim - <?php echo htmlspecialchars($movie['title']); ?>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <?php 
          $episodes->data_seek(0); // Reset pointer
          while($episode = $episodes->fetch_assoc()): 
          ?>
          <div class="col-md-6">
            <div class="card bg-black border-secondary h-100 episode-card" data-episode-id="<?php echo $episode['id']; ?>" data-video-url="<?php echo htmlspecialchars($episode['video_url']); ?>">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <h6 class="card-title text-warning mb-0"><?php echo htmlspecialchars($episode['title']); ?></h6>
                  <span class="badge bg-info">Tập <?php echo $episode['episode_number']; ?></span>
                </div>
                <?php if ($episode['description']): ?>
                  <p class="card-text text-secondary small"><?php echo htmlspecialchars($episode['description']); ?></p>
                <?php endif; ?>
                <div class="d-flex justify-content-between align-items-center">
                  <small class="text-muted">
                    <i class="fa-solid fa-clock me-1"></i><?php echo htmlspecialchars($episode['duration'] ?: '—'); ?>
                  </small>
                  <button class="btn btn-sm btn-warning play-episode" data-episode-id="<?php echo $episode['id']; ?>">
                    <i class="fa-solid fa-play me-1"></i>Xem
                  </button>

                  <?php if (isset($_SESSION['user'])): ?>
                    <form method="post" action="<?php echo BASE_PATH; ?>/watch.php" class="d-inline ms-2">
                      <input type="hidden" name="episode_id" value="<?php echo (int)$episode['id']; ?>">
                      <input type="hidden" name="redirect" value="<?php echo htmlspecialchars(BASE_PATH . '/movie.php?id=' . (int)$movie['id']); ?>">
                      <input type="hidden" name="action" value="<?php echo !empty($watchedEpisodes[$episode['id']]) ? 'unmark_episode' : 'mark_episode'; ?>">
                      <button class="btn btn-sm <?php echo !empty($watchedEpisodes[$episode['id']]) ? 'btn-outline-success' : 'btn-outline-light'; ?>">
                        <?php if (!empty($watchedEpisodes[$episode['id']])): ?>
                          <i class="fa-solid fa-check me-1"></i>Đã xem
                        <?php else: ?>
                          <i class="fa-regular fa-circle-play me-1"></i>Đánh dấu xem
                        <?php endif; ?>
                      </button>
                    </form>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
          <?php endwhile; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Video player modal -->
<div class="modal fade" id="playerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header border-0">
        <h5 class="modal-title mb-0"><?php echo htmlspecialchars($movie['title']); ?></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-3">
        <?php
          // Hiển thị embed; nếu muốn hiển thị tập được chọn thì truyền video của tập tương ứng
          echo getEmbedHtml($videoUrl, $movie['title']);
        ?>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  var modalEl = document.getElementById('playerModal');
  if (!modalEl) return;

  // Helper: build embed src từ một URL YouTube/Drive hoặc trả về nguyên trạng
  function buildEmbedSrc(url) {
    if (!url) return '';
    url = url.trim();
    // YouTube: youtu.be/ID or v=ID
    var m;
    m = url.match(/(?:youtube\.com\/watch\?v=|youtube\.com\/embed\/|youtu\.be\/)([A-Za-z0-9_-]{6,})/);
    if (!m) m = url.match(/[?&]v=([A-Za-z0-9_-]{6,})/);
    if (m) return 'https://www.youtube.com/embed/' + m[1] + '?rel=0';
    // Google Drive: /file/d/ID/ or open?id=ID or id=ID
    m = url.match(/drive\.google\.com\/(?:file\/d\/|open\?id=)([A-Za-z0-9_-]+)/) || url.match(/[?&]id=([A-Za-z0-9_-]+)/);
    if (m) return 'https://drive.google.com/file/d/' + m[1] + '/preview';
    // fallback: return original url
    return url;
  }

  // Khi đóng modal: dừng video và clear iframe src
  modalEl.addEventListener('hidden.bs.modal', function () {
    try {
      var video = modalEl.querySelector('video');
      if (video) {
        video.pause();
        video.currentTime = 0;
      }
      var iframe = modalEl.querySelector('iframe');
      if (iframe) {
        // lưu src gốc nếu cần (data-orig) và dọn src để stop playback
        if (!iframe.dataset.orig) iframe.dataset.orig = iframe.src || iframe.getAttribute('data-src') || '';
        iframe.src = 'about:blank';
      }
    } catch(e){}
  });

  // Khi modal mở (khi bấm "Xem ngay" dùng movie.videoUrl đã render sẵn)
  modalEl.addEventListener('show.bs.modal', function () {
    try {
      var iframe = modalEl.querySelector('iframe');
      if (iframe) {
        var orig = iframe.dataset.orig || iframe.getAttribute('data-src') || iframe.src || '';
        if (orig && (iframe.src === '' || iframe.src.indexOf('about:blank') === 0)) {
          iframe.src = orig;
        }
      }
      // nếu có thẻ <video> kiểm tra và load source nếu cần
      var video = modalEl.querySelector('video');
      if (video) {
        var srcEl = video.querySelector('source');
        if (srcEl && srcEl.dataset.src && (!srcEl.src || srcEl.src === '')) {
          srcEl.src = srcEl.dataset.src;
          video.load();
        }
      }
    } catch(e){}
  });

  // Khi bấm nút "Xem" của 1 tập -> build embed src, đặt vào modal và show modal
  document.querySelectorAll('.play-episode').forEach(function(btn){
    btn.addEventListener('click', function(e){
      var card = btn.closest('.episode-card');
      if (!card) return;
      var raw = card.dataset.videoUrl || '';
      var embed = buildEmbedSrc(raw);
      if (!embed) {
        alert('Không tìm thấy nguồn video cho tập này.');
        return;
      }

      var body = modalEl.querySelector('.modal-body');
      if (!body) return;

      // Tạo/ghi đè nội dung modal body bằng iframe responsive (Bootstrap ratio 16:9)
      body.innerHTML = '';
      var wrap = document.createElement('div');
      wrap.className = 'ratio ratio-16x9 rounded shadow-sm overflow-hidden bg-black';
      var ifr = document.createElement('iframe');
      ifr.className = 'w-100 h-100 border-0 rounded';
      ifr.setAttribute('allow', 'autoplay; encrypted-media; picture-in-picture');
      ifr.setAttribute('allowfullscreen', '');
      ifr.dataset.orig = embed;
      ifr.src = embed;
      wrap.appendChild(ifr);
      body.appendChild(wrap);

      // show modal bằng bootstrap API
      try {
        var bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        bsModal.show();
      } catch(e){}
    });
  });

  // Nếu có nút "Xem ngay" dùng movie.videoUrl, đảm bảo modal body đã có iframe (server-side getEmbedHtml đã in sẵn).
  // Nếu muốn cập nhật khi bấm "Xem ngay" mà không dùng data-bs-toggle, bạn có thể bind tương tự.
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>