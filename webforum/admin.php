<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    die("Zugriff verweigert! Diese Seite ist nur für Administratoren zugänglich.");
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target_user_id = intval($_POST['user_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($target_user_id > 0 && $target_user_id !== $_SESSION['user_id']) { // Verhindert Selbst-Löschung
        if ($action === 'make_admin') {
            $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
            $stmt->execute([$target_user_id]);
            $message = "Benutzerrolle erfolgreich auf 'admin' geändert.";
        } elseif ($action === 'make_user') {
            $stmt = $pdo->prepare("UPDATE users SET role = 'user' WHERE id = ?");
            $stmt->execute([$target_user_id]);
            $message = "Benutzerrolle erfolgreich auf 'user' geändert.";
        } elseif ($action === 'delete_user') {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$target_user_id]);
            $message = "Benutzer wurde gelöscht.";
        }
    }
}

$stmt = $pdo->query("SELECT id, username, role, created_at FROM users ORDER BY id ASC");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Webforum</title>
    <style>
        body { font-family: sans-serif; max-width: 800px; margin: 40px auto; padding: 20px; background: #f4f4f9; color: #333; }
        table { width: 100%; border-collapse: collapse; background: #fff; margin-top: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #343a40; color: white; }
        .btn { padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; color: white; text-decoration: none; font-size: 0.85em; }
        .btn-admin { background: #17a2b8; }
        .btn-user { background: #6c757d; }
        .btn-danger { background: #dc3545; }
        .msg { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    </style>
</head>
<body>

    <h2>Admin Dashboard - Benutzerverwaltung</h2>
    <p><a href="index.php">← Zurück zur Startseite</a></p>

    <?php if (!empty($message)): ?>
        <div class="msg"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Benutzername</th>
                <th>Rolle</th>
                <th>Registriert am</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
                    <td><?= htmlspecialchars($u['role']) ?></td>
                    <td><?= $u['created_at'] ?></td>
                    <td>
                        <?php if ($u['id'] !== $_SESSION['user_id']): ?>
                            <form action="admin.php" method="POST" style="display:inline-block;">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <?php if ($u['role'] === 'user'): ?>
                                    <input type="hidden" name="action" value="make_admin">
                                    <button type="submit" class="btn btn-admin">Zum Admin machen</button>
                                <?php else: ?>
                                    <input type="hidden" name="action" value="make_user">
                                    <button type="submit" class="btn btn-user">Admin entziehen</button>
                                <?php endif; ?>
                            </form>

                            <form action="admin.php" method="POST" style="display:inline-block;" onsubmit="return confirm('Benutzer wirklich löschen?');">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <input type="hidden" name="action" value="delete_user">
                                <button type="submit" class="btn btn-danger">Löschen</button>
                            </form>
                        <?php else: ?>
                            <em>(Eigener Account)</em>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>