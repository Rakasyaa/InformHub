<?php
/**
 * Create post page
 */
// Calculate paths using __DIR__ for reliable path resolution
$config_path = __DIR__ . '/../config/config.php';
$user_path = __DIR__ . '/../includes/user.php';
$topic_path = __DIR__ . '/../includes/topic.php';
$post_path = __DIR__ . '/../includes/post.php';

require_once $config_path;
require_once $user_path;
require_once $topic_path;
require_once $post_path;

// Check if user is logged in
if (!isLoggedIn()) {
    addError("You must be logged in to create a post");
    redirect('login.php');
}

// Get topic ID if provided
$topicId = isset($_GET['topic_id']) ? (int)$_GET['topic_id'] : null;

// Get all topics for dropdown
$topics = getAllTopics(100, 0);

// Handle post form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title']);
    $content = sanitizeInput($_POST['content']);
    $selectedTopicId = (int)$_POST['topic_id'];
    
    // Check if media file is uploaded
    $mediaFile = null;
    if (isset($_FILES['media']) && $_FILES['media']['error'] !== UPLOAD_ERR_NO_FILE) {
        $mediaFile = $_FILES['media'];
    }
    
    // Create post
    $result = createPost($_SESSION['user_id'], $selectedTopicId, $title, $content, $mediaFile);
    
    redirect("forum.php");
}

// Include header
include __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i> Create New Post</h4>
            </div>
            <div class="card-body">
                <form action="<?php echo SITE_URL; ?>/pages/create_post.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required maxlength="255">
                    </div>
                    
                    <div class="mb-3">
                        <label for="topic_id" class="form-label">Topic</label>
                        <select class="form-select" id="topic_id" name="topic_id" required>
                            <option value="">Select a topic</option>
                            <?php foreach ($topics as $topic): ?>
                                <option value="<?php echo $topic['topic_id']; ?>" <?php echo ($topicId == $topic['topic_id']) ? 'selected' : ''; ?>>
                                    <?php echo $topic['topic_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="post-content" name="content" rows="6" required maxlength="5000"></textarea>
                        <div class="form-text" id="char-count">5000 characters</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="media" class="form-label">Media (Optional)</label>
                        <input type="file" class="form-control" id="post-media" name="media" accept="image/jpeg,image/png,image/gif,video/mp4,video/webm">
                        <div class="form-text">Supported formats: JPEG, PNG, GIF, MP4, WebM (max 5MB)</div>
                        <div id="media-preview"></div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i> Create Post
                        </button>
                        <a href="<?php echo isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : SITE_URL; ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include __DIR__ . '/../includes/footer.php';
?>
