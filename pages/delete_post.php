<?php
/**
 * Delete post page
 */
require_once '../config/config.php';
require_once '../includes/user.php';
require_once '../includes/post.php';

// Check if user is logged in
if (!isLoggedIn()) {
    addError("You must be logged in to delete posts");
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

// Check if user is authorized to delete post
if ($post['user_id'] != $_SESSION['user_id'] && !isModerator()) {
    addError("You don't have permission to delete this post");
    redirect("post.php?id=$postId");
}

// Get topic ID for redirection
$topicId = $post['topic_id'];

// Delete post
$result = deletePost($postId);

if ($result) {
    addSuccess("Post deleted successfully");
    // Redirect to topic page
    redirect("index.php");
} else {
    addError("Failed to delete post");
    redirect("index.php");
}
?>
