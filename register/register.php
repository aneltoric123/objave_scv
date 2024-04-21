<?php 
session_start(); 
require_once '../connection.php'; 

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 0) {
  header("Location: ../login/login.php");
  exit();
}

if(isset($_POST['submit'])) {
    $name = $_POST['username'];
    $email = $_POST['email'];
    $pass1 = $_POST['password'];
    $pass2 = $_POST['confirm-password'];
    $role = $_POST['role'];
    $sola = $_POST['sola'];

    // Preveri vnos podatkov
    if(empty($name) || empty($email) || empty($pass1) || empty($pass2) || ($pass1 != $pass2)) {
        // Preusmeri nazaj na stran za registracijo z napako
        $_SESSION['error'] = "Prosim, izpolnite vsa polja in preverite, če se gesli ujemata.";
        header("Location: ../register/register.php");
        exit();
    }
    
    if ($pass1 == $pass2) {
        // Zakodiraj geslo
        $hashed_password = password_hash($pass1, PASSWORD_DEFAULT);

        // Vstavi uporabnika v bazo
        $sql = "INSERT INTO users (name, email, password, user_type, sola_id) VALUES ('$name', '$email', '$hashed_password', '$role', '$sola')"; 
        mysqli_query($link, $sql);

        // Preusmeri na stran za prijavo z uspešnim sporočilom
        $_SESSION['success'] = "Uporabnik uspešno registriran. Sedaj se lahko prijavite.";
        header("Location: ../admin/home_admin.php");
        exit();
    }
    else {
        // Preusmeri nazaj na stran za registracijo z napako
        $_SESSION['error'] = "Prosim, izpolnite vsa polja in preverite, če se gesli ujemata.";
        header("Location: ../register/register.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="sl">
<head>
  <meta charset="UTF-8">
  <title>Registracija</title>
  <link rel="stylesheet" href="../style.css">
  <link rel="stylesheet" href="register.css">
</head>
<body>
<header>
  <nav>
    <ul>
      <li id="home"><a href="../admin/home_admin.php">Domača stran</a></li>
      <li id="logout"><a href="../login/logout.php">Odjava</a></li>
    </ul>
  </nav>
</header>
<main>
  <div class="container">
    <form method="post">
      <h2>Ustvarite novega uporabnika</h2>
      <?php 
        if(isset($_SESSION['error'])) {
            echo "<p class='error'>" . $_SESSION['error'] . "</p>";
            unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])) {
            echo "<p class='success'>" . $_SESSION['success'] . "</p>";
            unset($_SESSION['success']);
        }
      ?>
      <div class="form-group">
        <label for="username">Ime:</label>
        <input type="text" id="username" name="username" value="<?php if(isset($_POST['username'])) echo $_POST['username']; ?>" required> 
      </div>
      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>" required>
      </div>
      <div class="form-group">
        <label for="password">Geslo:</label>
        <input type="password" id="password" name="password" required>
      </div>
      <div class="form-group">
        <label for="confirm-password">Potrdite geslo:</label>
        <input type="password" id="confirm-password" name="confirm-password" required>
      </div>
      <div class="form-group">
        <label for="role">Izbira vloge za uporabnika:</label>
        <select id="role" name="role" required>
          <option value="1">Lektor</option>
          <option value="2" selected>Pisatelj</option>
        </select>
      </div>
      <div class="form-group">
        <label for="sola">Izbira šole za uporabnika:</label>
        <select id="sola" name="sola" required>
          <option value="1" selected>ŠCV</option>
          <option value="2">ŠSGO</option>
          <option value="3">ERŠ</option>
          <option value="4">ŠSD</option>
          <option value="5">GIM</option>
          <option value="6">MIC</option>
          <option value="7">VSŠ</option>
        </select>
      </div>
      <input id="registerbtn" type="submit" name="submit" value="Registriraj">
    </form>
  </div>
      </main>
</body>
</html>
