<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/header.php';

if (!isset($featured_posts)) {
    $featured_posts = []; 
   
}
if (!isset($latest_tutorials)) {
    $latest_tutorials = [];
    
}

$conn = getDbConnection(); // Get mysqli connection

// Latest tutorial
$sql = "SELECT tutorial_id, category, title, description FROM tutorial_content ORDER BY created_at DESC LIMIT 3";
$result = executeQuery($sql);

while ($row = $result->fetch_assoc()) {
    $latest_tutorials[] = $row;
}

// Hot Topics
$hot_topics = [];
$sql = "SELECT p.*, u.username, u.profile_image, t.topic_name,
        (SELECT COUNT(*) FROM comments WHERE post_id = p.post_id) as comments_count
        FROM posts p
        JOIN users u ON p.user_id = u.user_id
        JOIN topic_spaces t ON p.topic_id = t.topic_id
        ORDER BY p.created_at DESC
        LIMIT 4";
$result = executeQuery($sql);

while ($row = $result->fetch_assoc()) {
    $hot_topics[] = $row;
}

// Popular topics
$popularTopics = [];
$sql = "SELECT t.*, COUNT(f.follow_id) as followers_count
        FROM topic_spaces t
        LEFT JOIN user_topic_follows f ON t.topic_id = f.topic_id
        GROUP BY t.topic_id
        ORDER BY followers_count DESC
        LIMIT 5";
$result = executeQuery($sql);

while ($row = $result->fetch_assoc()) {
    $popularTopics[] = $row;
}

// Community Stats
$total_users = 0;
$total_posts = 0;
$result_total_users = $conn->query("SELECT COUNT(*) as count FROM users");
if ($result_total_users) {
    $row = $result_total_users->fetch_assoc();
    $total_users = (int)$row['count'];
    $result_total_users->free();
}

$result_total_posts = $conn->query("SELECT COUNT(*) as count FROM posts"); 
if ($result_total_posts) {
    $row = $result_total_posts->fetch_assoc();
    $total_posts = (int)$row['count'];
    $result_total_posts->free();
}
?>

<section class="Home">
        <div class="Home-overlay">
            <div class="container">
                <div class="Home-content">
                    <h1 class="display-4 fw-bold">Welcome to Informatika Hub</h1>
                    <p class="lead mb-4">Learn, Share, and Connect with the Community</p>
                    <div class="Home-buttons">
                        <a href="<?php echo SITE_URL; ?>/Tutorial/course.php" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-book me-2"></i>Start Learning
                        </a>
                        <a href="index.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-comments me-2"></i>Join Discussion
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Courses Section -->
    <section id="latest-courses" class="py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="display-5 fw-bold">Latest Courses</h2>
                <p class="lead">Explore our latest tutorials</p>
            </div>
            
            <div class="row g-4">
                <?php foreach ($latest_tutorials as $tutorial): ?>
                    <div class="col-md-4">
                        <div class="card course-card h-100">
                            <a href="pages/tutorial.php?id=<?php echo urlencode($tutorial['tutorial_id']); ?>">
                                <div class="card-body">
                                    <div class="course-category mb-2">
                                        <span class="badge bg-primary">
                                            <?php echo $tutorial['category']; ?>
                                        </span>
                                    </div>
                                    <h5 class="card-title"><?php echo $tutorial['title']; ?></h5>
                                    <p class="card-text">
                                        <?php echo substr(strip_tags($tutorial['description']), 0, 100) . '...'; ?>
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Hot Topics Section -->
    <section id="hot-topics" class="py-5 bg-light">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="display-5 fw-bold">Hot Topics</h2>
                <p class="lead">Join the most active discussions</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="mb-0">Latest Discussions</h4>
                                <a href="index.php" class="btn btn-outline-primary btn-sm">
                                    View All
                                </a>
                            </div>
                            <?php foreach ($hot_topics as $hot_topics): ?>
                                <a class="biasa" href="pages/post.php?id=<?php echo $hot_topics['post_id']; ?>">
                                <div class="card post-card">
                                    <div class="card-header bg-white">
                                        <div class="post-header">
                                            <img src="<?php if (empty($hot_topics['profile_image'])) {echo 'assets/img/default.jpg'; } else { echo UPLOAD_URL . $hot_topics['profile_image']; } ?>" class="post-avatar">
                                            <div>
                                                <h5 class="mb-0"><?php echo $hot_topics['title']; ?></h5>
                                                <div class="post-meta">
                                                    <span>Posted by <a href="<?php echo SITE_URL; ?>/pages/profile.php?id=<?php echo $hot_topics['user_id']; ?>"><?php echo $hot_topics['username']; ?></a></span>
                                                    <span class="mx-1">•</span>
                                                    <span>in <a href="<?php echo SITE_URL; ?>/pages/topic.php?id=<?php echo $hot_topics['topic_id']; ?>"><?php echo $hot_topics['topic_name']; ?></a></span>
                                                    <span class="mx-1">•</span>
                                                    <span><?php 
                                                        $createdAt = !empty($hot_topics['topic_created_at']) ? $hot_topics['topic_created_at'] : date('Y-m-d H:i:s');
                                                        echo date('M d, Y', strtotime($createdAt)); 
                                                    ?></span>
                                                </div>
                                            </div>  
                                        </div>
                            </a>
                                        <div class="card-body">
                                            <div class="post-content">
                                                <?php 
                                                // Limit content preview to 200 characters
                                                echo nl2br(substr($hot_topics['content'], 0, 200));
                                                if (strlen($hot_topics['content']) > 200) {
                                                    echo '... <a href="pages/post.php?id=' . $hot_topics['post_id'] . '">Read more</a>';
                                                }
                                                ?>
                                                
                                                <?php if ($hot_topics['media_url'] && $hot_topics['media_type'] === 'image'): ?>
                                                    <div class="mt-3">
                                                        <img src="<?php echo UPLOAD_URL . $hot_topics['media_url']; ?>" alt="Post image" class="post-image">
                                                    </div>
                                                <?php elseif ($hot_topics['media_url'] && $hot_topics['media_type'] === 'video'): ?>
                                                    <div class="mt-3">
                                                        <video src="<?php echo UPLOAD_URL . $hot_topics['media_url']; ?>" controls class="post-video"></video>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Popular topics -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Popular Topics</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($popularTopics)): ?>
                                <p>No topics found.</p>
                            <?php else: ?>
                                <div class="list-group">
                                    <?php foreach ($popularTopics as $topic): ?>
                                        <a href="<?php echo SITE_URL; ?>/pages/topic.php?id=<?php echo $topic['topic_id']; ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <?php echo $topic['topic_name']; ?>
                                            <span class="badge bg-primary rounded-pill"><?php echo $topic['followers_count']; ?> followers</span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                                <div class="mt-3">
                                    <a href="<?php echo SITE_URL; ?>/pages/search.php" class="btn btn-outline-primary btn-sm w-100">View All Topics</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Community Stats Section -->
    <section id="community-stats" class="py-5 community-stats-section bg-light">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="display-5 fw-bold">Community Stats</h2>
                <p class="lead">See how our community is growing</p>
            </div>
            
            <div class="row justify-content-center gap-4">
                <div class="stats members">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="mb-0"><?php echo formatNumber($total_users); ?></h3>
                            <span class="text-muted">Members</span>
                        </div>
                    </div>
                </div>

                <div class="stats posts">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="mb-0"><?php echo formatNumber($total_posts); ?></h3>
                            <span class="text-muted">Posts</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php require_once 'includes/footer.php'; ?>