<?php
require_once 'db.php';
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
//CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Get only this user's projects
$sql = "SELECT * FROM projects WHERE uid = ? ORDER BY start_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Dashboard</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="add_project.php">Add Project</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?></h2>

        <h3>Your Projects</h3>

        <?php if (count($projects) > 0): ?>
            <?php foreach ($projects as $project): ?>
                <div class="project-card">
                    <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                    <p><strong>Start:</strong> <?php echo htmlspecialchars($project['start_date']); ?></p>
                    <p><strong>Phase:</strong> <?php echo htmlspecialchars($project['phase']); ?></p>

                    <a href="edit_project.php?pid=<?php echo $project['pid']; ?>">Edit</a> |
                    <form method="POST" action="delete_project.php" style="display:inline;">
                     <input type="hidden" name="pid" value="<?php echo $project['pid']; ?>">
                     <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                     <button type="submit" onclick="return confirm('Are you sure?');">Delete</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>You have no projects yet.</p>
        <?php endif; ?>
    </main>
</body>
</html>