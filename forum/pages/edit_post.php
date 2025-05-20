<?php
/**
 * Edit post page
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/forum/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/forum/includes/user.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/forum/includes/topic.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/forum/includes/post.php';

// Check if user is logged in
if (!isLoggedIn()) {
    addError("You must be logged in to edit posts");
    redirect('login.php');
}

// Check if post ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    addError("Invalid post ID");
    redirect('../index.php');
}

$postId = (int)$_GET['id'];

// Get post details
$post = getPostById($postId);

if (!$post) {
    addError("Post not found");
    redirect('../index.php');
}

// Check if user is authorized to edit post
if ($post['user_id'] != $_SESSION['user_id'] && !isModerator()) {
    addError("You don't have permission to edit this post");
    redirect("post.php?id=$postId");
}

// Handle post form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title']);
    $content = sanitizeInput($_POST['content']);
    
    // Update post
    $result = updatePost($postId, $title, $content);
    
    if ($result) {
        // Redirect to the post
        redirect("post.php?id=$postId");
    }
}

// Include header
include $_SERVER['DOCUMENT_ROOT'] . '/forum/includes/header.php';
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-edit me-2"></i> Edit Post</h4>
            </div>
            <div class="card-body">
                <form action="<?php echo SITE_URL; ?>/pages/edit_post.php?id=<?php echo $postId; ?>" method="POST">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required maxlength="255" value="<?php echo $post['title']; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="post-content" name="content" rows="6" required maxlength="5000"><?php echo $post['content']; ?></textarea>
                        <div class="form-text" id="char-count"><?php echo 5000 - strlen($post['content']); ?> characters remaining</div>
                    </div>
                    
                    <?php if ($post['media_url'] && $post['media_type'] === 'image'): ?>
                        <div class="mb-3">
                            <label class="form-label">Current Image</label>
                            <div>
                                <img src="<?php echo UPLOAD_URL . $post['media_url']; ?>" alt="Post image" class="img-fluid rounded" style="max-height: 200px;">
                            </div>
                            <div class="form-text">Note: Media cannot be changed after post creation</div>
                        </div>
                    <?php elseif ($post['media_url'] && $post['media_type'] === 'video'): ?>
                        <div class="mb-3">
                            <label class="form-label">Current Video</label>
                            <div>
                                <video src="<?php echo UPLOAD_URL . $post['media_url']; ?>" controls class="img-fluid rounded" style="max-height: 200px;"></video>
                            </div>
                            <div class="form-text">Note: Media cannot be changed after post creation</div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Post
                        </button>
                        <a href="<?php echo SITE_URL; ?>/pages/post.php?id=<?php echo $postId; ?>" class="btn btn-outline-secondary">
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
include $_SERVER['DOCUMENT_ROOT'] . '/forum/includes/footer.php';
?>
