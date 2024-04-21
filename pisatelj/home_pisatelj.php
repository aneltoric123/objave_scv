<?php 
session_start(); 
require_once '../connection.php'; 

$name=$_SESSION['user_name'];
$id=$_SESSION['user_id'];

// Preveri, ali je uporabnik prijavljen in ima vlogo pisatelja
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    header("Location: ../login/login.php");
    exit();
}

// Query the database for all posts
$result = mysqli_query($link, "SELECT id, naslov, datum FROM posts WHERE user_id = $id");

?>

<!DOCTYPE html>
<html>
  <head>
    <title>Pisateljeva Domača stran</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" type="text/css" href="home.css">
  </head>
  <body>
    <header>
      <nav>
        <ul>
          <li><div id="home"><a href="home_pisatelj.php">Domača stran</a></li></div>
          <li id="logout"><a href="../login/logout.php">Odjava</a></li>
        </ul>
      </nav>
    </header>
    <main>
      <h1>Dobrodošli, <?php echo($name) ?>!</h1>
      <p>Tukaj si lahko ogledate vsa besedila (osnutke), ki še niso bila oddana.</p>
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
        <tr>
          <td colspan="4" style="text-align: center"><a href="ustvari_besedilo.php">DODAJ</a></td>
        </tr>
      </table>
    </main>
  </body>
</html>
