<?php
/**
 * User posts page - displays all posts by a specific user
 */
require_once '../config/config.php';
require_once '../includes/user.php';
require_once '../includes/post.php';

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

// Get user's posts
$userPosts = [];
$sql = "SELECT p.*, t.topic_name,
        (SELECT COUNT(*) FROM comments WHERE post_id = p.post_id) as comments_count
        FROM posts p
        JOIN topic_spaces t ON p.topic_id = t.topic_id
        WHERE p.user_id = ?
        ORDER BY p.created_at DESC
        LIMIT ? OFFSET ?";
$result = executePreparedStatement($sql, "iii", [$userId, $perPage, $offset]);

while ($row = $result->fetch_assoc()) {
    $userPosts[] = $row;
}

// Get total posts count for pagination
$sql = "SELECT COUNT(*) as total FROM posts WHERE user_id = ?";
$result = executePreparedStatement($sql, "i", [$userId]);
$totalPosts = $result->fetch_assoc()['total'];
$totalPages = ceil($totalPosts / $perPage);

// Include header
include '../includes/header.php';
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Posts by <?php echo $user['username']; ?></h4>
            </div>
            <div class="card-body">
                <?php if (empty($userPosts)): ?>
                    <div class="alert alert-info">
                        <p class="mb-0">No posts found.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($userPosts as $post): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">
                                        <a href="<?php echo SITE_URL; ?>/pages/post.php?id=<?php echo $post['post_id']; ?>"><?php echo $post['title']; ?></a>
                                    </h5>
                                    <small><?php echo date('M d, Y', strtotime($post['created_at'])); ?></small>
                                </div>
                                <p class="mb-1">
                                    <?php 
                                    echo nl2br(substr($post['content'], 0, 150));
                                    if (strlen($post['content']) > 150) {
                                        echo '...';
                                    }
                                    ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-tag me-1"></i> 
                                        <a href="<?php echo SITE_URL; ?>/pages/topic.php?id=<?php echo $post['topic_id']; ?>"><?php echo $post['topic_name']; ?></a>
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-arrow-up me-1"></i> <?php echo $post['upvotes']; ?> upvotes
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-comment me-1"></i> <?php echo $post['comments_count']; ?> comments
                                    </small>
                                    
                                    <?php if (isLoggedIn() && ($post['user_id'] == $_SESSION['user_id'] || isModerator())): ?>
                                        <div>
                                            <a href="<?php echo SITE_URL; ?>/pages/edit_post.php?id=<?php echo $post['post_id']; ?>" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <a href="<?php echo SITE_URL; ?>/pages/delete_post.php?id=<?php echo $post['post_id']; ?>" 
                                               class="btn btn-sm btn-outline-danger ms-1"
                                               onclick="return confirm('Are you sure you want to delete this post?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    <?php endif; ?>
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
                                        <a class="page-link" href="<?php echo SITE_URL; ?>/pages/user_posts.php?id=<?php echo $userId; ?>&page=<?php echo $page - 1; ?>">
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
                                        <a class="page-link" href="<?php echo SITE_URL; ?>/pages/user_posts.php?id=<?php echo $userId; ?>&page=<?php echo $i; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo SITE_URL; ?>/pages/user_posts.php?id=<?php echo $userId; ?>&page=<?php echo $page + 1; ?>">
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
