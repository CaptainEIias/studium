<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$current_user_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target_id = intval($_POST['target_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($target_id && $target_id !== $current_user_id) {
        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT IGNORE INTO friends (user_id, friend_id) VALUES (?, ?), (?, ?)");
            $stmt->execute([$current_user_id, $target_id, $target_id, $current_user_id]);
            $message = "Freund erfolgreich hinzugefügt!";
        } elseif ($action === 'remove') {
            $stmt = $pdo->prepare("DELETE FROM friends WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)");
            $stmt->execute([$current_user_id, $target_id, $target_id, $current_user_id]);
            $message = "Freund entfernt.";
        }
    }
}

$stmt = $pdo->prepare("
    SELECT u.id, u.username, 
           EXISTS (
               SELECT 1 FROM friends f 
               WHERE f.user_id = ? AND f.friend_id = u.id
           ) AS is_friend 
    FROM users u 
    WHERE u.id != ?
");
$stmt->execute([$current_user_id, $current_user_id]);
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Freunde verwalten - Webforum</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 40px auto; padding: 20px; background: #f4f4f9; color: #333; }
        .user-card { background: #fff; padding: 12px 15px; margin-bottom: 10px; border-radius: 5px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .btn { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; color: white; text-decoration: none; font-size: 0.9em; }
        .btn-add { background: #28a745; }
        .btn-add:hover { background: #218838; }
        .btn-remove { background: #dc3545; }
        .btn-remove:hover { background: #c82333; }
        .msg { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    </style>
</head>
<body>

    <h2>Freunde verwalten</h2>
    <p><a href="index.php">← Zurück zur Startseite</a></p>

    <?php if (!empty($message)): ?>
        <div class="msg"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <h3>Alle registrierten Benutzer:</h3>

    <?php if (empty($users)): ?>
        <p>Keine anderen Benutzer registriert.</p>
    <?php else: ?>
        <?php foreach ($users as $user): ?>
            <div class="user-card">
                <span><strong><?= htmlspecialchars($user['username']) ?></strong></span>
                <form action="friends.php" method="POST" style="margin: 0;">
                    <input type="hidden" name="target_id" value="<?= $user['id'] ?>">
                    <?php if ($user['is_friend']): ?>
                        <input type="hidden" name="action" value="remove">
                        <button type="submit" class="btn btn-remove">Freund entfernen</button>
                    <?php else: ?>
                        <input type="hidden" name="action" value="add">
                        <button type="submit" class="btn btn-add">Als Freund hinzufügen</button>
                    <?php endif; ?>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>