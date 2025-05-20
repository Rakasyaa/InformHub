-- Database schema for Learning Forum
-- Create database
CREATE DATABASE IF NOT EXISTS forum_db;
USE forum_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    profile_image VARCHAR(255) DEFAULT 'default.jpg',
    bio TEXT,
    is_moderator BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Topic spaces table
CREATE TABLE IF NOT EXISTS topic_spaces (
    topic_id INT AUTO_INCREMENT PRIMARY KEY,
    topic_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
);

-- User topic follows table
CREATE TABLE IF NOT EXISTS user_topic_follows (
    follow_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    topic_id INT,
    followed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (topic_id) REFERENCES topic_spaces(topic_id) ON DELETE CASCADE,
    UNIQUE KEY user_topic_unique (user_id, topic_id)
);

-- Posts table
CREATE TABLE IF NOT EXISTS posts (
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    topic_id INT,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    media_url VARCHAR(255),
    media_type ENUM('image', 'video', 'none') DEFAULT 'none',
    upvotes INT DEFAULT 0,
    downvotes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (topic_id) REFERENCES topic_spaces(topic_id) ON DELETE CASCADE
);

-- Comments table
CREATE TABLE IF NOT EXISTS comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT,
    user_id INT,
    parent_comment_id INT DEFAULT NULL,
    content TEXT NOT NULL,
    upvotes INT DEFAULT 0,
    downvotes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (parent_comment_id) REFERENCES comments(comment_id) ON DELETE CASCADE
);

-- Votes table (for both posts and comments)
CREATE TABLE IF NOT EXISTS votes (
    vote_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    content_type ENUM('post', 'comment') NOT NULL,
    content_id INT NOT NULL,
    vote_type ENUM('upvote', 'downvote') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY user_content_unique (user_id, content_type, content_id)
);

-- Insert sample data for testing
-- Default admin user (password: admin123)
INSERT INTO users (username, email, password, is_moderator) 
VALUES ('admin', 'admin@example.com', '$2y$10$8WxmVVqLRNcJu/Wr9EBhouKRzKZSsAXbJQ7fF/PZ3VnZxdBYFbDXe', TRUE);

-- Sample topic spaces
INSERT INTO topic_spaces (topic_name, description, created_by) VALUES 
('HTML', 'Learn and discuss HTML basics and advanced techniques', 1),
('CSS', 'Everything about styling your web pages with CSS', 1),
('JavaScript', 'JavaScript programming language discussions', 1),
('PHP', 'Server-side scripting with PHP', 1),
('MySQL', 'Database design and queries using MySQL', 1);

-- Sample posts
INSERT INTO posts (user_id, topic_id, title, content) VALUES
(1, 1, 'HTML5 Semantic Elements Guide', 'Here\'s a comprehensive guide to semantic HTML5 elements and when to use them...'),
(1, 2, 'CSS Grid vs Flexbox', 'When should you use CSS Grid versus Flexbox? Let\'s discuss the pros and cons of each...'),
(1, 3, 'Understanding JavaScript Promises', 'Promises are a powerful way to handle asynchronous operations in JavaScript...');
