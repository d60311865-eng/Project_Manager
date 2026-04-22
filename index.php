<?php
require_once 'db.php';

$search = "";

if (isset($_GET['search'])) {
    $search = $_GET['search'];

    $sql = "SELECT pid, title, start_date, short_description 
            FROM projects 
            WHERE title LIKE ? OR start_date LIKE ?
            ORDER BY start_date DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $sql = "SELECT pid, title, start_date, short_description 
            FROM projects 
            ORDER BY start_date DESC";

    $stmt = $pdo->query($sql);
}

$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Software Projects</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="register.php">Register</a>
            <a href="login.php">Login</a>
        </nav>
    </header>

    <main>
        <h2>All Projects</h2>

        <!-- Search Form -->
        <form method="GET" action="index.php" style="margin-bottom: 20px;">
            <input type="text" name="search" placeholder="Search by title or start date" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>

        <?php if (count($projects) > 0): ?>
            <?php foreach ($projects as $project): ?>
                <div class="project-card">
                    <h3>
                        <a href="project.php?pid=<?php echo $project['pid']; ?>">
                            <?php echo htmlspecialchars($project['title']); ?>
                        </a>
                    </h3>
                    <p><strong>Start Date:</strong> <?php echo htmlspecialchars($project['start_date']); ?></p>
                    <p><?php echo htmlspecialchars($project['short_description']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No projects found.</p>
        <?php endif; ?>
    </main>
</body>
</html>