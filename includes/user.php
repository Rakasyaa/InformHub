<?php
/**
 * User related functions
 */
// Calculate the path to config.php
$config_path = __DIR__ . '/../config/config.php';
require_once $config_path;

// Register a new user
function registerUser($username, $email, $password) {
    // Validate input
    if (empty($username) || empty($email) || empty($password)) {
        addError("All fields are required");
        return false;
    }
    
    // Check if username already exists
    $sql = "SELECT user_id FROM users WHERE username = ?";
    $result = executePreparedStatement($sql, "s", [$username]);
    
    if ($result->num_rows > 0) {
        addError("Username already exists");
        return false;
    }
    
    // Check if email already exists
    $sql = "SELECT user_id FROM users WHERE email = ?";
    $result = executePreparedStatement($sql, "s", [$email]);
    
    if ($result->num_rows > 0) {
        addError("Email already exists");
        return false;
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user into database
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $result = executePreparedStatement($sql, "sss", [$username, $email, $hashedPassword]);

    addSuccess("Registration successful. You now can login.");
    return true;

}

// Login user
function loginUser($username, $password) {
    // Validate input
    if (empty($username) || empty($password)) {
        addError("Username and password are required");
        return false;
    }
    
    // Get user from database
    $sql = "SELECT user_id, username, password, role FROM users WHERE username = ?";
    $result = executePreparedStatement($sql, "s", [$username]);
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            addSuccess("Login successful");
            return true;
        } else {
            addError("Invalid password");
            return false;
        }
    } else {
        addError("User not found");
        return false;
    }
}

// Logout user
function logoutUser() {
    // Unset all session variables
    $_SESSION = [];
    
    // Destroy the session
    session_destroy();
    
    addSuccess("Logout successful");
    return true;
}

// Get user by ID
function getUserById($userId) {
    $sql = "SELECT user_id, username, email, profile_image, bio, role, created_at 
            FROM users WHERE user_id = ?";
    $result = executePreparedStatement($sql, "i", [$userId]);
    
    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

// Update user profile
function updateUserProfile($userId, $bio, $profileImage = null) {
    // Update bio
    $sql = "UPDATE users SET bio = ? WHERE user_id = ?";
    $result = executePreparedStatement($sql, "si", [$bio, $userId]);
    
    // Update profile image if provided
    if ($profileImage !== null) {
        $sql = "UPDATE users SET profile_image = ? WHERE user_id = ?";
        $result = executePreparedStatement($sql, "si", [$profileImage, $userId]);
    }
    
    addSuccess("Profile updated successfully");
    return true;
}

// Toggle moderator status
function toggleModeratorStatus($userId) {
    // Get current user data
    $user = getUserById($userId);
    
    // Toggle between 'mod' and 'user' roles
    $newStatus = ($user['role'] === 'mod') ? 'member' : 'mod';
    
    // Update moderator status
    $sql = "UPDATE users SET role = ? WHERE user_id = ?";
    $result = executePreparedStatement($sql, "si", [$newStatus, $userId]);
    
    $statusText = ($newStatus === 'mod') ? 'promoted to moderator' : 'demoted to regular user';
    addSuccess("User {$user['username']} has been {$statusText}");
    return true;
}