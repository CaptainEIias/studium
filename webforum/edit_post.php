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

$stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post || ($post['user_id'] != $current_user_id && $current_role !== 'admin')) {
    die("Zugriff verweigert oder Beitrag nicht gefunden.");
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $visibility = $_POST['visibility'] ?? 'public';

    if (!empty($title) && !empty($content)) {
        if (!in_array($visibility, ['public', 'friends'])) {
            $visibility = 'public';
        }

        $updateStmt = $pdo->prepare("UPDATE blogs SET title = ?, content = ?, visibility = ? WHERE id = ?");
        $updateStmt->execute([$title, $content, $visibility, $post_id]);

        header("Location: index.php");
        exit;
    } else {
        $message = "Bitte alle Pflichtfelder ausfüllen.";
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Beitrag bearbeiten - Webforum</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 40px auto; padding: 20px; background: #f4f4f9; }
        form { background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        input, textarea, select { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        textarea { height: 150px; }
        button { background: #ffc107; color: #212529; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        button:hover { background: #e0a800; }
        .msg { color: #d9534f; font-weight: bold; margin-bottom: 15px; }
    </style>
</head>
<body>

    <form action="edit_post.php?id=<?= $post_id ?>" method="POST">
        <h2>Beitrag bearbeiten</h2>

        <?php if (!empty($message)): ?>
            <div class="msg"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <label for="title">Titel:</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>

        <label for="content">Inhalt:</label>
        <textarea id="content" name="content" required><?= htmlspecialchars($post['content']) ?></textarea>

        <label for="visibility">Sichtbarkeit:</label>
        <select id="visibility" name="visibility">
            <option value="public" <?= $post['visibility'] === 'public' ? 'selected' : '' ?>>Öffentlich</option>
            <option value="friends" <?= $post['visibility'] === 'friends' ? 'selected' : '' ?>>Nur für Freunde</option>
        </select>

        <button type="submit">Änderungen speichern</button>
        <p style="margin-top: 15px;"><a href="index.php">← Abbrechen</a></p>
    </form>

</body>
</html>