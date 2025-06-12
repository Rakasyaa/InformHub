<?php
/**
 * Informatika Hub - Tutorial Helper Functions
 * 
 * This file contains helper functions for the tutorial section.
 */

require_once 'db_connect.php';

/**
 * Get tutorial content from database
 * 
 * @param string $category The tutorial category
 * @return array The tutorial content
 */
function get_tutorial_content($category) {
    global $conn;
    
    // Check if the tutorial_content table exists
    $tableExists = false;
    $result = $conn->query("SHOW TABLES LIKE 'tutorial_content'");
    if ($result && $result->num_rows > 0) {
        $tableExists = true;
    }
    
    if ($tableExists) {
        try {
            // Prepare statement to prevent SQL injection
            $stmt = $conn->prepare("SELECT * FROM tutorial_content WHERE category = ?");
            $stmt->bind_param("s", $category);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $tutorial = $result->fetch_assoc();
                
                // Get tutorial sections
                $sections = get_tutorial_sections($tutorial['id']);
                $tutorial['sections'] = $sections;
                
                // Get code example
                $code_example = get_code_example($tutorial['id']);
                if ($code_example) {
                    $tutorial['codeExample'] = $code_example;
                }
                
                return $tutorial;
            }
        } catch (Exception $e) {
            // If there's an error, return default content
        }
    }
    
    // Return default content if no tutorial found or tables don't exist
    return [
        'title' => ucfirst($category),
        'description' => 'Learn about ' . ucfirst($category),
        'sections' => [],
        'category' => in_array($category, ['blockchain', 'solidity', 'nft']) ? 'web3' : 'frontend'
    ];
}

/**
 * Get tutorial sections from database
 * 
 * @param int $tutorial_id The tutorial ID
 * @return array The tutorial sections
 */
function get_tutorial_sections($tutorial_id) {
    global $conn;
    
    // Check if the tutorial_sections table exists
    $tableExists = false;
    $result = $conn->query("SHOW TABLES LIKE 'tutorial_sections'");
    if ($result && $result->num_rows > 0) {
        $tableExists = true;
    }
    
    if ($tableExists) {
        try {
            $stmt = $conn->prepare("SELECT * FROM tutorial_sections WHERE tutorial_id = ? ORDER BY section_order ASC");
            $stmt->bind_param("i", $tutorial_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $sections = [];
            while ($row = $result->fetch_assoc()) {
                $sections[] = $row;
            }
            
            return $sections;
        } catch (Exception $e) {
            // If there's an error, return an empty array
            return [];
        }
    }
    
    return [];
}

/**
 * Get code example from database
 * 
 * @param int $tutorial_id The tutorial ID
 * @return array|null The code example or null if not found
 */
function get_code_example($tutorial_id) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM code_examples WHERE tutorial_id = ? LIMIT 1");
    $stmt->bind_param("i", $tutorial_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Get comments for a tutorial
 * 
 * @param int $tutorial_id The tutorial ID
 * @return array The comments
 */
function get_tutorial_comments($tutorial_id) {
    global $conn;
    
    // Check if the comments table exists
    $tableExists = false;
    $result = $conn->query("SHOW TABLES LIKE 'comments'");
    if ($result && $result->num_rows > 0) {
        $tableExists = true;
    }
    
    if ($tableExists) {
        try {
            // Get pinned comments first, then others ordered by creation date
            $stmt = $conn->prepare("
                SELECT c.*, u.username 
                FROM comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.tutorial_id = ? 
                ORDER BY c.is_pinned DESC, c.created_at DESC
            ");
            $stmt->bind_param("i", $tutorial_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $comments = [];
            while ($row = $result->fetch_assoc()) {
                $comments[] = $row;
            }
            
            return $comments;
        } catch (Exception $e) {
            // If there's an error, return an empty array
            return [];
        }
    }
    
    return [];
}

/**
 * Add a comment to a tutorial
 * 
 * @param int $tutorial_id The tutorial ID
 * @param int $user_id The user ID
 * @param string $content The comment content
 * @return bool True if successful, false otherwise
 */
function add_tutorial_comment($tutorial_id, $user_id, $content) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO comments (tutorial_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $tutorial_id, $user_id, $content);
    
    return $stmt->execute();
}

/**
 * Toggle pin status of a comment
 * 
 * @param int $comment_id The comment ID
 * @param int $user_id The user ID (must be admin)
 * @return bool True if successful, false otherwise
 */
function toggle_pin_comment($comment_id, $user_id) {
    global $conn;
    
    // Check if user is admin
    if (!is_admin($user_id)) {
        return false;
    }
    
    // Toggle pin status
    $stmt = $conn->prepare("UPDATE comments SET is_pinned = NOT is_pinned WHERE id = ?");
    $stmt->bind_param("i", $comment_id);
    
    return $stmt->execute();
}

/**
 * Add a new tutorial
 * 
 * @param string $category The tutorial category
 * @param string $title The tutorial title
 * @param string $description The tutorial description
 * @param int $user_id The user ID (must be admin)
 * @return int|bool The new tutorial ID if successful, false otherwise
 */
function add_tutorial($category, $title, $description, $user_id) {
    global $conn;
    
    // Check if user is admin
    if (!is_admin($user_id)) {
        return false;
    }
    
    $stmt = $conn->prepare("INSERT INTO tutorial_content (category, title, description, created_by) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $category, $title, $description, $user_id);
    
    if ($stmt->execute()) {
        return $conn->insert_id;
    }
    
    return false;
}

/**
 * Add a section to a tutorial
 * 
 * @param int $tutorial_id The tutorial ID
 * @param string $title The section title
 * @param string $content The section content
 * @param int $order The section order
 * @param int $user_id The user ID (must be admin)
 * @return bool True if successful, false otherwise
 */
function add_tutorial_section($tutorial_id, $title, $content, $order, $user_id) {
    global $conn;
    
    // Check if user is admin
    if (!is_admin($user_id)) {
        return false;
    }
    
    $stmt = $conn->prepare("INSERT INTO tutorial_sections (tutorial_id, title, content, section_order) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $tutorial_id, $title, $content, $order);
    
    return $stmt->execute();
}

/**
 * Add a code example to a tutorial
 * 
 * @param int $tutorial_id The tutorial ID
 * @param string $title The example title
 * @param string $description The example description
 * @param string $code The example code
 * @param string $language The code language
 * @param int $user_id The user ID (must be admin)
 * @return bool True if successful, false otherwise
 */
function add_code_example($tutorial_id, $title, $description, $code, $language, $user_id) {
    global $conn;
    
    // Check if user is admin
    if (!is_admin($user_id)) {
        return false;
    }
    
    $stmt = $conn->prepare("INSERT INTO code_examples (tutorial_id, title, description, code, language) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $tutorial_id, $title, $description, $code, $language);
    
    return $stmt->execute();
}

/**
 * Update a tutorial
 * 
 * @param int $tutorial_id The tutorial ID
 * @param string $title The tutorial title
 * @param string $description The tutorial description
 * @param int $user_id The user ID (must be admin)
 * @return bool True if successful, false otherwise
 */
function update_tutorial($tutorial_id, $title, $description, $user_id) {
    global $conn;
    
    // Check if user is admin
    if (!is_admin($user_id)) {
        return false;
    }
    
    $stmt = $conn->prepare("UPDATE tutorial_content SET title = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $description, $tutorial_id);
    
    return $stmt->execute();
}

/**
 * Delete a tutorial
 * 
 * @param int $tutorial_id The tutorial ID
 * @param int $user_id The user ID (must be admin)
 * @return bool True if successful, false otherwise
 */
function delete_tutorial($tutorial_id, $user_id) {
    global $conn;
    
    // Check if user is admin
    if (!is_admin($user_id)) {
        return false;
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Delete comments
        $stmt = $conn->prepare("DELETE FROM comments WHERE tutorial_id = ?");
        $stmt->bind_param("i", $tutorial_id);
        $stmt->execute();
        
        // Delete code examples
        $stmt = $conn->prepare("DELETE FROM code_examples WHERE tutorial_id = ?");
        $stmt->bind_param("i", $tutorial_id);
        $stmt->execute();
        
        // Delete sections
        $stmt = $conn->prepare("DELETE FROM tutorial_sections WHERE tutorial_id = ?");
        $stmt->bind_param("i", $tutorial_id);
        $stmt->execute();
        
        // Delete tutorial
        $stmt = $conn->prepare("DELETE FROM tutorial_content WHERE id = ?");
        $stmt->bind_param("i", $tutorial_id);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        return true;
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        return false;
    }
}
?>
