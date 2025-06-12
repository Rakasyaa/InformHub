<?php
/**
 * Informatika Hub - Add Section Page
 * 
 * This file allows admins to add a new section to a tutorial.
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
$tutorial_id = isset($_GET['tutorial_id']) ? intval($_GET['tutorial_id']) : 0;

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
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $order = isset($_POST['order']) ? intval($_POST['order']) : 0;
    
    // Validate input
    if (empty($title) || empty($content)) {
        $error_message = 'Please fill in all required fields.';
    } else {
        // Add section
        if (add_tutorial_section($tutorial_id, $title, $content, $order, $user_id)) {
            $success_message = 'Section added successfully.';
        } else {
            $error_message = 'Failed to add section. Please try again.';
        }
    }
}

// Get the highest section order for the tutorial
$max_order = 0;
global $conn;
$stmt = $conn->prepare("SELECT MAX(section_order) as max_order FROM tutorial_sections WHERE tutorial_id = ?");
$stmt->bind_param("i", $tutorial_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $max_order = $row['max_order'] + 1;
}
?>

<!-- Admin Content -->
<div class="course-container fade-in">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="course.php?category=<?php echo $tutorial['category']; ?>"><?php echo $tutorial['title']; ?></a></li>
            <li class="breadcrumb-item active">Add Section</li>
        </ol>
    </nav>
    
    <!-- Admin Header -->
    <div class="content-header slide-in">
        <h1>Add New Section to "<?php echo $tutorial['title']; ?>"</h1>
        <p class="lead">Create a new section for this tutorial</p>
    </div>
    
    <?php if ($success_message): ?>
    <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
    <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <!-- Add Section Form -->
    <div class="card slide-in mb-4">
        <div class="card-header">
            <h2>Section Details</h2>
        </div>
        <div class="card-body">
            <form method="post" action="add_section.php?tutorial_id=<?php echo $tutorial_id; ?>">
                <div class="mb-3">
                    <label for="title" class="form-label">Section Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label">Section Content (HTML allowed)</label>
                    <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
                    <small class="text-muted">You can use HTML tags for formatting.</small>
                </div>
                <div class="mb-3">
                    <label for="order" class="form-label">Section Order</label>
                    <input type="number" class="form-control" id="order" name="order" value="<?php echo $max_order; ?>" min="1">
                    <small class="text-muted">The order in which this section will appear.</small>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="course.php?category=<?php echo $tutorial['category']; ?>" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Add Section</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Include footer
include_once 'includes/footer.php';
?>
