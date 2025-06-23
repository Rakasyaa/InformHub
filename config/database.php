<?php
/**
 * Database configuration file
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'informhub');

// Create database connection
function getDbConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Close database connection
function closeDbConnection($conn) {
    $conn->close();
}

// Execute query and return result
function executeQuery($sql) {
    $conn = getDbConnection();
    $result = $conn->query($sql);
    closeDbConnection($conn);
    return $result;
}

// Execute prepared statement
function executePreparedStatement($sql, $types, $params) {
    $conn = getDbConnection();
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $success = $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    closeDbConnection($conn);
    
    return $success ? $result : false;
}

// Get last inserted ID
function getLastInsertId() {
    $conn = getDbConnection();
    $lastId = $conn->insert_id;
    closeDbConnection($conn);
    return $lastId;
}
