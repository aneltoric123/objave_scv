<?php
session_start();
require_once '../connection.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 0) {
    header("Location: ../login/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$user_id = $_GET['id'];

// Delete user's posts, platform checkboxes, and organization checkboxes from the database
$stmt = mysqli_prepare($link, "SELECT id FROM posts WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
    
while ($row = mysqli_fetch_assoc($result)) {
    $post_id = $row['id'];
    mysqli_query($link, "DELETE FROM mesto_posts WHERE post_id = $post_id");
    mysqli_query($link, "DELETE FROM sola_posts WHERE post_id = $post_id");
    mysqli_query($link, "DELETE FROM photos WHERE post_id = $post_id");
}

mysqli_query($link, "DELETE FROM posts WHERE user_id = $user_id");

// Delete user from the database
mysqli_query($link, "DELETE FROM users WHERE id = $user_id");

// Redirect to users page
header("Location: users.php");
exit();
?>
