<?php
/**
 * Informatika Hub - Configuration File
 * 
 * This file contains configuration settings for the Informatika Hub website.
 */

// Database configuration (for future implementation)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'informatika_hub');

// Site configuration
define('SITE_NAME', 'Informatika Hub');
define('SITE_URL', 'http://localhost/informatika-hub');
define('ADMIN_EMAIL', 'admin@informatikahub.com');

// API configuration
define('API_URL', 'api/');

// Default settings
define('DEFAULT_LANGUAGE', 'en');
define('DEFAULT_THEME', 'light');

// Available languages
$available_languages = [
    'en' => 'English',
    'id' => 'Bahasa Indonesia'
];

// Available themes
$available_themes = [
    'light' => 'Light Mode',
    'dark' => 'Dark Mode'
];

// Content categories
$content_categories = [
    'web_development' => [
        'html' => 'HTML',
        'css' => 'CSS',
        'javascript' => 'JavaScript',
        'bootstrap' => 'Bootstrap'
    ],
    'frontend_frameworks' => [
        'react' => 'React',
        'vue' => 'Vue.js'
    ],
    'web3_development' => [
        'blockchain' => 'Blockchain Basics',
        'solidity' => 'Solidity',
        'nft' => 'NFT Development'
    ]
];

// NFT badges configuration
$nft_badges = [
    'html' => [
        'name' => 'HTML Master',
        'description' => 'Awarded for completing the HTML curriculum',
        'image' => 'assets/images/badges/html_badge.png'
    ],
    'css' => [
        'name' => 'CSS Stylist',
        'description' => 'Awarded for completing the CSS curriculum',
        'image' => 'assets/images/badges/css_badge.png'
    ],
    'javascript' => [
        'name' => 'JavaScript Wizard',
        'description' => 'Awarded for completing the JavaScript curriculum',
        'image' => 'assets/images/badges/js_badge.png'
    ],
    'web3' => [
        'name' => 'Web3 Pioneer',
        'description' => 'Awarded for completing the Web3 curriculum',
        'image' => 'assets/images/badges/web3_badge.png'
    ]
];

// Error reporting (set to 0 for production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session configuration is now in header.php

// Time zone
date_default_timezone_set('Asia/Jakarta');

// Load helper functions
require_once 'functions.php';
