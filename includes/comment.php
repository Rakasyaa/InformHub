<?php
$config_path = __DIR__ . '/../config/config.php';
require_once $config_path;

// Create a new comment
function createComment($userId, $postId, $content, $parentCommentId = null) {
    // Validate input
    if (empty($content)) {
        addError("Comment content is required");
        return false;
    }
    
    // Check if user is logged in
    if (!isLoggedIn()) {
        addError("You must be logged in to comment");
        return false;
    }
    
    // Insert comment into database
    $sql = "INSERT INTO comments (post_id, user_id, parent_comment_id, content) 
            VALUES (?, ?, ?, ?)";
    $result = executePreparedStatement($sql, "iiis", [$postId, $userId, $parentCommentId, $content]);
    
    addSuccess("Comment added successfully");
    return false;
}

// Get comment by ID
function getCommentById($commentId) {
    $sql = "SELECT c.*, u.username, u.profile_image
            FROM comments c
            JOIN users u ON c.user_id = u.user_id
            WHERE c.comment_id = ?";
    $result = executePreparedStatement($sql, "i", [$commentId]);
    
    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

// Get comments for a post
function getCommentsByPost($postId) {
    $sql = "SELECT c.*, u.username, u.profile_image
            FROM comments c
            JOIN users u ON c.user_id = u.user_id
            WHERE c.post_id = ? AND c.parent_comment_id IS NULL
            ORDER BY c.created_at ASC";
    $result = executePreparedStatement($sql, "i", [$postId]);
    
    $comments = [];
    while ($row = $result->fetch_assoc()) {
        // Get replies for this comment
        $row['replies'] = getRepliesByComment($row['comment_id']);
        $comments[] = $row;
    }
    
    return $comments;
}

// Get replies for a comment
function getRepliesByComment($commentId) {
    $sql = "SELECT c.*, u.username, u.profile_image
            FROM comments c
            JOIN users u ON c.user_id = u.user_id
            WHERE c.parent_comment_id = ?
            ORDER BY c.created_at ASC";
    $result = executePreparedStatement($sql, "i", [$commentId]);
    
    $replies = [];
    while ($row = $result->fetch_assoc()) {
        $replies[] = $row;
    }
    
    return $replies;
}

// Update comment
function updateComment($commentId, $content) {
    // Validate input
    if (empty($content)) {
        addError("Comment content is required");
        return false;
    }
    
    // Check if user is authorized to edit comment
    $comment = getCommentById($commentId);
    if (!$comment) {
        addError("Comment not found");
        return false;
    }
    
    if ($comment['user_id'] != $_SESSION['user_id'] && !isModerator()) {
        addError("You don't have permission to edit this comment");
        return false;
    }
    
    // Update comment in database
    $sql = "UPDATE comments SET content = ?, updated_at = CURRENT_TIMESTAMP WHERE comment_id = ?";
    $result = executePreparedStatement($sql, "si", [$content, $commentId]);
    
    addSuccess("Comment updated successfully");
    return true;
}

// Delete comment
function deleteComment($commentId) {
    // Check if user is authorized to delete comment
    $comment = getCommentById($commentId);
    if (!$comment) {
        addError("Comment not found");
        return false;
    }
    
    if ($comment['user_id'] != $_SESSION['user_id'] && !isModerator()) {
        addError("You don't have permission to delete this comment");
        return false;
    }
    
    // Delete comment from database
    $sql = "DELETE FROM comments WHERE comment_id = ?";
    $result = executePreparedStatement($sql, "i", [$commentId]);
    
    if ($result) {
        addSuccess("Comment deleted successfully");
        return true;
    } else {
        addError("Failed to delete comment");
        return false;
    }
}