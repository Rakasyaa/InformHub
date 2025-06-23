<?php
// Include required files
require_once '../config/database.php';
require_once '../includes/user.php';
require_once '../includes/comment_tutorial.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['error'] = 'Anda harus login untuk menghapus komentar';
    header('Location: ' . SITE_URL . '/pages/login.php');
    exit;
}

// Get comment ID and tutorial ID from URL
$comment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$tutorial_id = isset($_GET['tutorial_id']) ? (int)$_GET['tutorial_id'] : 0;

if ($comment_id <= 0 || $tutorial_id <= 0) {
    $_SESSION['error'] = 'Parameter tidak valid';
    header('Location: ' . SITE_URL . '/pages/tutorial.php');
    exit;
}

// Get the comment to verify ownership
$comment = getTutorialCommentById($comment_id);

if (!$comment) {
    $_SESSION['error'] = 'Komentar tidak ditemukan';
    header('Location: ' . SITE_URL . "/pages/tutorial.php?id=$tutorial_id");
    exit;
}

// Check if user is the owner of the comment or a moderator
$is_owner = ($comment['user_id'] == $_SESSION['user_id']);
$is_moderator = isModerator();

if (!$is_owner && !$is_moderator) {
    $_SESSION['error'] = 'Anda tidak memiliki izin untuk menghapus komentar ini';
    header('Location: ' . SITE_URL . "/pages/tutorial.php?id=$tutorial_id");
    exit;
}

// Delete the comment
$success = deleteTutorialComment($comment_id);

if ($success) {
    $_SESSION['success'] = 'Komentar berhasil dihapus';
} else {
    $_SESSION['error'] = 'Gagal menghapus komentar. Silakan coba lagi.';
}

// Redirect back to the tutorial page
header('Location: ' . SITE_URL . "/pages/tutorial.php?id=$tutorial_id");
exit;
