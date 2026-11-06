<?php
require_once 'db.php';
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: index.php");
            exit;
        } else {
            $message = "Ungültiger Benutzername oder falsches Passwort.";
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
    <title>Login - Webforum</title>
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

    <form action="login.php" method="POST">
        <h2>Login</h2>
        
        <?php if (!empty($message)): ?>
            <div class="msg"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <label for="username">Benutzername:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Passwort:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Einloggen</button>
        <p style="text-align: center; margin-top: 15px;"><a href="register.php">Noch keinen Account? Registrieren</a></p>
    </form>

</body>
</html>