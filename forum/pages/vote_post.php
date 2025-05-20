<?php
/**
 * Vote post AJAX handler
 */
require_once '../config/config.php';
require_once '../includes/user.php';
require_once '../includes/post.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to vote']);
    exit;
}

// Check if post ID and vote type are provided
if (!isset($_POST['post_id']) || !is_numeric($_POST['post_id']) || !isset($_POST['vote_type'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$postId = (int)$_POST['post_id'];
$voteType = $_POST['vote_type'];

// Validate vote type
if ($voteType !== 'upvote' && $voteType !== 'downvote') {
    echo json_encode(['success' => false, 'message' => 'Invalid vote type']);
    exit;
}

// Vote on post
$result = votePost($_SESSION['user_id'], $postId, $voteType);

if ($result) {
    // Get updated post
    $post = getPostById($postId);
    
    // Get user's current vote
    $sql = "SELECT vote_type FROM votes 
            WHERE user_id = ? AND content_type = 'post' AND content_id = ?";
    $voteResult = executePreparedStatement($sql, "ii", [$_SESSION['user_id'], $postId]);
    $userVote = ($voteResult->num_rows > 0) ? $voteResult->fetch_assoc()['vote_type'] : null;
    
    echo json_encode([
        'success' => true,
        'voteCount' => $post['upvotes'] - $post['downvotes'],
        'userVote' => $userVote
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to vote']);
}
?>
