<?php
/**
 * Informatika Hub - Logout Script
 * 
 * This script handles user logout functionality.
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to home page
header("Location: ../index.php");
exit();
?>
