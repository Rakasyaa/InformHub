<?php
/**
 * Vote comment AJAX handler
 */
require_once '../config/config.php';
require_once '../includes/user.php';
require_once '../includes/comment.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to vote']);
    exit;
}

// Check if comment ID and vote type are provided
if (!isset($_POST['comment_id']) || !is_numeric($_POST['comment_id']) || !isset($_POST['vote_type'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$commentId = (int)$_POST['comment_id'];
$voteType = $_POST['vote_type'];

// Validate vote type
if ($voteType !== 'upvote' && $voteType !== 'downvote') {
    echo json_encode(['success' => false, 'message' => 'Invalid vote type']);
    exit;
}

// Vote on comment
$result = voteComment($_SESSION['user_id'], $commentId, $voteType);

if ($result) {
    // Get updated comment
    $comment = getCommentById($commentId);
    
    // Get user's current vote
    $sql = "SELECT vote_type FROM votes 
            WHERE user_id = ? AND content_type = 'comment' AND content_id = ?";
    $voteResult = executePreparedStatement($sql, "ii", [$_SESSION['user_id'], $commentId]);
    $userVote = ($voteResult->num_rows > 0) ? $voteResult->fetch_assoc()['vote_type'] : null;
    
    echo json_encode([
        'success' => true,
        'voteCount' => $comment['upvotes'] - $comment['downvotes'],
        'userVote' => $userVote
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to vote']);
}
?>
