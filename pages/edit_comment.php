<?php
/**
 * Edit comment page
 */
require_once '../config/config.php';
require_once '../includes/user.php';
require_once '../includes/comment.php';

// Check if user is logged in
if (!isLoggedIn()) {
    addError("You must be logged in to edit comments");
    redirect('login.php');
}

// Check if comment ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    addError("Invalid comment ID");
    redirect('../forum.php');
}

$commentId = (int)$_GET['id'];

// Get comment details
$comment = getCommentById($commentId);

if (!$comment) {
    addError("Comment not found");
    redirect('../forum.php');
}

// Check if user is authorized to edit comment
if ($comment['user_id'] != $_SESSION['user_id'] && !isModerator()) {
    addError("You don't have permission to edit this comment");
    redirect("post.php?id={$comment['post_id']}");
}

// Handle comment form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = sanitizeInput($_POST['content']);
    
    // Update comment
    $result = updateComment($commentId, $content);
    
    if ($result) {
        // Redirect to the post
        redirect("pages/post.php?id={$comment['post_id']}");
    }
}

// Include header
include '../includes/header.php';
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-edit me-2"></i> Edit Comment</h4>
            </div>
            <div class="card-body">
                <form action="<?php echo SITE_URL; ?>/pages/edit_comment.php?id=<?php echo $commentId; ?>" method="POST">
                    <div class="mb-3">
                        <label for="content" class="form-label">Comment Content</label>
                        <textarea class="form-control" id="content" name="content" rows="4" required><?php echo $comment['content']; ?></textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Comment
                        </button>
                        <a href="<?php echo SITE_URL; ?>/pages/post.php?id=<?php echo $comment['post_id']; ?>#comment-<?php echo $commentId; ?>" class="btn btn-outline-secondary">
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
include '../includes/footer.php';
?>
