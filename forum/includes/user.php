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
    
    if ($result) {
        addSuccess("Registration successful. You can now login.");
        return true;
    } else {
        addError("Registration failed. Please try again.");
        return false;
    }
}

// Login user
function loginUser($username, $password) {
    // Validate input
    if (empty($username) || empty($password)) {
        addError("Username and password are required");
        return false;
    }
    
    // Get user from database
    $sql = "SELECT user_id, username, password, is_moderator FROM users WHERE username = ?";
    $result = executePreparedStatement($sql, "s", [$username]);
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_moderator'] = $user['is_moderator'];
            
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
    $sql = "SELECT user_id, username, email, profile_image, bio, is_moderator, created_at 
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
    
    if ($result) {
        addSuccess("Profile updated successfully");
        return true;
    } else {
        addError("Failed to update profile");
        return false;
    }
}

// Toggle moderator status
function toggleModeratorStatus($userId) {
    // Check if current user is moderator
    if (!isModerator()) {
        addError("You don't have permission to perform this action");
        return false;
    }
    
    // Get current moderator status
    $user = getUserById($userId);
    $newStatus = $user['is_moderator'] ? 0 : 1;
    
    // Update moderator status
    $sql = "UPDATE users SET is_moderator = ? WHERE user_id = ?";
    $result = executePreparedStatement($sql, "ii", [$newStatus, $userId]);
    
    if ($result) {
        $statusText = $newStatus ? "promoted to moderator" : "demoted from moderator";
        addSuccess("User {$user['username']} has been {$statusText}");
        return true;
    } else {
        addError("Failed to update moderator status");
        return false;
    }
}
