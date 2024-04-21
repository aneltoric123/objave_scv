<?php
session_start();
require_once '../connection.php';

// Pridobitev seznama e-poštnih naslovov lektorjev iz baze podatkov
$lektor_emails = array();
$result = mysqli_query($link, "SELECT email FROM users WHERE user_type = 1");
while ($row = mysqli_fetch_assoc($result)) {
  $lektor_emails[] = $row['email'];
}

// Preverjanje, ali je uporabnik prijavljen in ima vlogo administratorja
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 0) {
  header("Location: ../login/login.php");
  exit();
}

// Preverjanje, ali je bilo poslano ID objave
if (isset($_GET['id'])) {
  $id = $_GET['id'];

  // Pridobitev podrobnosti objave iz baze podatkov
  $result = mysqli_query($link, "SELECT id, naslov, besedilo, datum, lektorirano, lektor_email FROM posts WHERE id='$id'");
  $row = mysqli_fetch_assoc($result);

  $selectedPlatforms = array();
  $resultPlatforms = mysqli_query($link, "SELECT mesto_id FROM mesto_posts WHERE post_id='$id'");
  while ($rowPlatform = mysqli_fetch_assoc($resultPlatforms)) {
    $selectedPlatforms[] = $rowPlatform['mesto_id'];
  }

  // Pridobitev izbranih organizacij za objavo
  $selectedOrganizations = array();
  $resultOrganizations = mysqli_query($link, "SELECT sola_id FROM sola_posts WHERE post_id='$id'");
  while ($rowOrganization = mysqli_fetch_assoc($resultOrganizations)) {
    $selectedOrganizations[] = $rowOrganization['sola_id'];
  }

  // Count the number of images associated with the post
  $image_count_result = mysqli_query($link, "SELECT COUNT(*) FROM photos WHERE post_id = '$id'");
  $image_count_row = mysqli_fetch_array($image_count_result);
  $image_count = $image_count_row[0];


} else {
  // Če ID objave ni poslan, uporabnika preusmerimo na domačo stran administratorja
  header("Location: home_admin.php");
  exit();
}

?>
<!DOCTYPE>
<html>
<head>
  <title>Ogled objave</title>
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="stylesheet" type="text/css" href="view_post.css">
</head>
<body>
<header>
  <nav>
    <ul>
      <li><div id="home"><a href="home_admin.php">Domača stran</a></div></li>
      <li id="logout"><a href="../login/logout.php">Odjava</a></li>
    </ul>
  </nav>
</header>
<main>
  <h1>Ogled objave</h1>
  <div class="post-container">
    <div class="post-field">
      <label for="naslov">Naslov:</label>
      <p><?php echo $row['naslov']; ?></p>
    </div>
    <div class="post-field">
      <label for="besedilo">Besedilo:</label>
      <p><?php echo nl2br($row['besedilo']); ?></p>
    </div>
    <div class="post-field">
      <label for="datum">Datum:</label>
      <p><?php echo $row['datum']; ?></p>
    </div>
    <div class="post-field">
      <label>Je besedilo pregledano?</label>
      <p><?php echo $row['lektorirano'] == 0 ? 'Da' : 'Ne'; ?></p>
    </div>
    <div class="post-field" <?php if ($row['lektorirano'] == 0) {
    echo "style='display:none;'";
    } ?>>
      <label for="lektor_email">Lektor:</label>
      <p><?php echo $row['lektor_email']; ?></p>
    </div>
    <div class="post-field">
      <label for="mesto">Mesto objave:</label>
      <ul>
        <?php
        $resultMesto = mysqli_query($link, "SELECT id, ime FROM mesto_objav");
        while ($rowMesto = mysqli_fetch_assoc($resultMesto)) {
          if (in_array($rowMesto['id'], $selectedPlatforms)) {
            echo "<li>" . $rowMesto['ime'] . "</li>";
          }
        }
        ?>
      </ul>
    </div>
    <div class="post-field">
      <label for="sola">Šola objave:</label>
      <ul>
        <?php
        $resultSola = mysqli_query($link, "SELECT id, ime FROM sola_objav");
        while ($rowSola = mysqli_fetch_assoc($resultSola)) {
          if (in_array($rowSola['id'], $selectedOrganizations)) {
            echo "<li>" . $rowSola['ime'] . "</li>";
          }
        }
        ?>
      </ul>
    </div>
    <?php if ($image_count > 0): ?>
      <div class="post-field">
        <div id="images_download">
          <label for="images">Tukaj si naloži slike:</label>
          <a href="download_image.php?id=<?php echo $row['id']; ?>" class="download-btn">Naloži .zip</a>
        </div>
      </div>
    <?php endif; ?>
  </div>
  <a href="home_admin.php" class="back-btn">Nazaj</a>
  <a href="delete_post.php?id=<?php echo $row['id']; ?>" class="delete-btn">Izbriši</a>
</main>
</body>
</html>