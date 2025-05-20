<?php
/**
 * Application configuration
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define paths for file inclusions
$script_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_FILENAME']));
$root_path = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
$relative_path = str_replace($root_path, '', $script_path);
$depth = substr_count($relative_path, '/') - substr_count(str_replace('/forum', '', $relative_path), '/');

// Define constants for paths
define('ROOT_PATH', str_replace('\\', '/', realpath(__DIR__ . '/..')));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('PAGES_PATH', ROOT_PATH . '/pages');
define('ASSETS_PATH', ROOT_PATH . '/assets');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Site configuration
define('SITE_NAME', 'Learning Forum');
define('SITE_URL', 'http://localhost/forum');
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/forum/uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');

// Maximum file upload size (in bytes) - 5MB
define('MAX_FILE_SIZE', 5 * 1024 * 1024);

// Allowed file types for uploads
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('ALLOWED_VIDEO_TYPES', ['video/mp4', 'video/webm']);

// Error and success messages
$messages = [
    'error' => [],
    'success' => []
];

// Add error message
function addError($message) {
    global $messages;
    $messages['error'][] = $message;
}

// Add success message
function addSuccess($message) {
    global $messages;
    $messages['success'][] = $message;
}

// Get all messages
function getMessages() {
    global $messages;
    return $messages;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Check if user is moderator
function isModerator() {
    return isset($_SESSION['is_moderator']) && $_SESSION['is_moderator'] === true;
}

// Redirect to a page
function redirect($page) {
    header("Location: " . SITE_URL . "/" . $page);
    exit;
}

// Sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Include database configuration
require_once 'database.php';
