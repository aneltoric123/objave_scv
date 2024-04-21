<?php
session_start();
require_once '../connection.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 0) {
  header("Location: ../login/login.php");
  exit();
}

// Check if the user ID is sent
if (isset($_GET['id'])) {
  $id = $_GET['id'];

  // Retrieve the user details from the database
  $result = mysqli_query($link, "SELECT * FROM users WHERE id='$id'");
  $user = mysqli_fetch_assoc($result);

  // Check if the form is submitted
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $user_type = $_POST['user_type'];
    $sola = $_POST['sola'];

    // Update the user details in the database
    mysqli_query($link, "UPDATE users SET name='$name', email='$email', user_type='$user_type', sola_id='$sola' WHERE id='$id'");

    // Redirect the user to the users page
    header("Location: users.php");
    exit();
  }
} else {
  // If the user ID is not sent, redirect the user to the users page
  header("Location: users.php");
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Uredi uporabnika</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="stylesheet" type="text/css" href="edit_user.css">
</head>
<body>
  <header>
    <nav>
      <ul>
        <li><div id="home"><a href="home_admin.php">Domov</a></li></div>
        <li><div id="home"><a href="users.php">Uporabniki</a></li></div>
        <li id="logout"><a href="../login/logout.php">Odjava</a></li>
      </ul>
    </nav>
  </header>
  <main>
    <h1>Uredi uporabnika</h1>
    <div class="form-container">
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $id; ?>">
      <div class="form-group">
        <label for="name">Ime:</label>
        <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required>
      </div>
      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
      </div>
      <div class="form-group">
        <label for="user_type">Tip uporabnika:</label>
        <select id="user_type" name="user_type" required>
          <option value="1" <?php echo $user['user_type'] == 1 ? 'selected' : ''; ?>>Lektor</option>
          <option value="2" <?php echo $user['user_type'] == 2 ? 'selected' : ''; ?>>Pisatelj</option>
        </select>
      </div>
      <div class="form-group">
        <label for="sola">Izbira šole za uporabnika:</label>
        <select id="sola" name="sola" required>
          <option value="1" <?php echo $user['sola_id'] == 1 ? 'selected' : ''; ?>>ŠCV</option>
          <option value="2" <?php echo $user['sola_id'] == 2 ? 'selected' : ''; ?>>ŠSGO</option>
          <option value="3" <?php echo $user['sola_id'] == 3 ? 'selected' : ''; ?>>ERŠ</option>
          <option value="4" <?php echo $user['sola_id'] == 4 ? 'selected' : ''; ?>>ŠSD</option>
          <option value="5" <?php echo $user['sola_id'] == 5 ? 'selected' : ''; ?>>GIM</option>
          <option value="6" <?php echo $user['sola_id'] == 6 ? 'selected' : ''; ?>>MIC</option>
          <option value="7" <?php echo $user['sola_id'] == 7 ? 'selected' : ''; ?>>VSŠ</option>
        </select>
      </div>
      <button type="submit" class="save-btn">Shrani</button>
      <a href="users.php" class="back-btn">Nazaj</a>
    </form>
  </div>
  </main>
</body>
</html>
