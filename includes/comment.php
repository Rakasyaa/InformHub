<?php
/**
 * Comment related functions
 */
// Calculate the path to config.php
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

// Vote on a comment
function voteComment($userId, $commentId, $voteType) {
    // Check if user is logged in
    if (!isLoggedIn()) {
        addError("You must be logged in to vote");
        return false;
    }
    
    // Check if comment exists
    $comment = getCommentById($commentId);
    if (!$comment) {
        addError("Comment not found");
        return false;
    }
    
    // Check if user has already voted on this comment
    $sql = "SELECT vote_id, vote_type FROM votes 
            WHERE user_id = ? AND content_type = 'comment' AND content_id = ?";
    $result = executePreparedStatement($sql, "ii", [$userId, $commentId]);
    
    if ($result->num_rows > 0) {
        $existingVote = $result->fetch_assoc();
        
        // If same vote type, remove vote
        if ($existingVote['vote_type'] === $voteType) {
            $sql = "DELETE FROM votes WHERE vote_id = ?";
            executePreparedStatement($sql, "i", [$existingVote['vote_id']]);
            
            // Update comment vote count
            updateCommentVoteCount($commentId);
            
            addSuccess("Vote removed");
            return true;
        } else {
            // If different vote type, update vote
            $sql = "UPDATE votes SET vote_type = ? WHERE vote_id = ?";
            executePreparedStatement($sql, "si", [$voteType, $existingVote['vote_id']]);
            
            // Update comment vote count
            updateCommentVoteCount($commentId);
            
            addSuccess("Vote updated");
            return true;
        }
    } else {
        // Insert new vote
        $sql = "INSERT INTO votes (user_id, content_type, content_id, vote_type) 
                VALUES (?, 'comment', ?, ?)";
        $result = executePreparedStatement($sql, "iis", [$userId, $commentId, $voteType]);
        
        if ($result) {
            // Update comment vote count
            updateCommentVoteCount($commentId);
            
            addSuccess("Vote added");
            return true;
        } else {
            addError("Failed to vote");
            return false;
        }
    }
}

// Update comment vote count
function updateCommentVoteCount($commentId) {
    // Count upvotes
    $sql = "SELECT COUNT(*) as count FROM votes 
            WHERE content_type = 'comment' AND content_id = ? AND vote_type = 'upvote'";
    $result = executePreparedStatement($sql, "i", [$commentId]);
    $upvotes = $result->fetch_assoc()['count'];
    
    // Count downvotes
    $sql = "SELECT COUNT(*) as count FROM votes 
            WHERE content_type = 'comment' AND content_id = ? AND vote_type = 'downvote'";
    $result = executePreparedStatement($sql, "i", [$commentId]);
    $downvotes = $result->fetch_assoc()['count'];
    
    // Update comment
    $sql = "UPDATE comments SET upvotes = ?, downvotes = ? WHERE comment_id = ?";
    executePreparedStatement($sql, "iii", [$upvotes, $downvotes, $commentId]);
}
