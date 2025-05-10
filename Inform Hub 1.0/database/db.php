<?php
/**
 * Informatika Hub - Database Connection
 * 
 * This file handles the database connection for the Informatika Hub website.
 */

$host = "localhost";
$user = "root";  
$pass = "";     
$db   = "simple_login";

// Check if we're in a page that requires database connection
$requires_db = true;

// Try to connect to the database
try {
    $conn = mysqli_connect($host, $user, $pass, $db);
    
    if (!$conn) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }
} catch (Exception $e) {
    // If we're in a page that requires database, show error
    if ($requires_db) {
        echo "<div style='background-color: #f8d7da; color: #721c24; padding: 10px; margin: 10px; border-radius: 5px;'>";
        echo "<h3>Database Connection Error</h3>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "<p>Please make sure your MySQL server is running and then try again.</p>";
        echo "<p>You can also run the <a href='/teswindud/database/setup.php'>database setup script</a> to initialize the database.</p>";
        echo "<p><a href='/teswindud/Home/index.php'>Return to Home Page</a></p>";
        echo "</div>";
    }
    
    // Set $conn to false so pages can check if connection is available
    $conn = false;
}
?>
