<?php
/**
 * Informatika Hub - Database Setup Script
 * 
 * This script checks the database connection and creates the database if it doesn't exist.
 */

// Database configuration
$host = "localhost";
$user = "root";  
$pass = "";     

// Connect to MySQL without selecting a database
$conn = mysqli_connect($host, $user, $pass);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if database exists
$result = mysqli_query($conn, "SHOW DATABASES LIKE 'simple_login'");
$db_exists = mysqli_num_rows($result) > 0;

if (!$db_exists) {
    echo "Database 'simple_login' does not exist. Creating database and tables...<br>";
    
    // Create database
    $sql_create_db = "CREATE DATABASE simple_login";
    if (mysqli_query($conn, $sql_create_db)) {
        echo "Database created successfully.<br>";
        
        // Select the newly created database
        mysqli_select_db($conn, "simple_login");
        
        // Create tables
        $sql_create_tables = "
        CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE user_progress (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            topic VARCHAR(50) NOT NULL,
            progress INT DEFAULT 0,
            last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        );

        CREATE TABLE completed_lessons (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            lesson_id VARCHAR(100) NOT NULL,
            completion_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        );
        ";
        
        // Execute multiple SQL statements
        if (mysqli_multi_query($conn, $sql_create_tables)) {
            do {
                // Store result
                if ($result = mysqli_store_result($conn)) {
                    mysqli_free_result($result);
                }
                // Check for more results
            } while (mysqli_next_result($conn));
            
            echo "Tables created successfully.<br>";
            
            // Insert default user
            mysqli_select_db($conn, "simple_login");
            $sql_insert_user = "INSERT INTO users (username, email, password) VALUES ('user', 'user@gmail.com', '123')";
            if (mysqli_query($conn, $sql_insert_user)) {
                echo "Default user created successfully.<br>";
            } else {
                echo "Error creating default user: " . mysqli_error($conn) . "<br>";
            }
        } else {
            echo "Error creating tables: " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "Error creating database: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "Database 'simple_login' already exists.<br>";
}

echo "<br>Database setup complete. <a href='../Home/index.php'>Go to Home Page</a>";

// Close connection
mysqli_close($conn);
?>
