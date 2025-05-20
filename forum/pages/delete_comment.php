<?php
/**
 * Delete comment page
 */
require_once '../config/config.php';
require_once '../includes/user.php';
require_once '../includes/comment.php';

// Check if user is logged in
if (!isLoggedIn()) {
    addError("You must be logged in to delete comments");
    redirect('login.php');
}

// Check if comment ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    addError("Invalid comment ID");
    redirect('../index.php');
}

$commentId = (int)$_GET['id'];

// Get comment details
$comment = getCommentById($commentId);

if (!$comment) {
    addError("Comment not found");
    redirect('../index.php');
}

// Check if user is authorized to delete comment
if ($comment['user_id'] != $_SESSION['user_id'] && !isModerator()) {
    addError("You don't have permission to delete this comment");
    redirect("post.php?id={$comment['post_id']}");
}

// Get post ID for redirection
$postId = $comment['post_id'];

// Delete comment
$result = deleteComment($commentId);

if ($result) {
    addSuccess("Comment deleted successfully");
    // Redirect to post page
    redirect("post.php?id=$postId");
} else {
    addError("Failed to delete comment");
    redirect("post.php?id=$postId");
}
?>
