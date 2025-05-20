<?php
/**
 * Logout page
 */
require_once '../config/config.php';
require_once '../includes/user.php';

// Logout user
logoutUser();

// Redirect to home page
redirect('../index.php');
?>
