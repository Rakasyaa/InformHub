<?php
/**
 * Search page - allows users to search for topic spaces
 */
// Calculate paths using __DIR__ for reliable path resolution
$config_path = __DIR__ . '/../config/config.php';
$user_path = __DIR__ . '/../includes/user.php';
$topic_path = __DIR__ . '/../includes/topic.php';

require_once $config_path;
require_once $user_path;
require_once $topic_path;

// Get search query if provided
$searchQuery = isset($_GET['q']) ? sanitizeInput($_GET['q']) : '';

// Get topics based on search query
$topics = [];
if (!empty($searchQuery)) {
    $topics = searchTopics($searchQuery);
} else {
    $topics = getAllTopics();
}

// Include header
include __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-search me-2"></i> Search Topic Spaces</h4>
            </div>
            <div class="card-body">
                <form action="<?php echo SITE_URL; ?>/pages/search.php" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control form-control-lg" placeholder="Search for topics..." value="<?php echo $searchQuery; ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                    </div>
                </form>
                
                <?php if (empty($topics)): ?>
                    <div class="alert alert-info">
                        <p class="mb-0">No topics found. <?php echo !empty($searchQuery) ? 'Try a different search term.' : ''; ?></p>
                    </div>
                <?php else: ?>
                    <h5 class="mb-3"><?php echo !empty($searchQuery) ? 'Search results for: ' . $searchQuery : 'All Topic Spaces'; ?></h5>
                    
                    <div class="row">
                        <?php foreach ($topics as $topic): ?>
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
                                            <?php if (isLoggedIn()): ?>
                                                <?php 
                                                $isFollowing = isFollowingTopic($_SESSION['user_id'], $topic['topic_id']);
                                                ?>
                                                <a href="<?php echo SITE_URL; ?>/pages/follow_topic.php?id=<?php echo $topic['topic_id']; ?>&action=<?php echo $isFollowing ? 'unfollow' : 'follow'; ?>" 
                                                   class="btn btn-sm <?php echo $isFollowing ? 'btn-primary' : 'btn-outline-primary'; ?> follow-topic-btn"
                                                   data-topic-id="<?php echo $topic['topic_id']; ?>">
                                                    <?php if ($isFollowing): ?>
                                                        <i class="fas fa-check me-1"></i> Following
                                                    <?php else: ?>
                                                        <i class="fas fa-plus me-1"></i> Follow
                                                    <?php endif; ?>
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
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (isModerator()): ?>
            <div class="text-center">
                <a href="<?php echo SITE_URL; ?>/pages/create_topic.php" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Create New Topic Space
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Include footer
include __DIR__ . '/../includes/footer.php';
?>
