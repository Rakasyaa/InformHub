<?php
/**
 * Informatika Hub - Theme Switcher
 * 
 * This file handles theme switching for the Informatika Hub website.
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include configuration
require_once 'config.php';

// Check if theme is set in the request
if (isset($_GET['theme'])) {
    $theme = strtolower($_GET['theme']);
    
    // Validate theme
    if (array_key_exists($theme, $available_themes)) {
        // Set theme in session and cookie
        $_SESSION['theme'] = $theme;
        setcookie('theme', $theme, time() + (86400 * 30), '/'); // 30 days
    }
}

// Redirect back to the referring page or home
$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : SITE_URL;
header('Location: ' . $redirect);
exit;
