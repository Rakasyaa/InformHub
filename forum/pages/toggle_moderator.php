<?php
/**
 * Toggle moderator status page
 */
require_once '../config/config.php';
require_once '../includes/user.php';

// Check if user is logged in and is a moderator
if (!isLoggedIn() || !isModerator()) {
    addError("You don't have permission to perform this action");
    redirect('../index.php');
}

// Check if user ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    addError("Invalid user ID");
    redirect('../index.php');
}

$userId = (int)$_GET['id'];

// Get user details
$user = getUserById($userId);

if (!$user) {
    addError("User not found");
    redirect('../index.php');
}

// Toggle moderator status
$result = toggleModeratorStatus($userId);

if ($result) {
    // Redirect to user profile
    redirect("profile.php?id=$userId");
} else {
    addError("Failed to update moderator status");
    redirect("profile.php?id=$userId");
}
?>
