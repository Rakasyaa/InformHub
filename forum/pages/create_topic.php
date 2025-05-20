<?php
/**
 * Create topic page - only accessible to moderators
 */
require_once '../config/config.php';
require_once '../includes/user.php';
require_once '../includes/topic.php';

// Check if user is logged in and is a moderator
if (!isLoggedIn()) {
    addError("You must be logged in to access this page");
    redirect('login.php');
}

if (!isModerator()) {
    addError("You don't have permission to create topic spaces");
    redirect('../index.php');
}

// Handle topic form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topicName = sanitizeInput($_POST['topic_name']);
    $description = sanitizeInput($_POST['description']);
    
    // Create topic
    $result = createTopicSpace($topicName, $description, $_SESSION['user_id']);
    
    if ($result) {
        // Redirect to the new topic
        redirect("topic.php?id=$result");
    }
}

// Include header
include '../includes/header.php';
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i> Create New Topic Space</h4>
            </div>
            <div class="card-body">
                <form action="<?php echo SITE_URL; ?>/pages/create_topic.php" method="POST">
                    <div class="mb-3">
                        <label for="topic_name" class="form-label">Topic Name</label>
                        <input type="text" class="form-control" id="topic_name" name="topic_name" required maxlength="100">
                        <div class="form-text">Choose a descriptive name for the topic space</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                        <div class="form-text">Provide a clear description of what this topic space is about</div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> Create Topic Space
                        </button>
                        <a href="<?php echo SITE_URL; ?>/pages/search.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include '../includes/footer.php';
?>
