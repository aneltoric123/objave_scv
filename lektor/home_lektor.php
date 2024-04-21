<?php 
session_start(); 
require_once '../connection.php'; 

$name=$_SESSION['user_name'];
$id=$_SESSION['user_id'];

// Preveri, ali je uporabnik prijavljen in ima vlogo lektorja
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 1) {
    header("Location: ../login/login.php");
    exit();
}

// Poišče vse objave
$result = mysqli_query($link, "SELECT id, naslov, datum FROM posts WHERE user_id = $id");

?>

<!DOCTYPE html>
<html>
  <head>
    <title>Lektor Domača stran</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" type="text/css" href="home.css">
  </head>
  <body>
    <header>
      <nav>
        <ul>
          <li><div id="home"><a href="home_lektor.php">Domača stran</a></div></li>
          <li id="logout"><a href="../login/logout.php">Odjava</a></li>
        </ul>
      </nav>
    </header>
    <main>
      <h1>Dobrodošli, Lektor!</h1>
      <p>To je domača stran lektorja.</p>
      <table>
        <tr><th>Naslov</th><th>Datum</th><th>Uredi</th><th>Izbriši</th></tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
          <tr>
            <td><?php echo $row['naslov']; ?></td>
            <td><?php echo $row['datum']; ?></td>
            <td><a href="edit_post.php?id=<?php echo $row['id']; ?>">UREDI</a></td>
            <td><a href="delete_post.php?id=<?php echo $row['id']; ?>">IZBRIŠI</a></td>
          </tr>
        <?php } ?>
      </table>
    </main>
  </body>
</html>
