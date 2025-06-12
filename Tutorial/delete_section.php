<?php
/**
 * Informatika Hub - Delete Section
 * 
 * This file handles the deletion of a tutorial section.
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

// Get section ID from URL
$section_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check if section exists and get tutorial category for redirect
$category = 'html'; // Default
if ($section_id > 0) {
    global $conn;
    $stmt = $conn->prepare("SELECT t.category FROM tutorial_sections s 
                           JOIN tutorial_content t ON s.tutorial_id = t.id 
                           WHERE s.id = ?");
    $stmt->bind_param("i", $section_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $category = $row['category'];
        
        // Delete the section
        $stmt = $conn->prepare("DELETE FROM tutorial_sections WHERE id = ?");
        $stmt->bind_param("i", $section_id);
        $stmt->execute();
    }
}

// Redirect back to the tutorial page
header("Location: course.php?category={$category}");
exit;
?>
