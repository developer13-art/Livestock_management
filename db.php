<?php
// db.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "livestock_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function sanitizeInput($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserRole() {
    return $_SESSION['role'] ?? null;
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function redirectIfNotAuthorized($allowedRoles) {
    redirectIfNotLoggedIn();
    if (!in_array(getUserRole(), $allowedRoles)) {
        header("Location: index.php");
        exit();
    }
}
?>
