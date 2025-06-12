<?php
/**
 * Informatika Hub - Delete Tutorial
 * 
 * This file handles the deletion of a tutorial.
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
include_once 'includes/tutorial_functions.php';

// Check if user is logged in and is admin
$user_logged_in = isset($_SESSION['user_id']);
$user_id = $user_logged_in ? $_SESSION['user_id'] : 0;
$is_admin = $user_logged_in && is_admin($user_id);

// Redirect if not admin
if (!$is_admin) {
    header('Location: index.php');
    exit;
}

// Get tutorial ID from URL
$tutorial_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check if tutorial exists and get category for redirect
$category = 'html'; // Default
if ($tutorial_id > 0) {
    global $conn;
    $stmt = $conn->prepare("SELECT category FROM tutorial_content WHERE id = ?");
    $stmt->bind_param("i", $tutorial_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $category = $row['category'];
        
        // Delete the tutorial
        delete_tutorial($tutorial_id, $user_id);
    }
}

// Redirect back to the category page
header("Location: course.php?category={$category}");
exit;
?>
