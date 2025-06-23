<?php
    require_once '../config/database.php';
    require_once '../config/config.php';
    require_once '../includes/user.php';
    require_once '../includes/comment_tutorial.php';
    
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isLoggedIn()) {
            header('Location: ' . SITE_URL . '/pages/login.php');
            exit;
        }
    
        $userId = $_SESSION['user_id'] ?? null;
        $tutorialId = $_POST['tutorial_id'] ?? null;
        $content = trim($_POST['comment_content'] ?? '');
    
        if ($tutorialId && $content) {
            createTutorialComment($userId, $tutorialId, $content);
            // Redirect to prevent form resubmission
            header('Location: ' . SITE_URL . "/pages/tutorial.php?id=$tutorialId");
            exit;
        }
    }

    include_once '../includes/header.php';
    
    $conn = getDbConnection();
    $tutorial_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

    // Ambil semua tutorial untuk sidebar
    $all_tutorials = [];
    $sql = "SELECT tutorial_id, title FROM tutorial_content ORDER BY created_at DESC";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $all_tutorials[] = $row;
        }
    }

    if ($tutorial_id) {
        // Ambil data tutorial
        $stmt = $conn->prepare("SELECT * FROM tutorial_content WHERE tutorial_id = ?");
        $stmt->bind_param("i", $tutorial_id);
        $stmt->execute();
        $tutorial = $stmt->get_result()->fetch_assoc();

        // Ambil sections
        $stmt_sections = $conn->prepare("SELECT * FROM tutorial_sections WHERE tutorial_id = ? ORDER BY section_order ASC");
        $stmt_sections->bind_param("i", $tutorial_id);
        $stmt_sections->execute();
        $sections = $stmt_sections->get_result();

        // Ambil code examples
        $stmt_examples = $conn->prepare("SELECT * FROM code_examples WHERE tutorial_id = ?");
        $stmt_examples->bind_param("i", $tutorial_id);
        $stmt_examples->execute();
        $examples = $stmt_examples->get_result();

    } else {
        // Ambil semua tutorial jika tidak ada yang dipilih
        $tutorials_result = $conn->query("SELECT * FROM tutorial_content ORDER BY created_at DESC");
    }

    // POST handling moved to the top of the file

    $comments = [];
    if ($tutorial_id) {
        // Get comments using the correct function
        $comments = getTutorialCommentsByTutorial($tutorial_id);
        if (!$comments) {
            $comments = []; // Ensure it's an array even if null is returned
        }
    }
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Tutorial -->
        <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <h2 class="sidebar-heading text-center">
                    <span>TUTORIAL</span>
                </h2>
                <ul class="nav flex-column">
                    <li class="nav-item category-box">
                        <a class="nav-link <?= !$tutorial_id ? 'active' : '' ?>" href="?">
                            Semua Tutorial
                        </a>
                    </li>

                    <?php foreach ($all_tutorials as $item): ?>
                        <li class="nav-item category-box">
                            <a class="nav-link <?= $tutorial_id == $item['tutorial_id'] ? 'active' : '' ?>" href="?id=<?= $item['tutorial_id'] ?>">
                                <?= htmlspecialchars($item['title']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    
                    <?php if (isadmin()): ?>
                        <li class="nav-item category-box">
                            <a class="nav-link text-success" href="<?= SITE_URL ?>/pages/create_tutorial.php">
                                <i class="fas fa-plus"></i> Tambah Tutorial
                            </a>
                        </li>
                    <?php endif; ?>
                    
                </ul>
            </div>
        </div>

        <!-- Konten Utama -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            
            <!-- Button Edit and Delete -->
            <?php if(isset($tutorial_id) && isAdmin()): ?>
                <div class="gap-2 d-flex justify-content-end mb-4">
                    <a href="<?= SITE_URL ?>/pages/update_tutorial.php?id=<?= $tutorial_id ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit fa-lg"></i> Edit
                    </a>
                    <a href="<?= SITE_URL ?>/pages/delete_tutorial.php?id=<?= $tutorial_id ?>" 
                       class="btn btn-sm btn-outline-danger delete-tutorial-btn"
                       data-delete-url="<?= SITE_URL ?>/pages/delete_tutorial.php?id=<?= $tutorial_id ?>">
                        <i class="fas fa-trash fa-lg"></i> Hapus
                    </a>
                </div>
            <?php endif; ?>

            <?php if ($tutorial_id && $tutorial): ?>
                <div class="card">
                    <div class="card-body">
                        <h2><?= htmlspecialchars($tutorial['title']) ?></h2>
                        <p><?= nl2br(htmlspecialchars($tutorial['description'])) ?></p>
                    </div>
                </div>
                    <?php while ($section = $sections->fetch_assoc()): ?>
                        <h2><?= htmlspecialchars($section['title']) ?></h2>
                        <div class="card">
                            <div class="card-body">
                                <p><?= nl2br(htmlspecialchars($section['content'])) ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>

                <h4>Contoh Kode</h4>
                <?php $example = $examples->fetch_assoc(); ?>
                <h6><?= htmlspecialchars($example['title']) ?></h6>
                <div class="card-code">
                    <pre><code><?= htmlspecialchars($example['code']) ?></code></pre>
                </div>
                
                <!-- Comment form -->
                <?php if (isLoggedIn()): ?>
                    <div class="card mt-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Tinggalkan Komentar</h5>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo SITE_URL; ?>/pages/tutorial.php?id=<?php echo $tutorial_id; ?>" method="POST">
                                <input type="hidden" name="tutorial_id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
                                <div class="mb-3">
                                    <textarea class="form-control" name="comment_content" rows="3" placeholder="Tulis komentarmu di sini..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-1"></i> Kirim Komentar
                                </button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mt-4">
                        <p class="mb-0">Silakan <a href="<?php echo SITE_URL; ?>/pages/login.php">login</a> untuk menulis komentar.</p>
                    </div>
                <?php endif; ?>

                <!-- Comments -->
                <div class="comment-section mt-4">
                    <h4 class="mb-3">Komentar</h4>
                    <?php if (empty($comments)): ?>
                        <div class="alert alert-info">
                            Belum ada komentar. Jadilah yang pertama berkomentar!
                        </div>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): 
                            // Set default values to prevent undefined index notices
                            $comment_id = $comment['id'] ?? 0;
                            $username = htmlspecialchars($comment['username'] ?? 'Pengguna Tidak Dikenal');
                            $profile_image = !empty($comment['profile_image']) ? UPLOAD_URL . $comment['profile_image'] : SITE_URL . '/assets/img/default.jpg';
                            $created_at = $comment['created_at'] ?? date('Y-m-d H:i:s');
                            $content = nl2br(htmlspecialchars($comment['content'] ?? ''));
                            $is_edited = ($comment['created_at'] ?? '') !== ($comment['updated_at'] ?? '');
                        ?>
                            <div class="card mb-3" id="comment-<?= $comment_id ?>">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <img src="<?= $profile_image ?>" alt="<?= $username ?>" class="rounded-circle me-2" width="40" height="40">
                                        <div>
                                            <h6 class="mb-0"><?= $username ?></h6>
                                            <small class="text-muted">
                                                <?= date('d M Y H:i', strtotime($created_at)) ?>
                                                <?php if ($is_edited): ?>
                                                    <span class="ms-2">• Disunting</span>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="comment-content">
                                        <?= $content ?>
                                    </div>
                                    <?php if (isLoggedIn() && (($comment['user_id'] ?? 0) == $_SESSION['user_id'] || isModerator())): ?>
                                        <a href="<?= SITE_URL ?>/pages/delete_comment_tutorial.php?id=<?= $comment['id'] ?>&tutorial_id=<?= $tutorial_id ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Yakin hapus komentar ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            <?php else: ?>
                <h3 class="mb-4">Semua Tutorial</h3>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php while ($row = $tutorials_result->fetch_assoc()): ?>
                        <div class="col">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h5 class="card-title ">
                                                <a href="?id=<?= $row['tutorial_id'] ?>">
                                                    <?= htmlspecialchars($row['title']) ?>
                                                </a>
                                            </h5>
                                        </div>
                                        <div class="col-md-4 justify-content-end">
                                            <p class="badge bg-primary fs-6">
                                                <?= htmlspecialchars($row['category']) ?>
                                            </p>
                                        </div>
                                    </div>
                                    <p class="card-text">
                                    <?php echo substr(strip_tags($row['description']), 0, 50) . '...'; ?>
                                    </p>
                                    <?php if(isAdmin()): ?>
                                    <div class="gap-2 d-flex justify-content-end mt-2">
                                        <a href="<?= SITE_URL ?>/pages/update_tutorial.php?id=<?= $row['tutorial_id'] ?>" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="Edit Tutorial">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= SITE_URL ?>/pages/delete_tutorial.php?id=<?= $row['tutorial_id'] ?>" 
                                        class="btn btn-sm btn-outline-danger delete-tutorial-btn"
                                        data-delete-url="<?= SITE_URL ?>/pages/delete_tutorial.php?id=<?= $row['tutorial_id'] ?>">
                                            <i class="fas fa-trash fa-lg"></i>
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<!-- Delete Tutorial Modal -->
<div id="deleteTutorialModal" class="modal-overlay" hidden>
  <div class="modal-content">
    <p>Yakin hapus tutorial ini? Tindakan ini tidak dapat dibatalkan!</p>
    <div class="modal-buttons">
      <button id="cancelDeleteTutorial">Batal</button>
      <a id="confirmDeleteTutorial" href="#" class="confirm-button">Ya, Hapus</a>
    </div>
  </div>
</div>

<?php include_once '../includes/footer.php'; ?>
