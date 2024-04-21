<?php
session_start();
require_once '../connection.php';

// Check if user is logged in and has the role of an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 0) {
    header("Location: ../login/login.php");
    exit();
}

// Check if user ID is specified in the query string
if (!isset($_GET['id'])) {
    header("Location: home_admin.php");
    exit();
}

$user_id = $_GET['id'];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    // TODO: You should add proper hashing function here
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update the password in the database
    $stmt = mysqli_prepare($link, "UPDATE users SET password = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);
    mysqli_stmt_execute($stmt);
    
    header("Location: home_admin.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }

        #reset-form {
            width: 300px;
            margin: 0 auto;
            margin-top: 50px;
        }

        #reset-form label {
            display: block;
            margin-top: 20px;
        }

        #reset-form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
        }

        #reset-form .btn-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        #reset-form input[type="submit"], #reset-form a {
            flex-basis: 48%;
            padding: 10px;
            text-align: center;
            background-color: #333;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
            border: none;
            margin-left: 10%;
        }

        #reset-form input[type="submit"]:hover, #reset-form a:hover {
            background-color: #444;
        }
    </style>
</head>
<body>
    <header>
        <h1>Reset Password</h1>
    </header>
    <div id="reset-form">
        <form method="post" action="">
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>
            <div class="btn-group">
                <input type="submit" value="Reset Password">
                <a href="home_admin.php" style="font-size: 13px;">Go Back</a>
            </div>
        </form>
    </div>
</body>
</html>
