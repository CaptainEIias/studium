<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blog_id = intval($_POST['blog_id'] ?? 0);
    $content = trim($_POST['content'] ?? '');
    $user_id = $_SESSION['user_id'];

    if ($blog_id > 0 && !empty($content)) {
        $checkStmt = $pdo->prepare("SELECT id FROM blogs WHERE id = ?");
        $checkStmt->execute([$blog_id]);
        
        if ($checkStmt->fetch()) {
            $stmt = $pdo->prepare("INSERT INTO comments (blog_id, user_id, content) VALUES (?, ?, ?)");
            $stmt->execute([$blog_id, $user_id, $content]);
        }
    }
}

header("Location: index.php");
exit;
?>