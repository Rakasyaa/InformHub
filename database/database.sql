-- Database schema for Learning Forum
-- Create database
CREATE DATABASE IF NOT EXISTS informhub;
USE informhub;

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
INSERT INTO `topic_spaces` (`topic_id`, `topic_name`, `description`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'HTML', 'Learn and discuss HTML basics and advanced techniques', 1, '2025-05-20 04:48:44', '2025-05-20 04:48:44'),
(2, 'CSS', 'Everything about styling your web pages with CSS', 1, '2025-05-20 04:48:44', '2025-05-20 04:48:44'),
(3, 'JavaScript', 'JavaScript programming language discussions', 1, '2025-05-20 04:48:44', '2025-05-20 04:48:44'),
(4, 'PHP', 'Server-side scripting with PHP', 1, '2025-05-20 04:48:44', '2025-05-20 04:48:44'),
(5, 'MySQL', 'Database design and queries using MySQL', 1, '2025-05-20 04:48:44', '2025-05-20 04:48:44');

-- Sample posts
INSERT INTO posts (user_id, topic_id, title, content) VALUES
(1, 1, 'HTML5 Semantic Elements Guide', 'Here\'s a comprehensive guide to semantic HTML5 elements and when to use them...'),
(1, 2, 'CSS Grid vs Flexbox', 'When should you use CSS Grid versus Flexbox? Let\'s discuss the pros and cons of each...'),
(1, 3, 'Understanding JavaScript Promises', 'Promises are a powerful way to handle asynchronous operations in JavaScript...');

CREATE TABLE `comments_tutorial` (
  `id` int(11) NOT NULL,
  `tutorial_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `comments_tutorial` (`id`, `tutorial_id`, `user_id`, `content`, `is_pinned`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Great tutorial on HTML basics!', 0, '2025-05-21 16:14:29', '2025-05-21 16:14:29'),
(2, 2, 1, 'CSS is so powerful for styling web pages.', 0, '2025-05-21 16:14:29', '2025-05-21 16:14:29');

CREATE TABLE `tutorial_content` (
  `id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tutorial_content` (`id`, `category`, `title`, `description`, `created_at`, `updated_at`, `created_by`) VALUES
(1, 'html', 'HTML Fundamentals', 'Learn the basics of HTML, the building blocks of web pages', '2025-05-21 16:14:29', '2025-05-21 16:14:29', NULL),
(2, 'css', 'CSS Styling', 'Learn how to style your HTML elements with CSS', '2025-05-21 16:14:29', '2025-05-21 16:14:29', NULL);

CREATE TABLE `tutorial_sections` (
  `id` int(11) NOT NULL,
  `tutorial_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `section_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `tutorial_sections` (`id`, `tutorial_id`, `title`, `content`, `section_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Introduction to HTML', '<p>HTML (HyperText Markup Language) is the standard markup language for creating web pages. It describes the structure of a web page and consists of a series of elements that tell the browser how to display the content.</p>', 1, '2025-05-21 16:14:29', '2025-05-21 16:14:29'),
(2, 1, 'HTML Elements', '<p>HTML elements are represented by tags. Tags are enclosed in angle brackets, and come in pairs with opening and closing tags.</p><p>Example: <code>&lt;h1&gt;This is a heading&lt;/h1&gt;</code></p>', 2, '2025-05-21 16:14:29', '2025-05-21 16:14:29'),
(3, 1, 'HTML Attributes', '<p>HTML attributes provide additional information about an element. They are always specified in the start tag and usually come in name/value pairs.</p><p>Example: <code>&lt;a href=\"https://www.example.com\"&gt;Visit Example.com&lt;/a&gt;</code></p>', 3, '2025-05-21 16:14:29', '2025-05-21 16:14:29'),
(4, 2, 'Introduction to CSS', '<p>CSS (Cascading Style Sheets) is used to style and layout web pages. It describes how HTML elements should be displayed.</p>', 1, '2025-05-21 16:14:29', '2025-05-21 16:14:29'),
(5, 2, 'CSS Selectors', '<p>CSS selectors are used to \"find\" (or select) the HTML elements you want to style. They can be element selectors, class selectors, or ID selectors.</p>', 2, '2025-05-21 16:14:29', '2025-05-21 16:14:29'),
(6, 2, 'CSS Box Model', '<p>The CSS box model is essentially a box that wraps around every HTML element. It consists of margins, borders, padding, and the actual content.</p>', 3, '2025-05-21 16:14:29', '2025-05-21 16:14:29');


CREATE TABLE `code_examples` (
`id` int(11) NOT NULL,
`tutorial_id` int(11) NOT NULL,
`title` varchar(255) NOT NULL,
`description` text NOT NULL,
`code` text NOT NULL,
`language` varchar(50) NOT NULL DEFAULT 'html',
`created_at` timestamp NOT NULL DEFAULT current_timestamp(),
`updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `code_examples` (`id`, `tutorial_id`, `title`, `description`, `code`, `language`, `created_at`, `updated_at`) VALUES
(1, 1, 'Basic HTML Document', 'A simple HTML document structure', '<!DOCTYPE html>\n<html>\n<head>\n  <title>My First Web Page</title>\n</head>\n<body>\n  <h1>Welcome to My Web Page</h1>\n  <p>This is a paragraph.</p>\n</body>\n</html>', 'html', '2025-05-21 16:14:29', '2025-05-21 16:14:29'),
(2, 2, 'Basic CSS Styling', 'Simple CSS styling for a webpage', 'body {\n  font-family: Arial, sans-serif;\n  margin: 0;\n  padding: 20px;\n  background-color: #f0f0f0;\n}\n\nh1 {\n  color: #0066cc;\n  text-align: center;\n}\n\np {\n  line-height: 1.6;\n  margin-bottom: 15px;\n}', 'css', '2025-05-21 16:14:29', '2025-05-21 16:14:29');
