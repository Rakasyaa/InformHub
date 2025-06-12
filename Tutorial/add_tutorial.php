<?php
/**
 * Informatika Hub - Add Tutorial Page
 * 
 * This file allows admins to add a new tutorial.
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
include_once 'includes/header.php';
include_once 'includes/tutorial_functions.php';

// Check if user is logged in and is admin
$user_logged_in = isset($_SESSION['user_id']);
$user_id = $user_logged_in ? $_SESSION['user_id'] : 0;
$is_admin = $user_logged_in && is_admin($user_id);

// Redirect if not admin
if (!$is_admin) {
    header('Location: index.php');
    exit;
}

// Process form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $category = isset($_POST['category']) ? trim($_POST['category']) : '';
    
    // Validate input
    if (empty($title) || empty($description) || empty($category)) {
        $error_message = 'Please fill in all required fields.';
    } else {
        // Add tutorial
        $tutorial_id = add_tutorial($category, $title, $description, $user_id);
        
        if ($tutorial_id) {
            $success_message = 'Tutorial added successfully. <a href="edit_tutorial.php?id=' . $tutorial_id . '">Edit it now</a> to add sections and code examples.';
        } else {
            $error_message = 'Failed to add tutorial. Please try again.';
        }
    }
}

// Get valid categories
$validCategories = ['html', 'css', 'javascript', 'bootstrap', 'react', 'vue', 'blockchain', 'solidity', 'nft'];
?>

<!-- Admin Content -->
<div class="course-container fade-in">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active">Add Tutorial</li>
        </ol>
    </nav>
    
    <!-- Admin Header -->
    <div class="content-header slide-in">
        <h1>Add New Tutorial</h1>
        <p class="lead">Create a new tutorial for Informatika Hub</p>
    </div>
    
    <?php if ($success_message): ?>
    <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
    <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <!-- Add Tutorial Form -->
    <div class="card slide-in mb-4">
        <div class="card-header">
            <h2>Tutorial Details</h2>
        </div>
        <div class="card-body">
            <form method="post" action="add_tutorial.php">
                <div class="mb-3">
                    <label for="title" class="form-label">Tutorial Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Tutorial Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-select" id="category" name="category" required>
                        <option value="" selected disabled>Select a category</option>
                        <?php foreach ($validCategories as $cat): ?>
                        <option value="<?php echo $cat; ?>"><?php echo ucfirst($cat); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Add Tutorial</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Existing Tutorials -->
    <div class="card slide-in mb-4">
        <div class="card-header">
            <h2>Existing Tutorials</h2>
        </div>
        <div class="card-body">
            <?php
            // Get all tutorials
            global $conn;
            $stmt = $conn->prepare("SELECT * FROM tutorial_content ORDER BY category, title");
            $stmt->execute();
            $result = $stmt->get_result();
            $tutorials = [];
            while ($row = $result->fetch_assoc()) {
                $tutorials[] = $row;
            }
            
            if (empty($tutorials)):
            ?>
            <div class="alert alert-info">No tutorials found. Add your first tutorial using the form above.</div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tutorials as $tutorial): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($tutorial['title']); ?></td>
                            <td><?php echo ucfirst(htmlspecialchars($tutorial['category'])); ?></td>
                            <td><?php echo date('M d, Y', strtotime($tutorial['created_at'])); ?></td>
                            <td>
                                <a href="course.php?category=<?php echo $tutorial['category']; ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-eye"></i> View</a>
                                <a href="edit_tutorial.php?id=<?php echo $tutorial['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i> Edit</a>
                                <a href="delete_tutorial.php?id=<?php echo $tutorial['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this tutorial?')"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Include footer
include_once 'includes/footer.php';
?>
