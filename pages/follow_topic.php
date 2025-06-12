<?php
/**
 * Follow/unfollow topic page
 */
require_once '../config/config.php';
require_once '../includes/user.php';
require_once '../includes/topic.php';

// Check if user is logged in
if (!isLoggedIn()) {
    // Return JSON response if AJAX request
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        echo json_encode(['success' => false, 'message' => 'You must be logged in to follow topics']);
        exit;
    }
    
    addError("You must be logged in to follow topics");
    redirect('login.php');
}

// Check if topic ID and action are provided
if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['action'])) {
    // Return JSON response if AJAX request
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }
    
    addError("Invalid request");
    redirect('../index.php');
}

$topicId = (int)$_GET['id'];
$action = $_GET['action'];

// Check if topic exists
$topic = getTopicById($topicId);

if (!$topic) {
    // Return JSON response if AJAX request
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        echo json_encode(['success' => false, 'message' => 'Topic not found']);
        exit;
    }
    
    addError("Topic not found");
    redirect('../index.php');
}

// Follow or unfollow topic
$success = false;
if ($action === 'follow') {
    $success = followTopic($_SESSION['user_id'], $topicId);
} elseif ($action === 'unfollow') {
    $success = unfollowTopic($_SESSION['user_id'], $topicId);
} else {
    // Return JSON response if AJAX request
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
    }
    
    addError("Invalid action");
    redirect("topic.php?id=$topicId");
}

// Return JSON response if AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    // Get updated followers count
    $updatedTopic = getTopicById($topicId);
    $isFollowing = isFollowingTopic($_SESSION['user_id'], $topicId);
    
    echo json_encode([
        'success' => $success,
        'followersCount' => $updatedTopic['followers_count'],
        'userFollowing' => $isFollowing
    ]);
    exit;
}

// Redirect back to topic page
redirect("topic.php?id=$topicId");
?>
