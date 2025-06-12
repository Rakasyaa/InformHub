<?php
/**
 * Informatika Hub - Database Connection for Tutorial Section
 * 
 * This file handles the database connection for the Tutorial section.
 */

// Database configuration
$host = "localhost";
$user = "root";  
$pass = "";     
$db   = "forum_db";

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/**
 * Get user role from database
 * 
 * @param int $user_id The user ID
 * @return string The user role (user, moderator, admin)
 */
function get_user_role($user_id) {
    global $conn;
    
    // Check if the role column exists in the users table
    $columnExists = false;
    $result = $conn->query("SHOW COLUMNS FROM users LIKE 'role'");
    if ($result && $result->num_rows > 0) {
        $columnExists = true;
    }
    
    if ($columnExists) {
        // If the role column exists, get the user's role
        try {
            $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                return $row['role'];
            }
        } catch (Exception $e) {
            // If there's an error, return the default role
            return 'user';
        }
    } else {
        // If the role column doesn't exist, check if the user is an admin based on ID
        // For now, let's assume user with ID 1 is admin (you can modify this logic)
        if ($user_id == 1) {
            return 'admin';
        }
    }
    
    return 'user'; // Default role
}

/**
 * Check if user has admin privileges
 * 
 * @param int $user_id The user ID
 * @return bool True if user is admin, false otherwise
 */
function is_admin($user_id) {
    return get_user_role($user_id) === 'admin';
}

/**
 * Check if user has moderator privileges
 * 
 * @param int $user_id The user ID
 * @return bool True if user is moderator or admin, false otherwise
 */
function is_moderator($user_id) {
    $role = get_user_role($user_id);
    return $role === 'moderator' || $role === 'admin';
}
?>
