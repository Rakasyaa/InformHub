<?php
/**
 * Login page
 */
// Calculate paths using __DIR__ for reliable path resolution
$config_path = __DIR__ . '/../config/config.php';
$user_path = __DIR__ . '/../includes/user.php';

require_once $config_path;
require_once $user_path;

// Check if user is already logged in
if (isLoggedIn()) {
    redirect('../home.php');
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    
    if (loginUser($username, $password)) {
        redirect('/home.php');
    }
}

// Include header
include __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i> Login</h4>
            </div>
            <div class="card-body">
                <form action="<?php echo SITE_URL; ?>/pages/login.php" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </button>
                        <p class="text-center mt-3">
                            Don't have an account? <a href="<?php echo SITE_URL; ?>/pages/register.php">Register</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include __DIR__ . '/../includes/footer.php';
?>
