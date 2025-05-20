<?php
/**
 * User topics page - displays all topics followed by a specific user
 */
require_once '../config/config.php';
require_once '../includes/user.php';
require_once '../includes/topic.php';

// Get user ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    addError("Invalid user ID");
    redirect('../index.php');
}

$userId = (int)$_GET['id'];

// Get user details
$user = getUserById($userId);

if (!$user) {
    addError("User not found");
    redirect('../index.php');
}

// Get page number for pagination
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Get user's followed topics
$followedTopics = getFollowedTopics($userId, $perPage, $offset);

// Get total followed topics count for pagination
$sql = "SELECT COUNT(*) as total FROM user_topic_follows WHERE user_id = ?";
$result = executePreparedStatement($sql, "i", [$userId]);
$totalTopics = $result->fetch_assoc()['total'];
$totalPages = ceil($totalTopics / $perPage);

// Include header
include '../includes/header.php';
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Topics Followed by <?php echo $user['username']; ?></h4>
            </div>
            <div class="card-body">
                <?php if (empty($followedTopics)): ?>
                    <div class="alert alert-info">
                        <p class="mb-0">No followed topics found.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($followedTopics as $topic): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 topic-card">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <a href="<?php echo SITE_URL; ?>/pages/topic.php?id=<?php echo $topic['topic_id']; ?>"><?php echo $topic['topic_name']; ?></a>
                                        </h5>
                                        <p class="card-text text-muted">
                                            <?php 
                                            echo !empty($topic['description']) ? substr($topic['description'], 0, 100) : 'No description available.';
                                            if (!empty($topic['description']) && strlen($topic['description']) > 100) {
                                                echo '...';
                                            }
                                            ?>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <small class="text-muted">
                                                <i class="fas fa-users me-1"></i> <?php echo $topic['followers_count']; ?> followers
                                            </small>
                                            <?php if (isLoggedIn() && $_SESSION['user_id'] == $userId): ?>
                                                <a href="<?php echo SITE_URL; ?>/pages/follow_topic.php?id=<?php echo $topic['topic_id']; ?>&action=unfollow" 
                                                   class="btn btn-sm btn-primary follow-topic-btn"
                                                   data-topic-id="<?php echo $topic['topic_id']; ?>">
                                                    <i class="fas fa-check me-1"></i> Following
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white">
                                        <small class="text-muted">
                                            Created by <?php echo $topic['creator_name']; ?> on <?php echo date('M d, Y', strtotime($topic['created_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo SITE_URL; ?>/pages/user_topics.php?id=<?php echo $userId; ?>&page=<?php echo $page - 1; ?>">
                                            <i class="fas fa-chevron-left"></i> Previous
                                        </a>
                                    </li>
                                <?php else: ?>
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="fas fa-chevron-left"></i> Previous</span>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?php echo SITE_URL; ?>/pages/user_topics.php?id=<?php echo $userId; ?>&page=<?php echo $i; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo SITE_URL; ?>/pages/user_topics.php?id=<?php echo $userId; ?>&page=<?php echo $page + 1; ?>">
                                            Next <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php else: ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">Next <i class="fas fa-chevron-right"></i></span>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
                
                <div class="text-center mt-3">
                    <a href="<?php echo SITE_URL; ?>/pages/profile.php?id=<?php echo $userId; ?>" class="btn btn-outline-primary">
                        <i class="fas fa-user me-1"></i> Back to Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include '../includes/footer.php';
?>
