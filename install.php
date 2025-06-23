<?php
/**
 * Installation script for Learning Forum
 */

// Define constants
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'forum_db');

// Start installation
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$error = '';
$success = '';

// Process installation steps
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 1) {
        // Check database connection
        $conn = @new mysqli($_POST['db_host'], $_POST['db_user'], $_POST['db_pass']);
        
        if ($conn->connect_error) {
            $error = "Database connection failed: " . $conn->connect_error;
        } else {
            // Create database if it doesn't exist
            $sql = "CREATE DATABASE IF NOT EXISTS " . $_POST['db_name'];
            if ($conn->query($sql) === TRUE) {
                // Update config file
                $configFile = 'config/database.php';
                $configContent = file_get_contents($configFile);
                
                $configContent = str_replace("define('DB_HOST', 'localhost')", "define('DB_HOST', '" . $_POST['db_host'] . "')", $configContent);
                $configContent = str_replace("define('DB_USER', 'root')", "define('DB_USER', '" . $_POST['db_user'] . "')", $configContent);
                $configContent = str_replace("define('DB_PASS', '')", "define('DB_PASS', '" . $_POST['db_pass'] . "')", $configContent);
                $configContent = str_replace("define('DB_NAME', 'forum_db')", "define('DB_NAME', '" . $_POST['db_name'] . "')", $configContent);
                
                file_put_contents($configFile, $configContent);
                
                $success = "Database connection successful. Database '{$_POST['db_name']}' created.";
                header("Location: install.php?step=2");
                exit;
            } else {
                $error = "Error creating database: " . $conn->error;
            }
            
            $conn->close();
        }
    } elseif ($step === 2) {
        // Import database schema
        $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            $error = "Database connection failed: " . $conn->connect_error;
        } else {
            // Read SQL file
            $sqlFile = 'database/forum_db.sql';
            $sql = file_get_contents($sqlFile);
            
            // Execute SQL queries
            if ($conn->multi_query($sql)) {
                $success = "Database tables created successfully.";
                header("Location: install.php?step=3");
                exit;
            } else {
                $error = "Error importing database schema: " . $conn->error;
            }
            
            $conn->close();
        }
    } elseif ($step === 3) {
        // Create admin user
        $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            $error = "Database connection failed: " . $conn->connect_error;
        } else {
            $username = $conn->real_escape_string($_POST['username']);
            $email = $conn->real_escape_string($_POST['email']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            
            // Check if admin user already exists
            $sql = "SELECT user_id FROM users WHERE username = 'admin'";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                // Update admin user
                $sql = "UPDATE users SET username = '$username', email = '$email', password = '$password' WHERE username = 'admin'";
            } else {
                // Insert admin user
                $sql = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', 'admin')";
            }
            
            if ($conn->query($sql) === TRUE) {
                $success = "Admin user created successfully.";
                header("Location: install.php?step=4");
                exit;
            } else {
                $error = "Error creating admin user: " . $conn->error;
            }
            
            $conn->close();
        }
    }
}

// HTML header
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InformatikaHub Installation</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 40px;
        }
        .install-container {
            max-width: 700px;
            margin: 0 auto;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            position: relative;
        }
        .step.active {
            background-color: #007bff;
            color: white;
        }
        .step.completed {
            background-color: #28a745;
            color: white;
        }
        .step-line {
            flex-grow: 1;
            height: 3px;
            background-color: #e9ecef;
            margin: 20px 10px 0;
        }
        .step-line.completed {
            background-color: #28a745;
        }
        .step-text {
            position: absolute;
            top: 45px;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="container install-container">
        <div class="text-center mb-4">
            <h1><i class="fas fa-book-reader me-2"></i> InformatikaHub</h1>
            <p class="lead">Installation Wizard</p>
        </div>
        
        <div class="step-indicator">
            <div class="step <?php echo $step >= 1 ? 'active' : ''; ?> <?php echo $step > 1 ? 'completed' : ''; ?>">
                1
                <span class="step-text">Database</span>
            </div>
            <div class="step-line <?php echo $step > 1 ? 'completed' : ''; ?>"></div>
            <div class="step <?php echo $step >= 2 ? 'active' : ''; ?> <?php echo $step > 2 ? 'completed' : ''; ?>">
                2
                <span class="step-text">Schema</span>
            </div>
            <div class="step-line <?php echo $step > 2 ? 'completed' : ''; ?>"></div>
            <div class="step <?php echo $step >= 3 ? 'active' : ''; ?> <?php echo $step > 3 ? 'completed' : ''; ?>">
                3
                <span class="step-text">Admin</span>
            </div>
            <div class="step-line <?php echo $step > 3 ? 'completed' : ''; ?>"></div>
            <div class="step <?php echo $step >= 4 ? 'active' : ''; ?>">
                4
                <span class="step-text">Finish</span>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body p-4">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if ($step === 1): ?>
                    <h3 class="card-title mb-4">Step 1: Database Configuration</h3>
                    <form action="install.php?step=1" method="POST">
                        <div class="mb-3">
                            <label for="db_host" class="form-label">Database Host</label>
                            <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
                        </div>
                        <div class="mb-3">
                            <label for="db_user" class="form-label">Database Username</label>
                            <input type="text" class="form-control" id="db_user" name="db_user" value="root" required>
                        </div>
                        <div class="mb-3">
                            <label for="db_pass" class="form-label">Database Password</label>
                            <input type="password" class="form-control" id="db_pass" name="db_pass" value="">
                        </div>
                        <div class="mb-3">
                            <label for="db_name" class="form-label">Database Name</label>
                            <input type="text" class="form-control" id="db_name" name="db_name" value="forum_db" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-database me-1"></i> Connect to Database
                            </button>
                        </div>
                    </form>
                <?php elseif ($step === 2): ?>
                    <h3 class="card-title mb-4">Step 2: Database Schema</h3>
                    <p>Now we will create the necessary tables in your database. This will set up the structure needed for the forum to work properly.</p>
                    <form action="install.php?step=2" method="POST">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-table me-1"></i> Create Tables
                            </button>
                        </div>
                    </form>
                <?php elseif ($step === 3): ?>
                    <h3 class="card-title mb-4">Step 3: Create Admin User</h3>
                    <p>Create an administrator account to manage your forum.</p>
                    <form action="install.php?step=3" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-shield me-1"></i> Create Admin User
                            </button>
                        </div>
                    </form>
                <?php elseif ($step === 4): ?>
                    <h3 class="card-title mb-4">Step 4: Installation Complete</h3>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-1"></i> Congratulations! The Learning Forum has been successfully installed.
                    </div>
                    <p>You can now start using your forum. Here are some next steps:</p>
                    <ul>
                        <li>Log in with your administrator account</li>
                        <li>Create topic spaces for your forum</li>
                        <li>Customize your forum settings</li>
                        <li>Invite users to join your forum</li>
                    </ul>
                    <div class="d-grid gap-2">
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-home me-1"></i> Go to Homepage
                        </a>
                        <a href="pages/login.php" class="btn btn-outline-primary">
                            <i class="fas fa-sign-in-alt me-1"></i> Log In
                        </a>
                    </div>
                    <div class="mt-3 text-center">
                        <small class="text-muted">For security reasons, please delete this installation file (install.php) after you've completed the setup.</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <p class="text-muted">&copy; <?php echo date('Y'); ?> Learning Forum</p>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
