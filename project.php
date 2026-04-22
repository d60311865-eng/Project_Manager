<?php
require_once 'db.php';

// Check if project ID exists
if (!isset($_GET['pid'])) {
    die("Project ID not provided.");
}

$pid = $_GET['pid'];

// Get project + user email using JOIN
$sql = "SELECT projects.*, users.email 
        FROM projects 
        JOIN users ON projects.uid = users.uid 
        WHERE pid = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$pid]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

// If project not found
if (!$project) {
    die("Project not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($project['title']); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($project['title']); ?></h1>
        <nav>
            <a href="index.php">Home</a>
        </nav>
    </header>

    <main>
        <p><strong>Start Date:</strong> <?php echo htmlspecialchars($project['start_date']); ?></p>
        <p><strong>End Date:</strong> <?php echo htmlspecialchars($project['end_date']); ?></p>
        <p><strong>Phase:</strong> <?php echo htmlspecialchars($project['phase']); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($project['short_description']); ?></p>
        <p><strong>Owner Email:</strong> <?php echo htmlspecialchars($project['email']); ?></p>
    </main>
</body>
</html>