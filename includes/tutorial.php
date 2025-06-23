<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/user.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = $_SESSION['user_id'];
    
    switch ($action) {
        case 'create':
            $title = trim($_POST['title'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            // Process sections
            $sections = [];
            if (!empty($_POST['sections'])) {
                foreach ($_POST['sections'] as $section) {
                    if (!empty($section['title']) && !empty($section['content'])) {
                        $sections[] = [
                            'title' => $section['title'],
                            'content' => $section['content']
                        ];
                    }
                }
            }
            
            // Process code examples
            $codeExamples = [];
            if (!empty($_POST['code_examples'])) {
                foreach ($_POST['code_examples'] as $example) {
                    if (!empty($example['title']) && !empty($example['code'])) {
                        $codeExamples[] = [
                            'title' => $example['title'],
                            'description' => $example['description'] ?? '',
                            'code' => $example['code'],
                            'language' => $example['language'] ?? 'php'
                        ];
                    }
                }
            }
            
            // Create tutorial
            $tutorialId = createTutorial($userId, $title, $category, $description, $sections, $codeExamples);
            
            if ($tutorialId) {
                $_SESSION['success'] = 'Tutorial berhasil dibuat';
                header('Location: ' . SITE_URL . '/pages/tutorial.php?id=' . $tutorialId);
            } else {
                $_SESSION['error'] = 'Gagal membuat tutorial';
                header('Location: ' . SITE_URL . '/pages/create_tutorial.php');
            }
            exit;
            
        case 'update':
            // Check if user has permission to update
            if (!isModerator()) {
                $_SESSION['error'] = 'Anda tidak memiliki izin untuk memperbarui tutorial';
                header('Location: ' . SITE_URL . '/pages/tutorial.php');
                exit;
            }
            
            $tutorialId = (int)($_POST['tutorial_id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            // Validate required fields
            if (empty($title) || empty($category) || empty($description)) {
                $_SESSION['error'] = 'Judul, kategori, dan deskripsi tidak boleh kosong';
                header('Location: ' . SITE_URL . '/pages/update_tutorial.php?id=' . $tutorialId);
                exit;
            }
            
            // Process sections
            $sections = [];
            if (!empty($_POST['sections'])) {
                foreach ($_POST['sections'] as $section) {
                    if (!empty($section['title']) && !empty($section['content'])) {
                        $sections[] = [
                            'title' => trim($section['title']),
                            'content' => trim($section['content'])
                        ];
                    }
                }
            }
            
            // Process code examples
            $codeExamples = [];
            if (!empty($_POST['code_examples'])) {
                foreach ($_POST['code_examples'] as $example) {
                    if (!empty($example['title']) && !empty($example['code'])) {
                        $codeExamples[] = [
                            'title' => trim($example['title']),
                            'code' => trim($example['code'])
                        ];
                    }
                }
            }
            
            // Update tutorial
            $success = updateTutorial($tutorialId, $title, $category, $description, $sections, $codeExamples);
            
            if ($success) {
                $_SESSION['success'] = 'Tutorial berhasil diperbarui';
                header('Location: ' . SITE_URL . '/pages/tutorial.php?id=' . $tutorialId);
            } else {
                $_SESSION['error'] = 'Gagal memperbarui tutorial. Silakan coba lagi.';
                header('Location: ' . SITE_URL . '/pages/update_tutorial.php?id=' . $tutorialId);
            }
            exit;
            
        case 'delete':
            // Check if user has permission to delete
            if (!isModerator()) {
                $_SESSION['error'] = 'Anda tidak memiliki izin untuk menghapus tutorial';
                header('Location: ' . SITE_URL . '/pages/tutorial.php');
                exit;
            }
            
            $tutorialId = (int)($_POST['tutorial_id'] ?? 0);
            
            if ($tutorialId <= 0) {
                $_SESSION['error'] = 'ID tutorial tidak valid';
                header('Location: ' . SITE_URL . '/pages/tutorial.php');
                exit;
            }
            
            // Verify tutorial exists before attempting to delete
            $tutorial = getTutorialById($tutorialId);
            if (!$tutorial) {
                $_SESSION['error'] = 'Tutorial tidak ditemukan';
                header('Location: ' . SITE_URL . '/pages/tutorial.php');
                exit;
            }
            
            // Perform the deletion
            if (deleteTutorial($tutorialId)) {
                $_SESSION['success'] = 'Tutorial berhasil dihapus';
                header('Location: ' . SITE_URL . '/pages/tutorial.php');
            } else {
                $_SESSION['error'] = 'Gagal menghapus tutorial. Silakan coba lagi.';
                header('Location: ' . SITE_URL . '/pages/tutorial.php?id=' . $tutorialId);
            }
            exit;
    }
}

// Function to create a new tutorial
function createTutorial($userId, $title, $category, $description, $sections = [], $codeExamples = []) {
    $conn = getDbConnection();
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Insert tutorial content
        $stmt = $conn->prepare("INSERT INTO tutorial_content (title, category, description, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $title, $category, $description, $userId);
        $stmt->execute();
        
        $tutorialId = $conn->insert_id;
        
        // Insert sections
        if (!empty($sections)) {
            $sectionStmt = $conn->prepare("INSERT INTO tutorial_sections (tutorial_id, title, content, section_order) VALUES (?, ?, ?, ?)");
            
            foreach ($sections as $index => $section) {
                $order = $index + 1;
                $sectionStmt->bind_param("issi", $tutorialId, $section['title'], $section['content'], $order);
                $sectionStmt->execute();
            }
            $sectionStmt->close();
        }
        
        // Insert code examples
        if (!empty($codeExamples)) {
            $codeStmt = $conn->prepare("INSERT INTO code_examples (tutorial_id, title, code) VALUES (?, ?, ?)");
            
            foreach ($codeExamples as $example) {
                $codeStmt->bind_param("iss", $tutorialId, $example['title'], $example['code']);
                $codeStmt->execute();
            }
            $codeStmt->close();
        }
        
        // Commit transaction
        $conn->commit();
        return $tutorialId;
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        error_log("Error creating tutorial: " . $e->getMessage());
        return false;
    }
}

// Function to update an existing tutorial
function updateTutorial($tutorialId, $title, $category, $description, $sections = [], $codeExamples = []) {
    // Validate input
    if (!is_numeric($tutorialId) || $tutorialId <= 0) {
        error_log("Invalid tutorial ID: " . $tutorialId);
        return false;
    }
    
    $title = trim($title);
    $category = trim($category);
    $description = trim($description);
    
    if (empty($title) || empty($category) || empty($description)) {
        error_log("Missing required fields for tutorial update");
        return false;
    }
    
    $conn = getDbConnection();
    
    try {
        $conn->begin_transaction();
        
        // Update tutorial content
        $stmt = $conn->prepare("UPDATE tutorial_content SET title = ?, category = ?, description = ?, updated_at = NOW() WHERE tutorial_id = ?");
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }
        
        $stmt->bind_param("sssi", $title, $category, $description, $tutorialId);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update tutorial: " . $stmt->error);
        }
        $stmt->close();
        
        // Delete existing sections and code examples using prepared statements
        $tables = ['tutorial_sections', 'code_examples'];
        foreach ($tables as $table) {
            $stmt = $conn->prepare("DELETE FROM $table WHERE tutorial_id = ?");
            if (!$stmt) {
                throw new Exception("Failed to prepare delete statement for $table: " . $conn->error);
            }
            $stmt->bind_param("i", $tutorialId);
            if (!$stmt->execute()) {
                throw new Exception("Failed to delete from $table: " . $stmt->error);
            }
            $stmt->close();
        }
        
        // Insert updated sections
        if (!empty($sections)) {
            $sectionStmt = $conn->prepare("INSERT INTO tutorial_sections (tutorial_id, title, content, section_order) VALUES (?, ?, ?, ?)");
            if (!$sectionStmt) {
                throw new Exception("Failed to prepare section insert: " . $conn->error);
            }
            
            foreach ($sections as $index => $section) {
                if (empty($section['title']) || empty($section['content'])) {
                    continue; // Skip empty sections
                }
                
                $order = $index + 1;
                $sectionTitle = trim($section['title']);
                $sectionContent = trim($section['content']);
                
                $sectionStmt->bind_param("issi", $tutorialId, $sectionTitle, $sectionContent, $order);
                if (!$sectionStmt->execute()) {
                    throw new Exception("Failed to insert section: " . $sectionStmt->error);
                }
            }
            $sectionStmt->close();
        }
        
        // Insert updated code examples
        if (!empty($codeExamples)) {
            $codeStmt = $conn->prepare("INSERT INTO code_examples (tutorial_id, title, code) VALUES (?, ?, ?)");
            if (!$codeStmt) {
                throw new Exception("Failed to prepare code example insert: " . $conn->error);
            }
            
            foreach ($codeExamples as $example) {
                if (empty($example['title']) || empty($example['code'])) {
                    continue; // Skip empty examples
                }
                
                $exampleTitle = trim($example['title']);
                $exampleCode = trim($example['code']);
                
                $codeStmt->bind_param("iss", $tutorialId, $exampleTitle, $exampleCode);
                if (!$codeStmt->execute()) {
                    throw new Exception("Failed to insert code example: " . $codeStmt->error);
                }
            }
            $codeStmt->close();
        }
        
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        if (isset($conn)) {
            $conn->rollback();
        }
        error_log("Error updating tutorial #$tutorialId: " . $e->getMessage());
        return false;
    }
}

// Function to delete a tutorial
function deleteTutorial($tutorialId) {
    if (!is_numeric($tutorialId) || $tutorialId <= 0) {
        error_log("Invalid tutorial ID: " . $tutorialId);
        return false;
    }
    
    $conn = getDbConnection();
    
    try {
        $conn->begin_transaction();
        
        // Delete related records first using prepared statements
        $tables = ['tutorial_sections', 'code_examples', 'comments_tutorial'];
        foreach ($tables as $table) {
            $stmt = $conn->prepare("DELETE FROM $table WHERE tutorial_id = ?");
            $stmt->bind_param("i", $tutorialId);
            $stmt->execute();
            $stmt->close();
        }
        
        // Delete the tutorial
        $stmt = $conn->prepare("DELETE FROM tutorial_content WHERE tutorial_id = ?");
        $stmt->bind_param("i", $tutorialId);
        $affectedRows = $stmt->execute() ? $stmt->affected_rows : 0;
        $stmt->close();
        
        $conn->commit();
        return $affectedRows > 0;
        
    } catch (Exception $e) {
        if (isset($conn)) {
            $conn->rollback();
        }
        error_log("Error deleting tutorial #$tutorialId: " . $e->getMessage());
        return false;
    }
}

// Function to get tutorial by ID
function getTutorialById($tutorialId) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT * FROM tutorial_content WHERE tutorial_id = ?");
    $stmt->bind_param("i", $tutorialId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

// Function to get all tutorials
function getAllTutorials($limit = 10, $offset = 0) {
    $conn = getDbConnection();
    $limit = (int)$limit;
    $offset = (int)$offset;
    
    $result = $conn->query("SELECT * FROM tutorial_content ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
    $tutorials = [];
    
    while ($row = $result->fetch_assoc()) {
        $tutorials[] = $row;
    }
    
    return $tutorials;
}

// Function to get tutorial sections
function getTutorialSections($tutorialId) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT * FROM tutorial_sections WHERE tutorial_id = ? ORDER BY section_order ASC");
    $stmt->bind_param("i", $tutorialId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $sections = [];
    while ($row = $result->fetch_assoc()) {
        $sections[] = $row;
    }
    
    return $sections;
}

// Function to get tutorial code examples
function getTutorialCodeExamples($tutorialId) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT * FROM code_examples WHERE tutorial_id = ?");
    $stmt->bind_param("i", $tutorialId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $examples = [];
    while ($row = $result->fetch_assoc()) {
        $examples[] = $row;
    }
    
    return $examples;
}