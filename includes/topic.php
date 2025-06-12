<?php
/**
 * Topic space related functions
 */
// Calculate the path to config.php
$config_path = __DIR__ . '/../config/config.php';
require_once $config_path;

// Create a new topic space
function createTopicSpace($topicName, $description, $createdBy) {
    // Validate input
    if (empty($topicName)) {
        addError("Topic name is required");
        return false;
    }
    
    // Check if user is moderator
    if (!isModerator()) {
        addError("Only moderators can create topic spaces");
        return false;
    }
    
    // Check if topic already exists
    $sql = "SELECT topic_id FROM topic_spaces WHERE topic_name = ?";
    $result = executePreparedStatement($sql, "s", [$topicName]);
    
    if ($result->num_rows > 0) {
        addError("Topic already exists");
        return false;
    }
    
    // Insert topic into database
    $sql = "INSERT INTO topic_spaces (topic_name, description, created_by) VALUES (?, ?, ?)";
    $result = executePreparedStatement($sql, "ssi", [$topicName, $description, $createdBy]);
    
    if ($result) {
        addSuccess("Topic space created successfully");
        return getLastInsertId();
    } else {
        addError("Failed to create topic space");
        return false;
    }
}

// Get topic by ID
function getTopicById($topicId) {
    $sql = "SELECT t.*, u.username as creator_name, 
            (SELECT COUNT(*) FROM user_topic_follows WHERE topic_id = t.topic_id) as followers_count
            FROM topic_spaces t
            LEFT JOIN users u ON t.created_by = u.user_id
            WHERE t.topic_id = ?";
    $result = executePreparedStatement($sql, "i", [$topicId]);
    
    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

// Get all topics
function getAllTopics($limit = 10, $offset = 0) {
    $sql = "SELECT t.*, u.username as creator_name, 
            (SELECT COUNT(*) FROM user_topic_follows WHERE topic_id = t.topic_id) as followers_count
            FROM topic_spaces t
            LEFT JOIN users u ON t.created_by = u.user_id
            ORDER BY t.created_at DESC
            LIMIT ? OFFSET ?";
    $result = executePreparedStatement($sql, "ii", [$limit, $offset]);
    
    $topics = [];
    while ($row = $result->fetch_assoc()) {
        $topics[] = $row;
    }
    
    return $topics;
}

// Search topics
function searchTopics($searchTerm, $limit = 10, $offset = 0) {
    $searchTerm = "%$searchTerm%";
    
    $sql = "SELECT t.*, u.username as creator_name, 
            (SELECT COUNT(*) FROM user_topic_follows WHERE topic_id = t.topic_id) as followers_count
            FROM topic_spaces t
            LEFT JOIN users u ON t.created_by = u.user_id
            WHERE t.topic_name LIKE ? OR t.description LIKE ?
            ORDER BY t.created_at DESC
            LIMIT ? OFFSET ?";
    $result = executePreparedStatement($sql, "ssii", [$searchTerm, $searchTerm, $limit, $offset]);
    
    $topics = [];
    while ($row = $result->fetch_assoc()) {
        $topics[] = $row;
    }
    
    return $topics;
}

// Follow a topic
function followTopic($userId, $topicId) {
    // Check if user is logged in
    if (!isLoggedIn()) {
        addError("You must be logged in to follow topics");
        return false;
    }
    
    // Check if already following
    $sql = "SELECT follow_id FROM user_topic_follows WHERE user_id = ? AND topic_id = ?";
    $result = executePreparedStatement($sql, "ii", [$userId, $topicId]);
    
    if ($result->num_rows > 0) {
        addError("You are already following this topic");
        return false;
    }
    
    // Insert follow into database
    $sql = "INSERT INTO user_topic_follows (user_id, topic_id) VALUES (?, ?)";
    $result = executePreparedStatement($sql, "ii", [$userId, $topicId]);
    
    if ($result) {
        addSuccess("You are now following this topic");
        return true;
    } else {
        addError("Failed to follow topic");
        return false;
    }
}

// Unfollow a topic
function unfollowTopic($userId, $topicId) {
    // Check if user is logged in
    if (!isLoggedIn()) {
        addError("You must be logged in to unfollow topics");
        return false;
    }
    
    // Delete follow from database
    $sql = "DELETE FROM user_topic_follows WHERE user_id = ? AND topic_id = ?";
    $result = executePreparedStatement($sql, "ii", [$userId, $topicId]);
    
    if ($result) {
        addSuccess("You have unfollowed this topic");
        return true;
    } else {
        addError("Failed to unfollow topic");
        return false;
    }
}

// Check if user is following a topic
function isFollowingTopic($userId, $topicId) {
    $sql = "SELECT follow_id FROM user_topic_follows WHERE user_id = ? AND topic_id = ?";
    $result = executePreparedStatement($sql, "ii", [$userId, $topicId]);
    
    return $result->num_rows > 0;
}

// Get topics followed by user
function getFollowedTopics($userId, $limit = 10, $offset = 0) {
    $sql = "SELECT t.*, u.username as creator_name, 
            (SELECT COUNT(*) FROM user_topic_follows WHERE topic_id = t.topic_id) as followers_count
            FROM topic_spaces t
            LEFT JOIN users u ON t.created_by = u.user_id
            JOIN user_topic_follows f ON t.topic_id = f.topic_id
            WHERE f.user_id = ?
            ORDER BY f.followed_at DESC
            LIMIT ? OFFSET ?";
    $result = executePreparedStatement($sql, "iii", [$userId, $limit, $offset]);
    
    $topics = [];
    while ($row = $result->fetch_assoc()) {
        $topics[] = $row;
    }
    
    return $topics;
}
