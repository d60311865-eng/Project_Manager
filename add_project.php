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
// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid CSRF token.");
    }
    $title = trim($_POST["title"]);
    $start_date = trim($_POST["start_date"]);
    $end_date = trim($_POST["end_date"]);
    $short_description = trim($_POST["short_description"]);
    $phase = trim($_POST["phase"]);
    $uid = $_SESSION["user_id"];

    $allowed_phases = ["design", "development", "testing", "deployment", "complete"];

    // Server-side validation
    if (empty($title) || empty($start_date) || empty($end_date) || empty($short_description) || empty($phase)) {
        $message = "All fields are required.";
    } elseif (!in_array($phase, $allowed_phases)) {
        $message = "Invalid project phase selected.";
    } else {
        $sql = "INSERT INTO projects (title, start_date, end_date, short_description, phase, uid)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([$title, $start_date, $end_date, $short_description, $phase, $uid])) {
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Failed to add project.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Project</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Add Project</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <h2>Create a New Project</h2>

        <?php if (!empty($message)): ?>
            <p><strong><?php echo htmlspecialchars($message); ?></strong></p>
        <?php endif; ?>

        <form method="POST" action="add_project.php">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <label for="title">Project Title:</label><br>
            <input type="text" name="title" id="title" required><br><br>

            <label for="start_date">Start Date:</label><br>
            <input type="date" name="start_date" id="start_date" required><br><br>

            <label for="end_date">End Date:</label><br>
            <input type="date" name="end_date" id="end_date" required><br><br>

            <label for="short_description">Short Description:</label><br>
            <textarea name="short_description" id="short_description" rows="4" cols="40" required></textarea><br><br>

            <label for="phase">Phase:</label><br>
            <select name="phase" id="phase" required>
                <option value="">-- Select Phase --</option>
                <option value="design">Design</option>
                <option value="development">Development</option>
                <option value="testing">Testing</option>
                <option value="deployment">Deployment</option>
                <option value="complete">Complete</option>
            </select><br><br>

            <button type="submit">Add Project</button>
        </form>
    </main>
</body>
</html>