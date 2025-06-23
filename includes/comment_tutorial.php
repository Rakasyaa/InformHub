<?php
$config_path = __DIR__ . '/../config/config.php';
require_once $config_path;

// Buat komentar baru
function createTutorialComment($userId, $tutorialId, $content) {
    if (empty($content)) {
        addError("Isi komentar tidak boleh kosong");
        return false;
    }

    if (!isLoggedIn()) {
        addError("Kamu harus login untuk berkomentar");
        return false;
    }

    $sql = "INSERT INTO comments_tutorial (tutorial_id, user_id, content) 
            VALUES (?, ?, ?)";
    $result = executePreparedStatement($sql, "iis", [$tutorialId, $userId, $content]);

    addSuccess("Komentar berhasil ditambahkan");
    return $result;
}

// Ambil komentar berdasarkan ID
function getTutorialCommentById($commentId) {
    $sql = "SELECT c.*, u.username, u.profile_image
            FROM comments_tutorial c
            JOIN users u ON c.user_id = u.user_id
            WHERE c.id = ?";
    $result = executePreparedStatement($sql, "i", [$commentId]);

    return ($result->num_rows === 1) ? $result->fetch_assoc() : null;
}

// Ambil semua komentar untuk tutorial
function getTutorialCommentsByTutorial($tutorialId) {
    $sql = "SELECT c.*, u.username, u.profile_image
            FROM comments_tutorial c
            JOIN users u ON c.user_id = u.user_id
            WHERE c.tutorial_id = ?
            ORDER BY c.created_at ASC";
    $result = executePreparedStatement($sql, "i", [$tutorialId]);

    $comments = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
    }

    return $comments;
}

// Perbarui komentar
function updateTutorialComment($commentId, $content) {
    if (empty($content)) {
        addError("Isi komentar tidak boleh kosong");
        return false;
    }

    $comment = getTutorialCommentById($commentId);
    if (!$comment) {
        addError("Komentar tidak ditemukan");
        return false;
    }

    if ($comment['user_id'] != $_SESSION['user_id'] && !isModerator()) {
        addError("Kamu tidak punya izin untuk mengedit komentar ini");
        return false;
    }

    $sql = "UPDATE comments_tutorial SET content = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
    $result = executePreparedStatement($sql, "si", [$content, $commentId]);

    addSuccess("Komentar berhasil diperbarui");
    return $result;
}

// Hapus komentar
function deleteTutorialComment($commentId) {
    $comment = getTutorialCommentById($commentId);
    if (!$comment) {
        addError("Komentar tidak ditemukan");
        return false;
    }

    if ($comment['user_id'] != $_SESSION['user_id'] && !isModerator()) {
        addError("Kamu tidak punya izin untuk menghapus komentar ini");
        return false;
    }

    $sql = "DELETE FROM comments_tutorial WHERE id = ?";
    $result = executePreparedStatement($sql, "i", [$commentId]);

    if ($result) {
        addSuccess("Komentar berhasil dihapus");
        return true;
    } else {
        addError("Gagal menghapus komentar");
        return false;
    }
}
