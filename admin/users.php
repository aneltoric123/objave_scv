<?php
session_start();
require_once '../connection.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 0) {
  header("Location: ../login/login.php");
  exit();
}

// Retrieve the list of users from the database
$users = array();
$result = mysqli_query($link, "SELECT * FROM users");
while ($row = mysqli_fetch_assoc($result)) {
  $users[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Uporabniki</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="stylesheet" type="text/css" href="users.css">
</head>
<body>
  <header>
    <nav>
      <ul>
        <li><div id="home"><a href="home_admin.php">Domov</a></div></li>
        <li><div id="home"><a href="../register/register.php">Dodaj uporabnika</a></div></li>
        <li id="logout"><a href="../login/logout.php">Odjava</a></li>
      </ul>
    </nav>
  </header>
  <main>
    <h1>Uporabniki</h1>
    <div class="users-container">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Ime</th>
            <th>Email</th>
            <th>Šola uporabnika</th>
            <th>Tip uporabnika</th>
            <th>Akcije</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user) { ?>
            <tr>
              <td><?php echo $user['id']; ?></td>
              <td><?php echo $user['name']; ?></td>
              <td><?php echo $user['email']; ?></td>
              <td>
                <?php if ($user['sola_id'] == 1) { ?>
                  ŠCV
                <?php } elseif ($user['sola_id'] == 2) { ?>
                  ŠSGO
                <?php } elseif ($user['sola_id'] == 3) { ?>
                  ERŠ
                <?php } elseif ($user['sola_id'] == 4) { ?>
                  ŠSD
                <?php } elseif ($user['sola_id'] == 5) { ?>
                  GIM
                <?php } elseif ($user['sola_id'] == 6) { ?>
                  MIC
                <?php } elseif ($user['sola_id'] == 7) { ?>
                  VSŠ
                <?php } else { ?>
                  ---
                <?php } ?>
              </td>
              <td>
                <?php if ($user['user_type'] == 0) { ?>
                  Admin
                <?php } elseif ($user['user_type'] == 1) { ?>
                  Lektor
                <?php } else { ?>
                  Pisatelj
                <?php } ?>
              </td>
              <td>
                <?php if ($user['user_type'] == 1 || $user['user_type'] == 2 ) { ?>
                  <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="edit-btn">Uredi</a>
                  <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="delete-btn">Izbriši</a>
                  <a href="reset_pass.php?id=<?php echo $user['id']; ?>" class="reset-btn">Reset password</a>
                <?php } ?>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </main>
</body>
</html>
