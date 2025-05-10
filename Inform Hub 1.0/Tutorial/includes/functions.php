<?php
/**
 * Informatika Hub - Helper Functions
 * 
 * This file contains helper functions used throughout the Informatika Hub website.
 */

/**
 * Sanitize input to prevent XSS attacks
 * 
 * @param string $input The input to sanitize
 * @return string The sanitized input
 */
function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Get the current language from session or cookie
 * 
 * @return string The current language code (en/id)
 */
function get_current_language() {
    if (isset($_SESSION['language'])) {
        return $_SESSION['language'];
    } elseif (isset($_COOKIE['language'])) {
        return $_COOKIE['language'];
    } else {
        return DEFAULT_LANGUAGE;
    }
}

/**
 * Get the current theme from session or cookie
 * 
 * @return string The current theme (light/dark)
 */
function get_current_theme() {
    if (isset($_SESSION['theme'])) {
        return $_SESSION['theme'];
    } elseif (isset($_COOKIE['theme'])) {
        return $_COOKIE['theme'];
    } else {
        return DEFAULT_THEME;
    }
}

/**
 * Set the current language
 * 
 * @param string $language The language code to set (en/id)
 * @return bool True if successful, false otherwise
 */
function set_language($language) {
    global $available_languages;
    
    if (array_key_exists($language, $available_languages)) {
        $_SESSION['language'] = $language;
        setcookie('language', $language, time() + (86400 * 30), '/'); // 30 days
        return true;
    }
    
    return false;
}

/**
 * Set the current theme
 * 
 * @param string $theme The theme to set (light/dark)
 * @return bool True if successful, false otherwise
 */
function set_theme($theme) {
    global $available_themes;
    
    if (array_key_exists($theme, $available_themes)) {
        $_SESSION['theme'] = $theme;
        setcookie('theme', $theme, time() + (86400 * 30), '/'); // 30 days
        return true;
    }
    
    return false;
}

/**
 * Get a translated string based on the current language
 * 
 * @param string $key The translation key
 * @param array $placeholders Optional placeholders to replace in the string
 * @return string The translated string
 */
function translate($key, $placeholders = []) {
    $language = get_current_language();
    $translations = get_translations($language);
    
    if (isset($translations[$key])) {
        $text = $translations[$key];
        
        // Replace placeholders
        foreach ($placeholders as $placeholder => $value) {
            $text = str_replace('{' . $placeholder . '}', $value, $text);
        }
        
        return $text;
    }
    
    // Fallback to the key itself
    return $key;
}

/**
 * Get all translations for a specific language
 * 
 * @param string $language The language code
 * @return array The translations
 */
function get_translations($language) {
    $translations = [];
    
    // English translations (default)
    $translations['en'] = [
        'home' => 'Home',
        'search' => 'Search',
        'login' => 'Login',
        'register' => 'Register',
        'profile' => 'Profile',
        'settings' => 'Settings',
        'logout' => 'Logout',
        'dark_mode' => 'Dark Mode',
        'light_mode' => 'Light Mode',
        'language' => 'Language',
        'web_development' => 'Web Development',
        'frontend_frameworks' => 'Frontend Frameworks',
        'web3_development' => 'Web3 Development',
        'try_it_yourself' => 'Try it Yourself',
        'run_code' => 'Run Code',
        'reset_code' => 'Reset Code',
        'output' => 'Output',
        'progress' => 'Progress',
        'nft_badges' => 'NFT Badges',
        'complete_to_earn' => 'Complete this module to earn an exclusive NFT badge for your collection!',
        'welcome_message' => 'Welcome to Informatika Hub, your modern web learning platform!'
    ];
    
    // Indonesian translations
    $translations['id'] = [
        'home' => 'Beranda',
        'search' => 'Cari',
        'login' => 'Masuk',
        'register' => 'Daftar',
        'profile' => 'Profil',
        'settings' => 'Pengaturan',
        'logout' => 'Keluar',
        'dark_mode' => 'Mode Gelap',
        'light_mode' => 'Mode Terang',
        'language' => 'Bahasa',
        'web_development' => 'Pengembangan Web',
        'frontend_frameworks' => 'Framework Frontend',
        'web3_development' => 'Pengembangan Web3',
        'try_it_yourself' => 'Coba Sendiri',
        'run_code' => 'Jalankan Kode',
        'reset_code' => 'Reset Kode',
        'output' => 'Keluaran',
        'progress' => 'Kemajuan',
        'nft_badges' => 'Lencana NFT',
        'complete_to_earn' => 'Selesaikan modul ini untuk mendapatkan lencana NFT eksklusif untuk koleksi Anda!',
        'welcome_message' => 'Selamat datang di Informatika Hub, platform pembelajaran web modern Anda!'
    ];
    
    return isset($translations[$language]) ? $translations[$language] : $translations['en'];
}

/**
 * Check if a user has earned a specific NFT badge
 * 
 * @param int $user_id The user ID
 * @param string $badge_key The badge key
 * @return bool True if the user has earned the badge, false otherwise
 */
function has_earned_badge($user_id, $badge_key) {
    // This is a dummy function for now
    // In a real implementation, this would check the database
    
    // For demonstration purposes, let's say the user has earned HTML and CSS badges
    $earned_badges = ['html', 'css'];
    
    return in_array($badge_key, $earned_badges);
}

/**
 * Get user progress for a specific category
 * 
 * @param int $user_id The user ID
 * @param string $category The category key
 * @return int The progress percentage (0-100)
 */
function get_user_progress($user_id, $category) {
    // This is a dummy function for now
    // In a real implementation, this would calculate progress from the database
    
    // For demonstration purposes, let's return random progress
    $progress = [
        'html' => 100,
        'css' => 80,
        'javascript' => 60,
        'bootstrap' => 40,
        'react' => 30,
        'vue' => 20,
        'blockchain' => 50,
        'solidity' => 40,
        'nft' => 30
    ];
    
    return isset($progress[$category]) ? $progress[$category] : 0;
}

/**
 * Format a date according to the current language
 * 
 * @param string $date The date to format
 * @param string $format The format to use (optional)
 * @return string The formatted date
 */
function format_date($date, $format = null) {
    $language = get_current_language();
    
    if ($format === null) {
        // Default format based on language
        $format = ($language == 'en') ? 'F j, Y' : 'j F Y';
    }
    
    $timestamp = strtotime($date);
    
    if ($language == 'id') {
        // Indonesian month names
        $months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $month = date('n', $timestamp) - 1;
        
        return str_replace(
            date('F', $timestamp),
            $months[$month],
            date($format, $timestamp)
        );
    }
    
    return date($format, $timestamp);
}
