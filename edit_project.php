<?php
require_once 'db.php';
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// Must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$message = "";

// Check project ID
if (!isset($_GET["pid"])) {
    die("Project ID not provided.");
}

$pid = $_GET["pid"];

// Fetch project AND ensure it belongs to this user
$sql = "SELECT * FROM projects WHERE pid = ? AND uid = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$pid, $user_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

// If not found or not owned by user
if (!$project) {
    die("You are not authorised to edit this project.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid CSRF token.");
    }
    $title = trim($_POST["title"]);
    $start_date = trim($_POST["start_date"]);
    $end_date = trim($_POST["end_date"]);
    $short_description = trim($_POST["short_description"]);
    $phase = trim($_POST["phase"]);

    $allowed_phases = ["design", "development", "testing", "deployment", "complete"];

    if (empty($title) || empty($start_date) || empty($end_date) || empty($short_description) || empty($phase)) {
        $message = "All fields are required.";
    } elseif (!in_array($phase, $allowed_phases)) {
        $message = "Invalid phase.";
    } else {
        $updateSql = "UPDATE projects 
                      SET title = ?, start_date = ?, end_date = ?, short_description = ?, phase = ?
                      WHERE pid = ? AND uid = ?";

        $updateStmt = $pdo->prepare($updateSql);

        if ($updateStmt->execute([$title, $start_date, $end_date, $short_description, $phase, $pid, $user_id])) {
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Update failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Project</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Edit Project</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <h2>Edit Project</h2>

        <?php if (!empty($message)): ?>
            <p><strong><?php echo htmlspecialchars($message); ?></strong></p>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <label>Title:</label><br>
            <input type="text" name="title" value="<?php echo htmlspecialchars($project['title']); ?>" required><br><br>

            <label>Start Date:</label><br>
            <input type="date" name="start_date" value="<?php echo htmlspecialchars($project['start_date']); ?>" required><br><br>

            <label>End Date:</label><br>
            <input type="date" name="end_date" value="<?php echo htmlspecialchars($project['end_date']); ?>" required><br><br>

            <label>Description:</label><br>
            <textarea name="short_description" rows="4" required><?php echo htmlspecialchars($project['short_description']); ?></textarea><br><br>

            <label>Phase:</label><br>
            <select name="phase" required>
                <option value="design" <?php if ($project['phase']=="design") echo "selected"; ?>>Design</option>
                <option value="development" <?php if ($project['phase']=="development") echo "selected"; ?>>Development</option>
                <option value="testing" <?php if ($project['phase']=="testing") echo "selected"; ?>>Testing</option>
                <option value="deployment" <?php if ($project['phase']=="deployment") echo "selected"; ?>>Deployment</option>
                <option value="complete" <?php if ($project['phase']=="complete") echo "selected"; ?>>Complete</option>
            </select><br><br>

            <button type="submit">Update Project</button>
        </form>
    </main>
</body>
</html>