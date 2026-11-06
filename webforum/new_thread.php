<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
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

        $stmt = $pdo->prepare("INSERT INTO blogs (user_id, title, content, visibility) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $title, $content, $visibility]);

        header("Location: index.php");
        exit;
    } else {
        $message = "Bitte Titel und Inhalt ausfüllen.";
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Neuen Beitrag erstellen - Webforum</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 40px auto; padding: 20px; background: #f4f4f9; }
        form { background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        input, textarea, select { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        textarea { height: 150px; }
        button { background: #007BFF; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .msg { color: #d9534f; font-weight: bold; margin-bottom: 15px; }
    </style>
</head>
<body>

    <form action="new_thread.php" method="POST">
        <h2>Neuen Beitrag verfassen</h2>

        <?php if (!empty($message)): ?>
            <div class="msg"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <label for="title">Titel:</label>
        <input type="text" id="title" name="title" required>

        <label for="content">Inhalt:</label>
        <textarea id="content" name="content" required></textarea>

        <label for="visibility">Sichtbarkeit:</label>
        <select id="visibility" name="visibility">
            <option value="public">Öffentlich (Für alle sichtbar)</option>
            <option value="friends">Nur für Freunde</option>
        </select>

        <button type="submit">Beitrag veröffentlichen</button>
        <p style="margin-top: 15px;"><a href="index.php">← Zurück zur Startseite</a></p>
    </form>

</body>
</html>