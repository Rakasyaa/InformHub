<?php
/**
 * Post detail page - displays a single post with its comments
 */
// Calculate paths using __DIR__ for reliable path resolution
$config_path = __DIR__ . '/../config/config.php';
$user_path = __DIR__ . '/../includes/user.php';
$topic_path = __DIR__ . '/../includes/topic.php';
$post_path = __DIR__ . '/../includes/post.php';
$comment_path = __DIR__ . '/../includes/comment.php';

require_once $config_path;
require_once $user_path;
require_once $topic_path;
require_once $post_path;
require_once $comment_path;

// Check if post ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    addError("Invalid post ID");
    redirect('../index.php');
}

$postId = (int)$_GET['id'];

// Get post details
$post = getPostById($postId);

if (!$post) {
    addError("Post not found");
    redirect('../index.php');
}

// Get comments for this post
$comments = getCommentsByPost($postId);

// Check if user has voted on this post
$userVote = '';
if (isLoggedIn()) {
    $sql = "SELECT vote_type FROM votes 
            WHERE user_id = ? AND content_type = 'post' AND content_id = ?";
    $result = executePreparedStatement($sql, "ii", [$_SESSION['user_id'], $postId]);
    if ($result->num_rows > 0) {
        $userVote = $result->fetch_assoc()['vote_type'];
    }
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_content'])) {
    if (!isLoggedIn()) {
        addError("You must be logged in to comment");
        redirect("post.php?id=$postId");
    }
    
    $commentContent = sanitizeInput($_POST['comment_content']);
    $parentCommentId = isset($_POST['parent_comment_id']) ? (int)$_POST['parent_comment_id'] : null;
    
    if (empty($commentContent)) {
        addError("Comment content is required");
    } else {
        $result = createComment($_SESSION['user_id'], $postId, $commentContent, $parentCommentId);
        redirect("pages/post.php?id=$postId");
    }
}

// Include header
include __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <!-- Main content -->
    <div class="col-lg-8">
        <!-- Post -->
        <div class="card post-card">
            <div class="card-header bg-white">
                <div class="post-header">
                    <img src="<?php echo UPLOAD_URL . $post['profile_image']; ?>" alt="<?php echo $post['username']; ?>" class="post-avatar">
                    <div>
                        <h4 class="mb-0"><?php echo $post['title']; ?></h4>
                        <div class="post-meta">
                            <span>Posted by <a href="<?php echo SITE_URL; ?>/pages/profile.php?id=<?php echo $post['user_id']; ?>"><?php echo $post['username']; ?></a></span>
                            <span class="mx-1">•</span>
                            <span>in <a href="<?php echo SITE_URL; ?>/pages/topic.php?id=<?php echo $post['topic_id']; ?>"><?php echo $post['topic_name']; ?></a></span>
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
                    <?php echo nl2br($post['content']); ?>
                    
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
                        <?php if (isLoggedIn() && ($post['user_id'] == $_SESSION['user_id'] || isAdmin() || isModerator())): ?>
                            <a href="<?php echo SITE_URL; ?>/pages/edit_post.php?id=<?php echo $post['post_id']; ?>" class="btn btn-sm btn-outline-secondary ms-1">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            
                            <a href="<?php echo SITE_URL; ?>/pages/delete_post.php?id=<?php echo $post['post_id']; ?>" 
                                class="btn btn-sm btn-outline-danger ms-1 delete-post-btn"
                                data-delete-url="<?php echo SITE_URL; ?>/pages/delete_post.php?id=<?php echo $post['post_id']; ?>">
                                <i class="fas fa-trash me-1"></i> Delete
                            </a>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Comment form -->
        <?php if (isLoggedIn()): ?>
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Add a Comment</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo SITE_URL; ?>/pages/post.php?id=<?php echo $postId; ?>" method="POST">
                        <div class="mb-3">
                            <textarea class="form-control" name="comment_content" rows="3" placeholder="Write your comment here..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i> Submit Comment
                        </button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info mt-4">
                <p class="mb-0">Please <a href="<?php echo SITE_URL; ?>/pages/login.php">login</a> to comment on this post.</p>
            </div>
        <?php endif; ?>
        
        <!-- Comments -->
        <div class="comment-section">
            <h4 class="mb-3"><?php echo count($comments); ?> Comments</h4>
            
            <?php if (empty($comments)): ?>
                <div class="alert alert-info">
                    <p class="mb-0">No comments yet. Be the first to comment!</p>
                </div>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="card comment-card mb-3" id="comment-<?php echo $comment['comment_id']; ?>">
                        <div class="card-body">
                            <div class="comment-header">
                                <img src="<?php echo UPLOAD_URL . $comment['profile_image']; ?>" alt="<?php echo $comment['username']; ?>" class="comment-avatar">
                                <div>
                                    <h6 class="mb-0"><?php echo $comment['username']; ?></h6>
                                    <div class="comment-meta">
                                        <span><?php echo date('M d, Y', strtotime($comment['created_at'])); ?></span>
                                        <?php if ($comment['updated_at'] != $comment['created_at']): ?>
                                            <span class="mx-1">•</span>
                                            <span>Edited</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="comment-content">
                                <?php echo nl2br($comment['content']); ?>
                            </div>
                            
                            <div class="comment-actions">
                                <div class="vote-buttons">
                                    <button class="btn btn-sm comment-vote-btn">
                                        <i class="fas fa-arrow-up"></i> 
                                    </button>
                                    <span>0</span>
                                    <button class="btn btn-sm comment-vote-btn">
                                        <i class="fas fa-arrow-down"></i>
                                    </button>
                                </div>
                                
                                <div>
                                    <?php if (isLoggedIn()): ?>
                                        <button class="btn btn-sm btn-link">
                                            <i class="fas fa-reply me-1"></i> Reply
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if (isLoggedIn() && ($comment['user_id'] == $_SESSION['user_id'] || isAdmin() || isModerator())): ?>
                                        <a href="<?php echo SITE_URL; ?>/pages/edit_comment.php?id=<?php echo $comment['comment_id']; ?>" class="btn btn-sm btn-link">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </a>
                                        
                                        <!-- Tombol Delete -->
                                        <a href="<?php echo SITE_URL; ?>/pages/delete_comment.php?id=<?php echo $comment['comment_id']; ?>" 
                                        class="btn btn-sm btn-link text-danger delete-comment"
                                        data-delete-url="<?php echo SITE_URL; ?>/pages/delete_comment.php?id=<?php echo $comment['comment_id']; ?>">
                                        <i class="fas fa-trash me-1"></i> Delete
                                        </a>

                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Topic info -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Topic: <?php echo $post['topic_name']; ?></h5>
            </div>
            <div class="card-body">
                <?php
                $topic = getTopicById($post['topic_id']);
                $isFollowing = false;
                if (isLoggedIn()) {
                    $isFollowing = isFollowingTopic($_SESSION['user_id'], $post['topic_id']);
                }
                ?>
                <p><?php echo $topic['description']; ?></p>
                <p class="mb-0"><strong>Followers:</strong> <?php echo $topic['followers_count']; ?></p>
                
                <?php if (isLoggedIn()): ?>
                    <div class="d-grid gap-2 mt-3">
                        <a href="<?php echo SITE_URL; ?>/pages/follow_topic.php?id=<?php echo $post['topic_id']; ?>&action=<?php echo $isFollowing ? 'unfollow' : 'follow'; ?>" 
                           class="btn <?php echo $isFollowing ? 'btn-primary' : 'btn-outline-primary'; ?> follow-topic-btn"
                           data-topic-id="<?php echo $post['topic_id']; ?>">
                            <?php if ($isFollowing): ?>
                                <i class="fas fa-check me-1"></i> Following
                            <?php else: ?>
                                <i class="fas fa-plus me-1"></i> Follow
                            <?php endif; ?>
                        </a>
                        
                        <a href="<?php echo SITE_URL; ?>/pages/topic.php?id=<?php echo $post['topic_id']; ?>" class="btn btn-outline-primary">
                            <i class="fas fa-list me-1"></i> View All Posts in This Topic
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Author info -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">About the Author</h5>
            </div>
            <div class="card-body">
                <?php
                $author = getUserById($post['user_id']);
                ?>
                <div class="d-flex align-items-center mb-3">
                    <img src="<?php echo UPLOAD_URL . $author['profile_image']; ?>" alt="<?php echo $author['username']; ?>" class="post-avatar me-3">
                    <div>
                        <h5 class="mb-0"><?php echo $author['username']; ?></h5>
                        <p class="text-muted mb-0">
                            <?php echo $author['role'] ? '<span class="badge bg-primary">Moderator</span>' : ''; ?>
                            Member since <?php echo date('M Y', strtotime($author['created_at'])); ?>
                        </p>
                    </div>
                </div>
                
                <p><?php echo !empty($author['bio']) ? $author['bio'] : 'No bio available.'; ?></p>
                
                <a href="<?php echo SITE_URL; ?>/pages/profile.php?id=<?php echo $author['user_id']; ?>" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-user me-1"></i> View Profile
                </a>
            </div>
        </div>
        
        <!-- Related posts -->
        <?php
        // Get related posts from the same topic
        $relatedPosts = [];
        $sql = "SELECT p.post_id, p.title, p.created_at, u.username
                FROM posts p
                JOIN users u ON p.user_id = u.user_id
                WHERE p.topic_id = ? AND p.post_id != ?
                ORDER BY p.created_at DESC
                LIMIT 5";
        $result = executePreparedStatement($sql, "ii", [$post['topic_id'], $postId]);
        
        while ($row = $result->fetch_assoc()) {
            $relatedPosts[] = $row;
        }
        ?>
        
        <?php if (!empty($relatedPosts)): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Related Posts</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php foreach ($relatedPosts as $relatedPost): ?>
                            <a href="<?php echo SITE_URL; ?>/pages/post.php?id=<?php echo $relatedPost['post_id']; ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo $relatedPost['title']; ?></h6>
                                    <small><?php echo date('M d', strtotime($relatedPost['created_at'])); ?></small>
                                </div>
                                <small>Posted by <?php echo $relatedPost['username']; ?></small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- HTML JS -->
<div id="deleteModal" class="modal-overlay" hidden>
  <div class="modal-content">
    <p>Are you sure you want to delete this comment?</p>
    <div class="modal-buttons">
      <button id="cancelDelete">Cancel</button>
      <a id="confirmDelete" href="#" class="confirm-button">Yes, delete</a>
    </div>
  </div>
</div>

<div id="deletePostModal" class="modal-overlay" hidden>
  <div class="modal-content">
    <p>Are you sure you want to delete this post?</p>
    <div class="modal-buttons">
      <button id="cancelDeletePost">Cancel</button>
      <a id="confirmDeletePost" href="#" class="confirm-button">Yes, delete</a>
    </div>
  </div>
</div>


<?php
// Include footer
include '../includes/footer.php';
?>
