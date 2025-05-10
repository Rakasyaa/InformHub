<?php
/**
 * Informatika Hub - Language Switcher
 * 
 * This file handles language switching for the Informatika Hub website.
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include configuration
require_once 'config.php';

// Check if language is set in the request
if (isset($_GET['lang'])) {
    $language = strtolower($_GET['lang']);
    
    // Validate language
    if (array_key_exists($language, $available_languages)) {
        // Set language in session and cookie
        $_SESSION['language'] = $language;
        setcookie('language', $language, time() + (86400 * 30), '/'); // 30 days
    }
}

// Redirect back to the referring page or home
$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : SITE_URL;
header('Location: ' . $redirect);
exit;
