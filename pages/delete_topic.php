<?php
require_once '../config/config.php';
require_once '../includes/user.php';
require_once '../includes/topic.php';

$topicId = (int)$_GET['id'];

$success = deleteTopic($topicId, $_SESSION['user_id']);

redirect('pages/search.php');
?>
