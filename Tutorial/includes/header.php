<?php
/**
 * Informatika Hub - Header Template
 * 
 * This file contains the header section that is included in all pages.
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    // Session configuration (must be set before session starts)
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS
    
    session_start();
}

// Include configuration and functions
require_once 'config.php';

// Set default language and get theme
$current_language = 'en'; // Fixed to English as requested
$current_theme = get_current_theme();

// Set theme attribute if dark mode
$theme_attribute = ($current_theme == 'dark') ? 'data-theme="dark"' : '';

// include_once '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $current_language; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Modern Web Learning Platform</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/comment-styles.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Prism.js for code highlighting -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css">
    <!-- CodeMirror for live editor -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css">
</head>



<body <?php echo $theme_attribute; ?>>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Navigation -->
            <div class="col-md-3 col-lg-2 sidebar" id="sidebar">
                <div class="sidebar-header">
                    <h3><a href="../home.php">Informatika Hub</a></h3>
                    <div class="logo">IH</div>
                </div>
                <div class="sidebar-content">
                    <div class="navigation">
                        <h6 class="sidebar-heading"><?php echo translate('web_development'); ?></h6>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="course.php?category=html" class="nav-link <?php echo (!isset($_GET['category']) || $_GET['category'] == 'html') ? 'active' : ''; ?>" data-category="html">HTML</a>
                            </li>
                            <li class="nav-item">
                                <a href="course.php?category=css" class="nav-link <?php echo (isset($_GET['category']) && $_GET['category'] == 'css') ? 'active' : ''; ?>" data-category="css">CSS</a>
                            </li>
                            <li class="nav-item">
                                <a href="course.php?category=javascript" class="nav-link <?php echo (isset($_GET['category']) && $_GET['category'] == 'javascript') ? 'active' : ''; ?>" data-category="javascript">JavaScript</a>
                            </li>
                            <li class="nav-item">
                                <a href="course.php?category=bootstrap" class="nav-link <?php echo (isset($_GET['category']) && $_GET['category'] == 'bootstrap') ? 'active' : ''; ?>" data-category="bootstrap">Bootstrap</a>
                            </li>
                        </ul>
                        
                        <h6 class="sidebar-heading mt-4"><?php echo translate('frontend_frameworks'); ?></h6>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="course.php?category=react" class="nav-link <?php echo (isset($_GET['category']) && $_GET['category'] == 'react') ? 'active' : ''; ?>" data-category="react">React</a>
                            </li>
                            <li class="nav-item">
                                <a href="course.php?category=vue" class="nav-link <?php echo (isset($_GET['category']) && $_GET['category'] == 'vue') ? 'active' : ''; ?>" data-category="vue">Vue.js</a>
                            </li>
                        </ul>
                        
                        <h6 class="sidebar-heading mt-4"><?php echo translate('web3_development'); ?></h6>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="course.php?category=blockchain" class="nav-link <?php echo (isset($_GET['category']) && $_GET['category'] == 'blockchain') ? 'active' : ''; ?>" data-category="blockchain">Blockchain Basics</a>
                            </li>
                            <li class="nav-item">
                                <a href="course.php?category=solidity" class="nav-link <?php echo (isset($_GET['category']) && $_GET['category'] == 'solidity') ? 'active' : ''; ?>" data-category="solidity">Solidity</a>
                            </li>
                            <li class="nav-item">
                                <a href="course.php?category=nft" class="nav-link <?php echo (isset($_GET['category']) && $_GET['category'] == 'nft') ? 'active' : ''; ?>" data-category="nft">NFT Development</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Main Content Area -->
            <div class="col-md-9 col-lg-10 ms-sm-auto main-content" id="main-content">
                <!-- Top Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" id="sidebar-toggle">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="d-flex align-items-center">
                            <div class="search-bar">
                                <input type="text" class="form-control" placeholder="<?php echo translate('search'); ?>..." id="search-input">
                                <i class="fas fa-search"></i>
                            </div>
                            <div class="user-profile ms-3">
                                <img src="https://via.placeholder.com/40" alt="User" class="rounded-circle">
                                <?php if (isset($_SESSION['user_id'])): ?>
                                <div class="nft-badge" title="Web3 Learner NFT Badge">
                                    <i class="fas fa-certificate"></i>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="d-none d-md-block ms-3">
                                <a href="editor.php" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-code me-1"></i> <?php echo ($current_language == 'en') ? 'Live Editor' : 'Editor Langsung'; ?>
                                </a>
                            </div>
                            <div class="d-none d-md-block ms-3">
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <span class="me-2">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                                    <a href="../Login/logout.php" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-sign-out-alt me-1"></i> Logout
                                    </a>
                                <?php else: ?>
                                    <a href="../Login/login.php" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-sign-in-alt me-1"></i> Login
                                    </a>
                                    <a href="../Login/signup.php" class="btn btn-sm btn-outline-info ms-1">
                                        <i class="fas fa-user-plus me-1"></i> Register
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </nav>
                
                <!-- Content Container -->
                <div class="content-container" id="content-container">
                    <!-- Content will be loaded dynamically via JavaScript -->
