<?php
/**
 * Delete post page
 */
require_once '../config/config.php';
require_once '../includes/user.php';
require_once '../includes/post.php';

// Check if post ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    addError("Invalid post ID");
    redirect('index.php');
}

$postId = (int)$_GET['id'];

// Get post details
$post = getPostById($postId);

if (!$post) {
    addError("Post not found");
    redirect('index.php');
}

// Delete post
$result = deletePost($postId);

if (1) {
    redirect("index.php");
}
?>
