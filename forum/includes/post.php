<?php
/**
 * Post related functions
 */
// Calculate the path to config.php
$config_path = __DIR__ . '/../config/config.php';
require_once $config_path;

// Create a new post
function createPost($userId, $topicId, $title, $content, $mediaFile = null) {
    // Validate input
    if (empty($title)) {
        addError("Title is required");
        return false;
    }
    
    // Check if user is logged in
    if (!isLoggedIn()) {
        addError("You must be logged in to create posts");
        return false;
    }
    
    $mediaUrl = null;
    $mediaType = 'none';
    
    // Handle media upload if provided
    if ($mediaFile !== null && $mediaFile['error'] === 0) {
        $uploadResult = uploadMedia($mediaFile);
        if ($uploadResult['success']) {
            $mediaUrl = $uploadResult['url'];
            $mediaType = $uploadResult['type'];
        } else {
            addError($uploadResult['message']);
            return false;
        }
    }
    
    // Insert post into database
    $sql = "INSERT INTO posts (user_id, topic_id, title, content, media_url, media_type) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $result = executePreparedStatement($sql, "iissss", [
        $userId, $topicId, $title, $content, $mediaUrl, $mediaType
    ]);
    
    if ($result) {
        addSuccess("Post created successfully");
        return getLastInsertId();
    } else {
        addError("Failed to create post");
        return false;
    }
}

// Upload media file
function uploadMedia($file) {
    // Check if file was uploaded without errors
    if ($file['error'] !== 0) {
        return ['success' => false, 'message' => 'Error uploading file'];
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File is too large (max 5MB)'];
    }
    
    // Check file type
    $fileType = $file['type'];
    $mediaType = '';
    
    if (in_array($fileType, ALLOWED_IMAGE_TYPES)) {
        $mediaType = 'image';
    } elseif (in_array($fileType, ALLOWED_VIDEO_TYPES)) {
        $mediaType = 'video';
    } else {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    // Generate unique filename
    $fileName = uniqid() . '_' . basename($file['name']);
    $targetPath = UPLOAD_DIR . $fileName;
    
    // Move uploaded file to target directory
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return [
            'success' => true,
            'url' => $fileName,
            'type' => $mediaType
        ];
    } else {
        return ['success' => false, 'message' => 'Failed to upload file'];
    }
}

// Get post by ID
function getPostById($postId) {
    $sql = "SELECT p.*, u.username, u.profile_image, t.topic_name,
            (SELECT COUNT(*) FROM comments WHERE post_id = p.post_id) as comments_count
            FROM posts p
            JOIN users u ON p.user_id = u.user_id
            JOIN topic_spaces t ON p.topic_id = t.topic_id
            WHERE p.post_id = ?";
    $result = executePreparedStatement($sql, "i", [$postId]);
    
    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

// Get posts by topic
function getPostsByTopic($topicId, $limit = 10, $offset = 0) {
    $sql = "SELECT p.*, u.username, u.profile_image, t.topic_name,
            (SELECT COUNT(*) FROM comments WHERE post_id = p.post_id) as comments_count
            FROM posts p
            JOIN users u ON p.user_id = u.user_id
            JOIN topic_spaces t ON p.topic_id = t.topic_id
            WHERE p.topic_id = ?
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?";
    $result = executePreparedStatement($sql, "iii", [$topicId, $limit, $offset]);
    
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    
    return $posts;
}

// Get posts from followed topics
function getPostsFromFollowedTopics($userId, $limit = 10, $offset = 0) {
    $sql = "SELECT p.*, u.username, u.profile_image, t.topic_name,
            (SELECT COUNT(*) FROM comments WHERE post_id = p.post_id) as comments_count
            FROM posts p
            JOIN users u ON p.user_id = u.user_id
            JOIN topic_spaces t ON p.topic_id = t.topic_id
            JOIN user_topic_follows f ON p.topic_id = f.topic_id
            WHERE f.user_id = ?
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?";
    $result = executePreparedStatement($sql, "iii", [$userId, $limit, $offset]);
    
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    
    return $posts;
}

// Update post
function updatePost($postId, $title, $content) {
    // Validate input
    if (empty($title)) {
        addError("Title is required");
        return false;
    }
    
    // Check if user is authorized to edit post
    $post = getPostById($postId);
    if (!$post) {
        addError("Post not found");
        return false;
    }
    
    if ($post['user_id'] != $_SESSION['user_id'] && !isModerator()) {
        addError("You don't have permission to edit this post");
        return false;
    }
    
    // Update post in database
    $sql = "UPDATE posts SET title = ?, content = ?, updated_at = CURRENT_TIMESTAMP WHERE post_id = ?";
    $result = executePreparedStatement($sql, "ssi", [$title, $content, $postId]);
    
    if ($result) {
        addSuccess("Post updated successfully");
        return true;
    } else {
        addError("Failed to update post");
        return false;
    }
}

// Delete post
function deletePost($postId) {
    // Check if user is authorized to delete post
    $post = getPostById($postId);
    if (!$post) {
        addError("Post not found");
        return false;
    }
    
    if ($post['user_id'] != $_SESSION['user_id'] && !isModerator()) {
        addError("You don't have permission to delete this post");
        return false;
    }
    
    // Delete post from database
    $sql = "DELETE FROM posts WHERE post_id = ?";
    $result = executePreparedStatement($sql, "i", [$postId]);
    
    if ($result) {
        // Delete associated media file if exists
        if ($post['media_url'] && file_exists(UPLOAD_DIR . $post['media_url'])) {
            unlink(UPLOAD_DIR . $post['media_url']);
        }
        
        addSuccess("Post deleted successfully");
        return true;
    } else {
        addError("Failed to delete post");
        return false;
    }
}

// Vote on a post
function votePost($userId, $postId, $voteType) {
    // Check if user is logged in
    if (!isLoggedIn()) {
        addError("You must be logged in to vote");
        return false;
    }
    
    // Check if post exists
    $post = getPostById($postId);
    if (!$post) {
        addError("Post not found");
        return false;
    }
    
    // Check if user has already voted on this post
    $sql = "SELECT vote_id, vote_type FROM votes 
            WHERE user_id = ? AND content_type = 'post' AND content_id = ?";
    $result = executePreparedStatement($sql, "ii", [$userId, $postId]);
    
    if ($result->num_rows > 0) {
        $existingVote = $result->fetch_assoc();
        
        // If same vote type, remove vote
        if ($existingVote['vote_type'] === $voteType) {
            $sql = "DELETE FROM votes WHERE vote_id = ?";
            executePreparedStatement($sql, "i", [$existingVote['vote_id']]);
            
            // Update post vote count
            updatePostVoteCount($postId);
            
            addSuccess("Vote removed");
            return true;
        } else {
            // If different vote type, update vote
            $sql = "UPDATE votes SET vote_type = ? WHERE vote_id = ?";
            executePreparedStatement($sql, "si", [$voteType, $existingVote['vote_id']]);
            
            // Update post vote count
            updatePostVoteCount($postId);
            
            addSuccess("Vote updated");
            return true;
        }
    } else {
        // Insert new vote
        $sql = "INSERT INTO votes (user_id, content_type, content_id, vote_type) 
                VALUES (?, 'post', ?, ?)";
        $result = executePreparedStatement($sql, "iis", [$userId, $postId, $voteType]);
        
        if ($result) {
            // Update post vote count
            updatePostVoteCount($postId);
            
            addSuccess("Vote added");
            return true;
        } else {
            addError("Failed to vote");
            return false;
        }
    }
}

// Update post vote count
function updatePostVoteCount($postId) {
    // Count upvotes
    $sql = "SELECT COUNT(*) as count FROM votes 
            WHERE content_type = 'post' AND content_id = ? AND vote_type = 'upvote'";
    $result = executePreparedStatement($sql, "i", [$postId]);
    $upvotes = $result->fetch_assoc()['count'];
    
    // Count downvotes
    $sql = "SELECT COUNT(*) as count FROM votes 
            WHERE content_type = 'post' AND content_id = ? AND vote_type = 'downvote'";
    $result = executePreparedStatement($sql, "i", [$postId]);
    $downvotes = $result->fetch_assoc()['count'];
    
    // Update post
    $sql = "UPDATE posts SET upvotes = ?, downvotes = ? WHERE post_id = ?";
    executePreparedStatement($sql, "iii", [$upvotes, $downvotes, $postId]);
}
