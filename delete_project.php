<<<<<<< HEAD
<?php
require_once 'db.php';
session_start();

// Must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Check CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid CSRF token.");
}

// Check project ID
if (!isset($_POST["pid"])) {
    die("Project ID not provided.");
}

$pid = $_POST["pid"];
$user_id = $_SESSION["user_id"];

// Delete only if project belongs to logged-in user
$sql = "DELETE FROM projects WHERE pid = ? AND uid = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$pid, $user_id]);

header("Location: dashboard.php");
exit();
=======
<?php
require_once 'db.php';
session_start();

// Must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Check CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid CSRF token.");
}

// Check project ID
if (!isset($_POST["pid"])) {
    die("Project ID not provided.");
}

$pid = $_POST["pid"];
$user_id = $_SESSION["user_id"];

// Delete only if project belongs to logged-in user
$sql = "DELETE FROM projects WHERE pid = ? AND uid = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$pid, $user_id]);

header("Location: dashboard.php");
exit();
>>>>>>> 02983e44d05fd4e829d6a06f795e249585bced94
?>