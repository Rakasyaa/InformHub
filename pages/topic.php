<?php
$config_path = __DIR__ . '/../config/config.php';
$user_path = __DIR__ . '/../includes/user.php';
$topic_path = __DIR__ . '/../includes/topic.php';
$post_path = __DIR__ . '/../includes/post.php';

require_once $config_path;
require_once $user_path;
require_once $topic_path;
require_once $post_path;

// Check if topic ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    addError("Invalid topic ID");
    redirect('search.php');
}

$topicId = (int)$_GET['id'];

// Get topic details
$topic = getTopicById($topicId);

if (!$topic) {
    addError("Topic not found");
    redirect('search.php');
}

// Check if user is following this topic
$isFollowing = false;
if (isLoggedIn()) {
    $isFollowing = isFollowingTopic($_SESSION['user_id'], $topicId);
}

// Get posts for this topic
$posts = getPostsByTopic($topicId);

// Include header
include __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <!-- Main content -->
    <div class="col-lg-8">
        <!-- Topic header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0"><?php echo $topic['topic_name']; ?></h2>
                    
                    <?php if (isLoggedIn()): ?>
                        <a href="<?php echo SITE_URL; ?>/pages/follow_topic.php?id=<?php echo $topicId; ?>&action=<?php echo $isFollowing ? 'unfollow' : 'follow'; ?>" 
                           class="btn <?php echo $isFollowing ? 'btn-primary' : 'btn-outline-primary'; ?> follow-topic-btn"
                           data-topic-id="<?php echo $topicId; ?>">
                            <?php if ($isFollowing): ?>
                                <i class="fas fa-check me-1"></i> Following
                            <?php else: ?>
                                <i class="fas fa-plus me-1"></i> Follow
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                </div>
                
                <p class="text-muted mt-2 mb-0">
                    <?php echo $topic['description']; ?>
                </p>
                
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <small class="text-muted">
                            <i class="fas fa-users me-1"></i> <span id="topic-followers-count"><?php echo $topic['followers_count']; ?></span> followers
                        </small>
                        <small class="text-muted ms-3">
                            <i class="fas fa-calendar me-1"></i> Created on <?php echo date('M d, Y', strtotime($topic['created_at'])); ?>
                        </small>
                    </div>
                    
                    <?php if (isLoggedIn()): ?>
                        <a href="<?php echo SITE_URL; ?>/pages/create_post.php?topic_id=<?php echo $topicId; ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> Create Post
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Posts -->
        <?php if (empty($posts)): ?>
            <div class="alert alert-info">
                <p class="mb-0">No posts in this topic yet. Be the first to create a post!</p>
            </div>
        <?php else: ?>
            <h4 class="mb-3">Posts in this topic</h4>
            
            <?php foreach ($posts as $post): ?>
                <div class="card post-card">
                    <div class="card-header bg-white">
                        <div class="post-header">
                            <img src="<?php echo UPLOAD_URL . $post['profile_image']; ?>" alt="<?php echo $post['username']; ?>" class="post-avatar">
                            <div>
                                <h5 class="mb-0"><?php echo $post['title']; ?></h5>
                                <div class="post-meta">
                                    <span>Posted by <a href="<?php echo SITE_URL; ?>/pages/profile.php?id=<?php echo $post['user_id']; ?>"><?php echo $post['username']; ?></a></span>
                                    <span class="mx-1">•</span>
                                    <span><?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                                    <?php if ($post['updated_at'] != $post['created_at']): ?>
                                        <span class="mx-1">•</span>
                                        <span>Edited on <?php echo date('M d, Y', strtotime($post['updated_at'])); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="post-content">
                            <?php 
                            // Limit content preview to 200 characters
                            echo nl2br(substr($post['content'], 0, 200));
                            if (strlen($post['content']) > 200) {
                                echo '... <a href="' . SITE_URL . '/pages/post.php?id=' . $post['post_id'] . '">Read more</a>';
                            }
                            ?>
                            
                            <?php if ($post['media_url'] && $post['media_type'] === 'image'): ?>
                                <div class="mt-3">
                                    <img src="<?php echo UPLOAD_URL . $post['media_url']; ?>" alt="Post image" class="post-image">
                                </div>
                            <?php elseif ($post['media_url'] && $post['media_type'] === 'video'): ?>
                                <div class="mt-3">
                                    <video src="<?php echo UPLOAD_URL . $post['media_url']; ?>" controls class="post-video"></video>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="post-actions">
                            <div class="vote-buttons">
                                <?php 
                                $userVote = '';
                                if (isLoggedIn()) {
                                    $sql = "SELECT vote_type FROM votes 
                                            WHERE user_id = ? AND content_type = 'post' AND content_id = ?";
                                    $result = executePreparedStatement($sql, "ii", [$_SESSION['user_id'], $post['post_id']]);
                                    if ($result->num_rows > 0) {
                                        $userVote = $result->fetch_assoc()['vote_type'];
                                    }
                                }
                                ?>
                                <button class="btn btn-sm <?php echo $userVote === 'upvote' ? 'btn-success' : 'btn-outline-success'; ?> post-vote-btn" 
                                        id="post-<?php echo $post['post_id']; ?>-upvote"
                                        data-post-id="<?php echo $post['post_id']; ?>" 
                                        data-vote-type="upvote" 
                                        <?php echo !isLoggedIn() ? 'disabled' : ''; ?>>
                                    <i class="fas fa-arrow-up"></i>
                                </button>
                                
                                <span class="vote-count" id="post-<?php echo $post['post_id']; ?>-votes">
                                    <?php echo $post['upvotes'] - $post['downvotes']; ?>
                                </span>
                                
                                <button class="btn btn-sm <?php echo $userVote === 'downvote' ? 'btn-danger' : 'btn-outline-danger'; ?> post-vote-btn" 
                                        id="post-<?php echo $post['post_id']; ?>-downvote"
                                        data-post-id="<?php echo $post['post_id']; ?>" 
                                        data-vote-type="downvote" 
                                        <?php echo !isLoggedIn() ? 'disabled' : ''; ?>>
                                    <i class="fas fa-arrow-down"></i>
                                </button>
                            </div>
                            
                            <div>
                                <a href="<?php echo SITE_URL; ?>/pages/post.php?id=<?php echo $post['post_id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-comment me-1"></i> <?php echo $post['comments_count']; ?> Comments
                                </a>
                                
                                <?php if (isLoggedIn() && ($post['user_id'] == $_SESSION['user_id'] || isModerator())): ?>
                                    <a href="<?php echo SITE_URL; ?>/pages/edit_post.php?id=<?php echo $post['post_id']; ?>" class="btn btn-sm btn-outline-secondary ms-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <a href="<?php echo SITE_URL; ?>/pages/delete_post.php?id=<?php echo $post['post_id']; ?>" 
                                       class="btn btn-sm btn-outline-danger ms-1"
                                       onclick="return confirm('Are you sure you want to delete this post?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Topic info -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">About This Topic</h5>
                <?php if (isModerator()): ?>
                    <a href="<?php echo SITE_URL; ?>/pages/delete_topic.php?id=<?php echo $topicId; ?>" 
                    class="btn btn-sm btn-outline-danger delete-topic-btn"
                    data-delete-url="<?php echo SITE_URL; ?>/pages/delete_topic.php?id=<?php echo $topicId; ?>">
                        <i class="fas fa-trash"></i> Delete Topic
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <p><?php echo $topic['description']; ?></p>
                <p class="mb-0"><strong>Created by:</strong> <?php echo $topic['creator_name']; ?></p>
                <p class="mb-0"><strong>Created on:</strong> <?php echo date('M d, Y', strtotime($topic['created_at'])); ?></p>
                <p class="mb-0"><strong>Followers:</strong> <?php echo $topic['followers_count']; ?></p>
                
                <?php if (isLoggedIn()): ?>
                    <div class="d-grid gap-2 mt-3">
                        <a href="<?php echo SITE_URL; ?>/pages/follow_topic.php?id=<?php echo $topicId; ?>&action=<?php echo $isFollowing ? 'unfollow' : 'follow'; ?>" 
                           class="btn <?php echo $isFollowing ? 'btn-primary' : 'btn-outline-primary'; ?> follow-topic-btn"
                           data-topic-id="<?php echo $topicId; ?>">
                            <?php if ($isFollowing): ?>
                                <i class="fas fa-check me-1"></i> Following
                            <?php else: ?>
                                <i class="fas fa-plus me-1"></i> Follow
                            <?php endif; ?>
                        </a>
                        
                        <a href="<?php echo SITE_URL; ?>/pages/create_post.php?topic_id=<?php echo $topicId; ?>" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> Create Post
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Related topics -->
        <?php
        // Get related topics
        $relatedTopics = [];
        $sql = "SELECT t.*, COUNT(f.follow_id) as followers_count
                FROM topic_spaces t
                LEFT JOIN user_topic_follows f ON t.topic_id = f.topic_id
                WHERE t.topic_id != ?
                GROUP BY t.topic_id
                ORDER BY followers_count DESC
                LIMIT 5";
        $result = executePreparedStatement($sql, "i", [$topicId]);
        
        while ($row = $result->fetch_assoc()) {
            $relatedTopics[] = $row;
        }
        ?>
        
        <?php if (!empty($relatedTopics)): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Related Topics</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php foreach ($relatedTopics as $relatedTopic): ?>
                            <a href="<?php echo SITE_URL; ?>/pages/topic.php?id=<?php echo $relatedTopic['topic_id']; ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <?php echo $relatedTopic['topic_name']; ?>
                                <span class="badge bg-primary rounded-pill"><?php echo $relatedTopic['followers_count']; ?> followers</span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-3">
                        <a href="<?php echo SITE_URL; ?>/pages/search.php" class="btn btn-outline-primary btn-sm w-100">View All Topics</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Topic Modal -->
<div id="deleteTopicModal" class="modal-overlay" hidden>
  <div class="modal-content">
    <p>Are you sure you want to delete this topic? This action cannot be undone.</p>
    <div class="modal-buttons">
      <button id="cancelDeleteTopic">Cancel</button>
      <a id="confirmDeleteTopic" href="#" class="confirm-button">Yes, Delete</a>
    </div>
  </div>
</div>

<?php
// Include footer
include __DIR__ . '/../includes/footer.php';
?>
