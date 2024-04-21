<?php 
session_start(); 
require_once '../connection.php'; 

// Retrieve the list of lektor emails from the database
$lektor_emails = array();
$result = mysqli_query($link, "SELECT email, user_type FROM users WHERE user_type = 1");
while ($row = mysqli_fetch_assoc($result)) {
  $lektor_emails[] = $row['email'];
  $user_type = $row['user_type'];
}

// Preveri, ali je uporabnik prijavljen in ima vlogo pisatelja
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 1) {
    header("Location: ../login/login.php");
    exit();
}

// Preveri, ali je bil poslan obrazec za urejanje prispevka
if(isset($_POST['osnutek']) || isset($_POST['oddajte'])) {
    // Pridobi podatke iz obrazca
    $id = $_POST['id'];
    $naslov = $_POST['naslov'];
    $besedilo = $_POST['besedilo'];
    $datum = $_POST['datum'];


    //preveri ali je osnutek ali ne
    if(isset($_POST['osnutek']))
    {
        $user_id=$_SESSION['user_id'];
    }
    else if(isset($_POST['oddajte']))
    {
      $stmt = mysqli_prepare($link, "SELECT id FROM users WHERE user_type = 0");
        mysqli_stmt_execute($stmt);
        $result1 = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result1) > 0) {
          $row = mysqli_fetch_assoc($result1);
          $user_id = $row['id'];
        } else {
            echo "<script>alert('Admina ni bilo najdenega')</script>";
            $user_id=$_SESSION['user_id'];
            header("Location: edit_post.php");
            exit();
        }
    }


    // Get the new state of the checkboxes from $_POST
    $platform = isset($_POST['platform']) ? $_POST['platform'] : array();
    $org = isset($_POST['org']) ? $_POST['org'] : array();

    // Delete post's platform checkboxes from the database
    $stmt = mysqli_prepare($link, "DELETE FROM mesto_posts WHERE post_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    // Delete post's organization checkboxes from the database
    $stmt = mysqli_prepare($link, "DELETE FROM sola_posts WHERE post_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    foreach ($platform as $platform_value) {
      $stmt = mysqli_prepare($link, "INSERT INTO mesto_posts (post_id, mesto_id) VALUES (?, ?)");
      mysqli_stmt_bind_param($stmt, "ii", $id, $platform_value);
      mysqli_stmt_execute($stmt);
    }

    foreach ($org as $org_value) {
      $stmt = mysqli_prepare($link, "INSERT INTO sola_posts (sola_id, post_id) VALUES (?, ?)");
      mysqli_stmt_bind_param($stmt, "ii", $org_value, $id);
      mysqli_stmt_execute($stmt);
    }


    // Posodobi prispevek v bazi podatkov
    $query = "UPDATE posts SET naslov='$naslov', besedilo='$besedilo', datum='$datum', lektorirano=0, user_id='$user_id', lektor_email='' WHERE id='$id'";
    mysqli_query($link, $query);

  // Preusmeri uporabnika na domačo stran pisatelja
  header("Location: home_lektor.php");
  exit();
}

// Preveri, ali je bil poslan ID prispevka
if(isset($_GET['id'])) {
  $id = $_GET['id'];

  // Pridobi podatke o prispevku iz baze podatkov
  $result = mysqli_query($link, "SELECT naslov, besedilo, datum, lektorirano, lektor_email FROM posts WHERE id='$id'");
  $row = mysqli_fetch_assoc($result);


  $selectedPlatforms = array();
  $resultPlatforms = mysqli_query($link, "SELECT mesto_id FROM mesto_posts WHERE post_id='$id'");
  while ($rowPlatform = mysqli_fetch_assoc($resultPlatforms)) {
    $selectedPlatforms[] = $rowPlatform['mesto_id'];
  }

  // Retrieve the selected organizations for the post
  $selectedOrganizations = array();
  $resultOrganizations = mysqli_query($link, "SELECT sola_id FROM sola_posts WHERE post_id='$id'");
  while ($rowOrganization = mysqli_fetch_assoc($resultOrganizations)) {
    $selectedOrganizations[] = $rowOrganization['sola_id'];
  }
} else {
  // Če ni bil poslan ID prispevka, preusmeri uporabnika na domačo stran pisatelja
  header("Location: home_pisatelj.php");
  exit();
}

?>
<!DOCTYPE>
<html>
  <head>
    <title>Lektoriranje</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" type="text/css" href="../pisatelj/ustvari_besedilo.css">
  </head>
  <body>
    <header>
      <nav>
        <ul>
          <li><div id="home"><a href="home_lektor.php">Domača stran</a></li></div>
          <li id="logout"><a href="../login/logout.php">Odjava</a></li>
        </ul>
      </nav>
    </header>
    <main>
      <h1>Lektorirajte besedilo</h1>
      <form method="post" action="edit_post.php">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <label for="naslov">Naslov:</label>
        <input type="text" name="naslov" value="<?php echo $row['naslov']; ?>">
        <label for="besedilo">Besedilo:</label>
        <textarea name="besedilo" rows="20" cols="50" required><?php echo $row['besedilo']; ?></textarea>
        <label for="datum">Datum:</label>
        <input type="date" name="datum" value="<?php echo $row['datum']; ?>">
        

        <label>Platforma:</label>
          <div>
            <input type="checkbox" id="spletna_stran" name="platform[]" value="1" onclick="showCheckboxes()" <?php if(in_array("1", $selectedPlatforms)) { echo "checked"; } ?>>Spletna stran
          </div>
          <br>
          <div>
            <input type="checkbox" id="druzabni_mediji" name="platform[]" value="2" onclick="showCheckboxes()" <?php if(in_array("2", $selectedPlatforms)) { echo "checked"; } ?>>Družabni mediji
          </div>
          <br>
          <div>
            <input type="checkbox" id="sta_mediji" name="platform[]" value="3" onclick="showCheckboxes()" <?php if(in_array("3", $selectedPlatforms)) { echo "checked"; } ?>>STA, Mediji
          </div><br><br>

          <div id="checkboxes" <?php if(empty($selectedPlatforms) && empty($selectedOrganizations)) { echo "style='display:none;'"; } ?>>
            <label>Šole:</label>
            <div>
              <input type="checkbox" id="scv" name="org[]" value="1" <?php if(in_array("1", $selectedOrganizations)) { echo "checked"; } ?>>ŠCV
            </div>
            <div>
              <input type="checkbox" id="ssgo" name="org[]" value="2" <?php if(in_array("2", $selectedOrganizations)) { echo "checked"; } ?>>ŠSGO
            </div>
            <div>
              <input type="checkbox" id="ers" name="org[]" value="3" <?php if(in_array("3", $selectedOrganizations)) { echo "checked"; } ?>>ERŠ
            </div>
            <div>
              <input type="checkbox" id="ssd" name="org[]" value="4" <?php if(in_array("4", $selectedOrganizations)) { echo "checked"; } ?>>ŠSD
            </div>
            <div>
              <input type="checkbox" id="gim" name="org[]" value="5" <?php if(in_array("5", $selectedOrganizations)) { echo "checked"; } ?>>GIM
            </div>
            <div>
              <input type="checkbox" id="mic" name="org[]" value="6" <?php if(in_array("6", $selectedOrganizations)) { echo "checked"; } ?>>MIC
            </div>
            <div>
              <input type="checkbox" id="vss" name="org[]" value="7" <?php if(in_array("7", $selectedOrganizations)) { echo "checked"; } ?>>VSŠ
            </div>
            <br><br>
          </div>    

        <input type="submit" name="oddajte" value="Oddajte Adminu">
        <input type="submit" name="osnutek" value="Shranite">
      </form>
      <script>
        function showCheckboxes() {
            const selectedOptions = document.querySelectorAll('input[name="platform[]"]:checked');
            let showCheckboxes = false;
            selectedOptions.forEach(option => {
              if (option.value === "1" || option.value === "2") {
                showCheckboxes = true;
                
                document.querySelectorAll('input[name="org[]"]').forEach(checkbox => {
                  checkbox.checked = false;
                });
              }
            });
            if (showCheckboxes) {
              document.getElementById("checkboxes").style.display = "block";
            } else {
              document.getElementById("checkboxes").style.display = "none";
            }
          }

          window.addEventListener("load", function() {
            updateEmailField();
            showCheckboxes();
            
            // Check the selected organizations checkboxes
            <?php foreach ($selectedOrganizations as $org) { ?>
              document.getElementById("<?php echo 'org'.$org; ?>").checked = true;
            <?php } ?>
          });
      </script>
    </main>
  </body>
</html>
