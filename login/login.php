<!DOCTYPE html>
<html>
  <head>
    <title>Prijava</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" type="text/css" href="login.css">
  </head>
  <body><?php 
    session_start(); 
    require_once '../connection.php';
      if(isset($_POST['submit'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Preveri vnos podatkov
        if(empty($email) || empty($password)) {
            // Prikaži sporočilo o napaki
            echo "<p class='error'>Prosim, izpolnite vsa polja.</p>";
        } else {
            // Preveri, če uporabnik obstaja v bazi
            $sql = "SELECT * FROM users WHERE email='$email'";
            $result = mysqli_query($link, $sql);
            $user = mysqli_fetch_assoc($result);

            if($user) {
                // Preveri geslo
                if(password_verify($password, $user['password'])) {
                    // Geslo je pravilno, prijavi uporabnika
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_type'] = $user['user_type'];
                    if($user['user_type'] == 0) {
                        // admin
                        header("Location: ../admin/home_admin.php");
                        exit();
                    } else if ($user['user_type'] == 1) {
                        // lektor
                        header("Location: ../lektor/home_lektor.php");
                        exit();
                    } else {
                        // pisatelj
                        header("Location: ../pisatelj/home_pisatelj.php");
                        exit();
                    }
                } else {
                    // Prikaži sporočilo o napaki
                    echo "<p class='error'>Napačen email ali geslo!</p>";
                }
            } else {
                // Prikaži sporočilo o napaki
                echo "<p class='error'>Napačen email ali geslo!</p>";
            }
        }
    }
  ?>
    <div class="container">
      <div class="login-box">
        <h2>Prijava</h2>
        <form method="post">
          <input type="text" name="email" placeholder="Email">
          <input type="password" name="password" placeholder="Geslo">
          <input type="submit" name="submit" value="Prijavi se">
        </form>
      </div>
    </div>
  </body>
</html>


<?php 
if(isset($_SESSION['error'])) {
    echo "<script>alert('" . $_SESSION['error'] . "')</script>";
    unset($_SESSION['error']);
}
if(isset($_SESSION['success'])) {
    echo "<script>alert('" . $_SESSION['success'] . "')</script>";
    unset($_SESSION['success']);
}
?>