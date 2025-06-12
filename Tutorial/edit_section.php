<?php
/**
 * Informatika Hub - Edit Section Page
 * 
 * This file allows admins to edit an existing section.
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

// Get section ID from URL
$section_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check if section exists
$section = null;
$tutorial = null;
if ($section_id > 0) {
    global $conn;
    $stmt = $conn->prepare("SELECT s.*, t.title as tutorial_title, t.category FROM tutorial_sections s 
                           JOIN tutorial_content t ON s.tutorial_id = t.id 
                           WHERE s.id = ?");
    $stmt->bind_param("i", $section_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $section = $result->fetch_assoc();
        
        // Get tutorial info
        $tutorial_id = $section['tutorial_id'];
        $stmt = $conn->prepare("SELECT * FROM tutorial_content WHERE id = ?");
        $stmt->bind_param("i", $tutorial_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $tutorial = $result->fetch_assoc();
        }
    }
}

// Redirect if section doesn't exist
if (!$section || !$tutorial) {
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
        // Update section
        global $conn;
        $stmt = $conn->prepare("UPDATE tutorial_sections SET title = ?, content = ?, section_order = ? WHERE id = ?");
        $stmt->bind_param("ssii", $title, $content, $order, $section_id);
        
        if ($stmt->execute()) {
            $success_message = 'Section updated successfully.';
            
            // Refresh section data
            $stmt = $conn->prepare("SELECT * FROM tutorial_sections WHERE id = ?");
            $stmt->bind_param("i", $section_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $section = $result->fetch_assoc();
            }
        } else {
            $error_message = 'Failed to update section. Please try again.';
        }
    }
}
?>

<!-- Admin Content -->
<div class="course-container fade-in">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="course.php?category=<?php echo $tutorial['category']; ?>"><?php echo $tutorial['title']; ?></a></li>
            <li class="breadcrumb-item active">Edit Section</li>
        </ol>
    </nav>
    
    <!-- Admin Header -->
    <div class="content-header slide-in">
        <h1>Edit Section</h1>
        <p class="lead">Update section content for "<?php echo $tutorial['title']; ?>"</p>
    </div>
    
    <?php if ($success_message): ?>
    <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
    <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <!-- Edit Section Form -->
    <div class="card slide-in mb-4">
        <div class="card-header">
            <h2>Section Details</h2>
        </div>
        <div class="card-body">
            <form method="post" action="edit_section.php?id=<?php echo $section_id; ?>">
                <div class="mb-3">
                    <label for="title" class="form-label">Section Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($section['title']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label">Section Content (HTML allowed)</label>
                    <textarea class="form-control" id="content" name="content" rows="10" required><?php echo htmlspecialchars($section['content']); ?></textarea>
                    <small class="text-muted">You can use HTML tags for formatting.</small>
                </div>
                <div class="mb-3">
                    <label for="order" class="form-label">Section Order</label>
                    <input type="number" class="form-control" id="order" name="order" value="<?php echo $section['section_order']; ?>" min="1">
                    <small class="text-muted">The order in which this section will appear.</small>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="course.php?category=<?php echo $tutorial['category']; ?>" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Section</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Include footer
include_once 'includes/footer.php';
?>
