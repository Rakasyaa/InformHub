<?php
require_once '../config/config.php';
require_once '../includes/user.php';

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

// Redirect back to profile page
redirect("pages/profile.php?id=$userId");
?>
