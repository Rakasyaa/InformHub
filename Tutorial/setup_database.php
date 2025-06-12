<?php
/**
 * Informatika Hub - Database Setup
 * 
 * This file sets up the database tables for the tutorial system.
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
include_once 'includes/header.php';
include_once 'includes/db_connect.php';

// Check if user is logged in and is admin
$user_logged_in = isset($_SESSION['user_id']);
$user_id = $user_logged_in ? $_SESSION['user_id'] : 0;
$is_admin = $user_logged_in && is_admin($user_id);

// Process form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup'])) {
    // Read SQL file
    $sql_file = file_get_contents('../database/tutorial_tables.sql');
    
    if ($sql_file) {
        // Split SQL file into individual queries
        $queries = explode(';', $sql_file);
        
        // Execute each query
        $success = true;
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                if (!$conn->query($query)) {
                    $success = false;
                    $error_message = "Error executing query: " . $conn->error;
                    break;
                }
            }
        }
        
        if ($success) {
            $success_message = "Database tables created successfully!";
        }
    } else {
        $error_message = "Could not read SQL file.";
    }
}

// Check if tables exist
$tables_exist = false;
$result = $conn->query("SHOW TABLES LIKE 'tutorial_content'");
if ($result && $result->num_rows > 0) {
    $tables_exist = true;
}

// Check if role column exists in users table
$role_column_exists = false;
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'role'");
if ($result && $result->num_rows > 0) {
    $role_column_exists = true;
}
?>

<!-- Setup Content -->
<div class="course-container fade-in">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active">Database Setup</li>
        </ol>
    </nav>
    
    <!-- Setup Header -->
    <div class="content-header slide-in">
        <h1>Tutorial Database Setup</h1>
        <p class="lead">Set up the database tables for the tutorial system</p>
    </div>
    
    <?php if ($success_message): ?>
    <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
    <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <!-- Setup Form -->
    <div class="card slide-in mb-4">
        <div class="card-header">
            <h2>Database Status</h2>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <h4>Current Status:</h4>
                <ul class="list-group mb-3">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Tutorial Tables
                        <?php if ($tables_exist): ?>
                        <span class="badge bg-success">Installed</span>
                        <?php else: ?>
                        <span class="badge bg-danger">Not Installed</span>
                        <?php endif; ?>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        User Roles
                        <?php if ($role_column_exists): ?>
                        <span class="badge bg-success">Installed</span>
                        <?php else: ?>
                        <span class="badge bg-danger">Not Installed</span>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
            
            <?php if (!$tables_exist || !$role_column_exists): ?>
            <form method="post" action="setup_database.php">
                <div class="alert alert-warning">
                    <p><strong>Warning:</strong> This will create new tables in your database and modify existing ones. Make sure you have a backup before proceeding.</p>
                </div>
                <div class="d-grid">
                    <input type="hidden" name="setup" value="1">
                    <button type="submit" class="btn btn-primary btn-lg">Set Up Database</button>
                </div>
            </form>
            <?php else: ?>
            <div class="alert alert-info">
                <p>All database tables are already set up. You're ready to use the tutorial system!</p>
            </div>
            <div class="d-grid gap-2">
                <a href="course.php" class="btn btn-primary">Go to Tutorials</a>
                <a href="add_tutorial.php" class="btn btn-outline-primary">Manage Tutorials</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Include footer
include_once 'includes/footer.php';
?>
