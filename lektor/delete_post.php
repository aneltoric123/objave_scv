<?php
session_start();
require_once '../connection.php';

// Check if user is logged in and has the role of a writer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    header("Location: ../login/login.php");
    exit();
}

// Check if post ID is set
if (!isset($_GET['id'])) {
    header("Location: ../lektor/home_lektor.php");
    exit();
}

$post_id = $_GET['id'];

// Check if belongs to the current user
$stmt = mysqli_prepare($link, "SELECT user_id FROM posts WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $post_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: ../lektor/home_lektor.php");
    exit();
}

$row = mysqli_fetch_assoc($result);

if ($row['user_id'] != $_SESSION['user_id']) {
    header("Location: ../lektor/home_lektor.php");
    exit();
}

// Delete post's platform checkboxes from the database
$stmt = mysqli_prepare($link, "DELETE FROM mesto_posts WHERE post_id = ?");
mysqli_stmt_bind_param($stmt, "i", $post_id);
mysqli_stmt_execute($stmt);

// Delete post's organization checkboxes from the database
$stmt = mysqli_prepare($link, "DELETE FROM sola_posts WHERE post_id = ?");
mysqli_stmt_bind_param($stmt, "i", $post_id);
mysqli_stmt_execute($stmt);

// Delete post's photos from the database
$stmt = mysqli_prepare($link, "DELETE FROM photos WHERE post_id = ?");
mysqli_stmt_bind_param($stmt, "i", $post_id);
mysqli_stmt_execute($stmt);

// Delete post from the database
$stmt = mysqli_prepare($link, "DELETE FROM posts WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $post_id);
mysqli_stmt_execute($stmt);

// Redirect to home page
header("Location: ../lektor/home_lektor.php");
exit();
?>
