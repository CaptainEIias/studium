<?php
session_start();
require_once 'db.php';

$current_user_id = $_SESSION['user_id'] ?? 0;
$current_role = $_SESSION['role'] ?? 'guest';
$search = trim($_GET['search'] ?? '');

$sql = "
    SELECT blogs.*, users.username 
    FROM blogs 
    JOIN users ON blogs.user_id = users.id 
    WHERE (blogs.visibility = 'public' 
       OR blogs.user_id = ? 
       OR (blogs.visibility = 'friends' AND blogs.user_id IN (
           SELECT friend_id FROM friends WHERE user_id = ?
       )))
";

$params = [$current_user_id, $current_user_id];

if (!empty($search)) {
    $sql .= " AND (blogs.title LIKE ? OR blogs.content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY blogs.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();

$comments = [];
if (!empty($posts)) {
    $post_ids = array_column($posts, 'id');
    $placeholders = implode(',', array_fill(0, count($post_ids), '?'));
    
    $commStmt = $pdo->prepare("
        SELECT comments.*, users.username 
        FROM comments 
        JOIN users ON comments.user_id = users.id 
        WHERE comments.blog_id IN ($placeholders) 
        ORDER BY comments.created_at ASC
    ");
    $commStmt->execute($post_ids);
    $raw_comments = $commStmt->fetchAll();

    foreach ($raw_comments as $c) {
        $comments[$c['blog_id']][] = $c;
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Webforum - Startseite</title>
    <style>
        body { font-family: sans-serif; max-width: 800px; margin: 40px auto; padding: 0 20px; background: #f4f4f9; color: #333; }
        header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #ccc; padding-bottom: 10px; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
        .search-bar { margin-bottom: 20px; display: flex; gap: 10px; }
        .search-bar input { flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .post { background: #fff; padding: 20px; margin-bottom: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .meta { font-size: 0.85em; color: #777; margin-top: 10px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        .btn { display: inline-block; background: #007BFF; color: white; padding: 6px 10px; text-decoration: none; border-radius: 4px; font-size: 0.85em; }
        .btn:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-warning:hover { background: #e0a800; }
        .btn-friends { background: #17a2b8; }
        .btn-friends:hover { background: #138496; }
        .btn-profile { background: #6c757d; }
        .btn-profile:hover { background: #5a6268; }
        .btn-admin { background: #6f42c1; }
        .btn-admin:hover { background: #5a32a3; }
        .actions { margin-top: 10px; }
        
        .comments-section { margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px; }
        .comment { background: #f8f9fa; padding: 10px 12px; border-radius: 4px; margin-bottom: 8px; font-size: 0.9em; }
        .comment-meta { font-size: 0.75em; color: #888; margin-top: 4px; }
        .comment-form textarea { width: 100%; height: 60px; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; margin-top: 5px; }
        .comment-form button { margin-top: 5px; background: #28a745; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.85em; }
        .comment-form button:hover { background: #218838; }
    </style>
</head>
<body>

    <header>
        <h1>Webforum</h1>
        <div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span>Eingeloggt als: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> (<?= htmlspecialchars($current_role) ?>)</span> 
                
                <?php if ($current_role === 'admin'): ?>
                    <a href="admin.php" class="btn btn-admin" style="margin-left: 10px;">Admin-Panel</a>
                <?php endif; ?>

                <a href="profile.php" class="btn btn-profile" style="margin-left: 5px;">Profil</a>
                <a href="friends.php" class="btn btn-friends" style="margin-left: 5px;">Freunde</a>
                <a href="new_thread.php" class="btn" style="margin-left: 5px;">+ Beitrag erstellen</a>
                <a href="logout.php" class="btn btn-danger" style="margin-left: 5px;">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn">Login</a>
                <a href="register.php" class="btn" style="background: #28a745;">Registrieren</a>
            <?php endif; ?>
        </div>
    </header>

    <form action="index.php" method="GET" class="search-bar">
        <input type="text" name="search" placeholder="Beiträge durchsuchen (Titel oder Inhalt)..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn">Suchen</button>
        <?php if (!empty($search)): ?>
            <a href="index.php" class="btn" style="background: #6c757d; text-decoration: none; display: flex; align-items: center;">Zurücksetzen</a>
        <?php endif; ?>
    </form>

    <h2><?= !empty($search) ? 'Suchergebnisse für: "' . htmlspecialchars($search) . '"' : 'Aktuelle Beiträge' ?></h2>

    <?php if (empty($posts)): ?>
        <p>Keine Beiträge gefunden.</p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <h3><?= htmlspecialchars($post['title']) ?></h3>
                <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                <div class="meta">
                    <div>
                        Erstellt von: <strong><?= htmlspecialchars($post['username']) ?></strong> am <?= $post['created_at'] ?> 
                        | Sichtbarkeit: <em><?= htmlspecialchars($post['visibility']) ?></em>
                    </div>
                    
                    <?php if (isset($_SESSION['user_id']) && ($post['user_id'] == $_SESSION['user_id'] || $current_role === 'admin')): ?>
                        <div class="actions">
                            <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-warning">Bearbeiten</a>
                            <a href="delete_post.php?id=<?= $post['id'] ?>" class="btn btn-danger" onclick="return confirm('Wirklich löschen?');">Löschen</a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="comments-section">
                    <h4>Kommentare</h4>
                    
                    <?php if (!empty($comments[$post['id']])): ?>
                        <?php foreach ($comments[$post['id']] as $comm): ?>
                            <div class="comment">
                                <div><?= nl2br(htmlspecialchars($comm['content'])) ?></div>
                                <div class="comment-meta">
                                    Von <strong><?= htmlspecialchars($comm['username']) ?></strong> am <?= $comm['created_at'] ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="font-size: 0.85em; color: #777;">Noch keine Kommentare vorhanden.</p>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="add_comment.php" method="POST" class="comment-form">
                            <input type="hidden" name="blog_id" value="<?= $post['id'] ?>">
                            <textarea name="content" placeholder="Schreibe einen Kommentar..." required></textarea>
                            <button type="submit">Kommentar abschicken</button>
                        </form>
                    <?php else: ?>
                        <p style="font-size: 0.8em; color: #555; margin-top: 8px;"><a href="login.php">Einloggen</a>, um einen Kommentar zu schreiben.</p>
                    <?php endif; ?>
                </div>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>