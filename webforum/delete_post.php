<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$post_id = intval($_GET['id'] ?? 0);
$current_user_id = $_SESSION['user_id'];
$current_role = $_SESSION['role'] ?? 'user';

$stmt = $pdo->prepare("SELECT user_id FROM blogs WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if ($post && ($post['user_id'] == $current_user_id || $current_role === 'admin')) {
    $deleteStmt = $pdo->prepare("DELETE FROM blogs WHERE id = ?");
    $deleteStmt->execute([$post_id]);
}

header("Location: index.php");
exit;
?>