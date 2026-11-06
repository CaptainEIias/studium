<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$current_user_id = $_SESSION['user_id'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    if (!empty($old_password) && !empty($new_password)) {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$current_user_id]);
        $user_data = $stmt->fetch();

        if ($user_data && password_verify($old_password, $user_data['password'])) {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_stmt->execute([$new_hash, $current_user_id]);
            $message = "Passwort erfolgreich aktualisiert!";
        } else {
            $error = "Das eingegebene alte Passwort ist falsch.";
        }
    } else {
        $error = "Bitte alle Passwortfelder ausfüllen.";
    }
}

$userStmt = $pdo->prepare("SELECT username, role, created_at FROM users WHERE id = ?");
$userStmt->execute([$current_user_id]);
$user = $userStmt->fetch();

$postStmt = $pdo->prepare("SELECT * FROM blogs WHERE user_id = ? ORDER BY created_at DESC");
$postStmt->execute([$current_user_id]);
$my_posts = $postStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Mein Profil - Webforum</title>
    <style>
        body { font-family: sans-serif; max-width: 800px; margin: 40px auto; padding: 0 20px; background: #f4f4f9; color: #333; }
        header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #ccc; padding-bottom: 10px; margin-bottom: 20px; }
        .card { background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .post { background: #fff; padding: 15px; margin-bottom: 15px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .meta { font-size: 0.85em; color: #777; margin-top: 10px; display: flex; justify-content: space-between; align-items: center; }
        .btn { display: inline-block; background: #007BFF; color: white; padding: 6px 10px; text-decoration: none; border-radius: 4px; font-size: 0.85em; }
        .btn:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-warning:hover { background: #e0a800; }
        input[type="password"] { width: 100%; padding: 8px; margin: 8px 0 12px 0; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .msg { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    </style>
</head>
<body>

    <header>
        <h1>Mein Profil</h1>
        <div>
            <a href="index.php" class="btn">← Zurück zur Startseite</a>
        </div>
    </header>

    <?php if (!empty($message)): ?>
        <div class="msg"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <h3>Account-Informationen</h3>
        <p><strong>Benutzername:</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p><strong>Rolle:</strong> <span style="text-transform: uppercase; font-weight: bold; color: #007BFF;"><?= htmlspecialchars($user['role']) ?></span></p>
        <p><strong>Mitglied seit:</strong> <?= $user['created_at'] ?></p>
    </div>

    <div class="card">
        <h3>Passwort ändern</h3>
        <form action="profile.php" method="POST">
            <input type="hidden" name="change_password" value="1">
            
            <label for="old_password">Altes Passwort:</label>
            <input type="password" id="old_password" name="old_password" required>

            <label for="new_password">Neues Passwort:</label>
            <input type="password" id="new_password" name="new_password" required>

            <button type="submit" class="btn" style="background: #28a745;">Passwort aktualisieren</button>
        </form>
    </div>

    <h3>Meine Beiträge (<?= count($my_posts) ?>)</h3>
    <?php if (empty($my_posts)): ?>
        <p>Du hast noch keine Beiträge verfasst.</p>
    <?php else: ?>
        <?php foreach ($my_posts as $post): ?>
            <div class="post">
                <h4><?= htmlspecialchars($post['title']) ?></h4>
                <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                <div class="meta">
                    <div>
                        Erstellt am: <?= $post['created_at'] ?> | Sichtbarkeit: <em><?= htmlspecialchars($post['visibility']) ?></em>
                    </div>
                    <div>
                        <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-warning">Bearbeiten</a>
                        <a href="delete_post.php?id=<?= $post['id'] ?>" class="btn btn-danger" onclick="return confirm('Wirklich löschen?');">Löschen</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>