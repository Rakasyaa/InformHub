<?php
/**
 * Informatika Hub - Edit Tutorial Page
 * 
 * This file allows admins to edit an existing tutorial.
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

// Get tutorial ID from URL
$tutorial_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check if tutorial exists
$tutorial = null;
if ($tutorial_id > 0) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM tutorial_content WHERE id = ?");
    $stmt->bind_param("i", $tutorial_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $tutorial = $result->fetch_assoc();
    }
}

// Redirect if tutorial doesn't exist
if (!$tutorial) {
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
        // Update tutorial
        if (update_tutorial($tutorial_id, $title, $description, $user_id)) {
            $success_message = 'Tutorial updated successfully.';
            
            // Refresh tutorial data
            $stmt = $conn->prepare("SELECT * FROM tutorial_content WHERE id = ?");
            $stmt->bind_param("i", $tutorial_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $tutorial = $result->fetch_assoc();
            }
        } else {
            $error_message = 'Failed to update tutorial. Please try again.';
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
            <li class="breadcrumb-item"><a href="course.php?category=<?php echo $tutorial['category']; ?>"><?php echo $tutorial['title']; ?></a></li>
            <li class="breadcrumb-item active">Edit Tutorial</li>
        </ol>
    </nav>
    
    <!-- Admin Header -->
    <div class="content-header slide-in">
        <h1>Edit Tutorial</h1>
        <p class="lead">Update tutorial information</p>
    </div>
    
    <?php if ($success_message): ?>
    <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
    <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <!-- Edit Tutorial Form -->
    <div class="card slide-in mb-4">
        <div class="card-header">
            <h2>Tutorial Details</h2>
        </div>
        <div class="card-body">
            <form method="post" action="edit_tutorial.php?id=<?php echo $tutorial_id; ?>">
                <div class="mb-3">
                    <label for="title" class="form-label">Tutorial Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($tutorial['title']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Tutorial Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($tutorial['description']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-select" id="category" name="category" required>
                        <?php foreach ($validCategories as $cat): ?>
                        <option value="<?php echo $cat; ?>" <?php echo ($tutorial['category'] == $cat) ? 'selected' : ''; ?>><?php echo ucfirst($cat); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="course.php?category=<?php echo $tutorial['category']; ?>" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Tutorial</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Tutorial Sections -->
    <div class="card slide-in mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2>Tutorial Sections</h2>
            <a href="add_section.php?tutorial_id=<?php echo $tutorial_id; ?>" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Add Section</a>
        </div>
        <div class="card-body">
            <?php
            // Get tutorial sections
            $sections = get_tutorial_sections($tutorial_id);
            
            if (empty($sections)):
            ?>
            <div class="alert alert-info">No sections found. Add sections to this tutorial.</div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Title</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sections as $section): ?>
                        <tr>
                            <td><?php echo $section['section_order']; ?></td>
                            <td><?php echo htmlspecialchars($section['title']); ?></td>
                            <td>
                                <a href="edit_section.php?id=<?php echo $section['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i> Edit</a>
                                <a href="delete_section.php?id=<?php echo $section['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this section?')"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Code Examples -->
    <div class="card slide-in mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2>Code Examples</h2>
            <a href="add_code_example.php?tutorial_id=<?php echo $tutorial_id; ?>" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Add Example</a>
        </div>
        <div class="card-body">
            <?php
            // Get code examples
            global $conn;
            $stmt = $conn->prepare("SELECT * FROM code_examples WHERE tutorial_id = ?");
            $stmt->bind_param("i", $tutorial_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $examples = [];
            while ($row = $result->fetch_assoc()) {
                $examples[] = $row;
            }
            
            if (empty($examples)):
            ?>
            <div class="alert alert-info">No code examples found. Add examples to this tutorial.</div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Language</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($examples as $example): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($example['title']); ?></td>
                            <td><?php echo htmlspecialchars($example['language']); ?></td>
                            <td>
                                <a href="edit_code_example.php?id=<?php echo $example['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i> Edit</a>
                                <a href="delete_code_example.php?id=<?php echo $example['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this code example?')"><i class="fas fa-trash"></i> Delete</a>
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
