<?php 
  session_start(); 
  require_once '../connection.php'; 

  // Check if user is logged in and has the role of a writer
  if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
      header("Location: ../login/login.php");
      exit();
  }

?>

<!DOCTYPE html>
<html>
  <head>
      <title>Dodajte besedilo</title>
      <link rel="stylesheet" type="text/css" href="ustvari_besedilo.css">
  </head>
  <body>
    <header>
      <nav>
        <ul>
          <li><a href="home_pisatelj.php">Domača stran</a></li>
          <li id="logout"><a href="../login/logout.php">Odjava</a></li>
        </ul>
      </nav>
    </header>
    <main>
      <h1>Ustvarite novo besedilo</h1>
      <form action="submit_event.php" method="post" enctype="multipart/form-data">
          <label for="title">Naslov:</label>
          <input type="text" id="title" name="title" required><br><br>

          <label for="body">Besedilo:</label>
          <textarea id="body" name="body" rows="20" cols="50" required></textarea><br><br>

          <label for="date">Datum:</label>
          <input type="date" id="date" name="date" required><br><br>

          <div class="radio-group">
            <label>Ali je besedilo lektorirano?</label>
            <input type="radio" id="yes" name="proofread" value="0" onclick="hideEmail()" required>
            Da
            <input type="radio" id="no" name="proofread" value="1" onclick="showEmail()" required>
            Ne
          </div>

          <div id="emailField" style="display:none;">
              <label for="email">Lektoriju:</label>
              <select id="email" name="email">
                <?php 
                  $result = mysqli_query($link, "SELECT email FROM users WHERE user_type = 1");
                  while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['email'] . "'>" . $row['email'] . "</option>";
                  }
                ?>
              </select>
          </div><br>

          <label for="images">Naložite slike:</label>
          <input type="file" id="images" name="images[]" accept="image/*" multiple><br><br>

          <label>Platforma:</label>
          <div>
            <input type="checkbox" id="spletna_stran" name="platform[]" value="1" onclick="showCheckboxes()">Spletna stran
          </div>
          <br>
          <div>
            <input type="checkbox" id="druzabni_mediji" name="platform[]" value="2" onclick="showCheckboxes()">Družabni mediji
          </div>
          <br>
          <div>
            <input type="checkbox" id="sta_mediji" name="platform[]" value="3" onclick="showCheckboxes()">STA, Mediji
          </div><br><br>

          <div id="checkboxes" style="display:none;">
            <label>Šole:</label>
            <div>
              <input type="checkbox" id="scv" name="org[]" value="1">ŠCV
            </div>
            <div>
              <input type="checkbox" id="ssgo" name="org[]" value="2">ŠSGO
            </div>
            <div>
              <input type="checkbox" id="ers" name="org[]" value="3">ERŠ
            </div>
            <div>
              <input type="checkbox" id="ssd" name="org[]" value="4">ŠSD
            </div>
            <div>
              <input type="checkbox" id="gim" name="org[]" value="5">GIM
            </div>
            <div>
              <input type="checkbox" id="mic" name="org[]" value="6">MIC
            </div>
            <div>
              <input type="checkbox" id="vss" name="org[]" value="7">VSŠ
            </div>
            <br><br>   
          </div>       

          <input type="submit" name="oddajte" value="Oddajte">
          <input type="submit" name="osnutek" value="Shrani osnutek">
      </form>

      <script>
          function showEmail() {
              document.getElementById("emailField").style.display = "block";
              document.getElementById("email").required = true;
          }

          function hideEmail() {
              document.getElementById("emailField").style.display = "none";
              document.getElementById("email").required = false;
              document.getElementById("email").value = "";
          }

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
      </script>
    </main>
  </body>
</html>
