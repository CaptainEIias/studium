<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            
            if ($stmt->fetch()) {
                $message = "Dieser Benutzername ist bereits vergeben.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $insertStmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                $insertStmt->execute([$username, $hashed_password]);

                $message = "Registrierung erfolgreich! Du kannst dich jetzt einloggen.";
            }
        } catch (PDOException $e) {
            $message = "Fehler bei der Registrierung: " . $e->getMessage();
        }
    } else {
        $message = "Bitte alle Felder ausfüllen.";
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Registrierung - Webforum</title>
    <style>
        body { font-family: sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; background: #f4f4f9; }
        form { background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #007BFF; color: white; padding: 10px; border: none; width: 100%; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .msg { margin-bottom: 15px; color: #d9534f; font-weight: bold; }
    </style>
</head>
<body>

    <form action="register.php" method="POST">
        <h2>Registrierung</h2>
        
        <?php if (!empty($message)): ?>
            <div class="msg"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <label for="username">Benutzername:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Passwort:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Registrieren</button>
        <p style="text-align: center; margin-top: 15px;"><a href="login.php">Bereits einen Account? Zum Login</a></p>
    </form>

</body>
</html>