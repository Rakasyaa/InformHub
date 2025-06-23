<?php
require_once '../config/config.php';
require_once '../includes/tutorial.php';
require_once '../includes/user.php';

// Pastikan user sudah login dan admin/moderator
if (!isLoggedIn() || !isModerator()) {
    $_SESSION['error'] = 'Anda tidak memiliki izin untuk menghapus tutorial';
    header('Location: ' . SITE_URL . '/pages/tutorial.php');
    exit;
}

// Pastikan ID tutorial ada dan valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = 'ID tutorial tidak valid';
    header('Location: ' . SITE_URL . '/pages/tutorial.php');
    exit;
}

$tutorial_id = (int)$_GET['id'];

// Verifikasi tutorial ada sebelum menghapus
$tutorial = getTutorialById($tutorial_id);
if (!$tutorial) {
    $_SESSION['error'] = 'Tutorial tidak ditemukan';
    header('Location: ' . SITE_URL . '/pages/tutorial.php');
    exit;
}

try {
    // Hapus tutorial
    $result = deleteTutorial($tutorial_id);

    if ($result) {
        $_SESSION['success'] = 'Tutorial berhasil dihapus';
    } else {
        throw new Exception('Gagal menghapus tutorial');
    }
} catch (Exception $e) {
    error_log('Error deleting tutorial #' . $tutorial_id . ': ' . $e->getMessage());
    $_SESSION['error'] = 'Terjadi kesalahan saat menghapus tutorial. Silakan coba lagi.';
}

// Redirect kembali ke halaman tutorial
header('Location: ' . SITE_URL . '/pages/tutorial.php');
exit;
