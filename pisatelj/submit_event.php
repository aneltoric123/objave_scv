<?php
session_start();
require_once '../connection.php';

// Check if user is logged in and has the role of a writer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 2) {
    header("Location: ../login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$lektor_emailll = '';
$pisatelj_emailll = '';
$admin_emailll = '';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    // Check if all required fields are filled
    if (isset($_POST['title']) && isset($_POST['body']) && isset($_POST['date']) && isset($_POST['proofread'])) {
        $title = $_POST['title'];
        $body = $_POST['body'];
        $date = $_POST['date'];
        $lektorirano = $_POST['proofread'];
        $lektor_email = isset($_POST['email']) ? $_POST['email'] : '';

        //preveri ali je osnutek ali ne
        if(isset($_POST['osnutek']))
        {
            $user_id=$_SESSION['user_id'];
        }
        else if(isset($_POST['oddajte'])) //Dobiva id za pravega userja
        {
            if ($lektorirano == 1) {
                $stmt = mysqli_prepare($link, "SELECT id FROM users WHERE email = ? AND user_type = 1");
                if (!$stmt) {
                    echo "<script>alert('Napaka pri pripravi poizvedbe: " . mysqli_error($link) . "')</script>";
                    $user_id=$_SESSION['user_id'];
                    header("Location: edit_post.php");
                    exit();
                }
                mysqli_stmt_bind_param($stmt, "s", $lektor_email);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);

                    
                    $stmt = mysqli_prepare($link, "SELECT email FROM users WHERE id = ?");
                    mysqli_stmt_bind_param($stmt, "i", $user_id);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    if (mysqli_num_rows($result) > 0) {
                        $row1 = mysqli_fetch_assoc($result);
                        $pisatelj_emailll = $row1['email'];
                    }


                    $user_id = $row['id'];


                    $stmt = mysqli_prepare($link, "SELECT email FROM users WHERE id = ?");
                    mysqli_stmt_bind_param($stmt, "i", $user_id);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    if (mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);
                        $lektor_emailll = $row['email'];
                    }
                    

                } else {
                    echo "<script>alert('Uporabnika z tem emailom ni bilo najdenega')</script>";
                    $user_id=$_SESSION['user_id'];
                    header("Location: edit_post.php");
                    exit();
                }
            } else if($lektorirano == 0) {
                $stmt = mysqli_prepare($link, "SELECT id FROM users WHERE user_type = 0");
                mysqli_stmt_execute($stmt);
                $result1 = mysqli_stmt_get_result($stmt);
                if (mysqli_num_rows($result1) > 0) {
                    $row = mysqli_fetch_assoc($result1);
                    $user_id = $row['id'];

                    // With this code to get the recipient email address from the user ID:
                    $stmt = mysqli_prepare($link, "SELECT email FROM users WHERE id = ?");
                    mysqli_stmt_bind_param($stmt, "i", $user_id);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    if (mysqli_num_rows($result) > 0) {
                        $row1 = mysqli_fetch_assoc($result);
                        $admin_emailll = $row1['email'];
                    }

                } else {
                    echo "<script>alert('Admina ni bilo najdenega')</script>";
                    $user_id=$_SESSION['user_id'];
                    header("Location: edit_post.php");
                    exit();
                }
            }
            else{
                $user_id=$_SESSION['user_id'];
            }
    }

        // Check if any of the new checkboxes are checked
        $platform = isset($_POST['platform']) ? $_POST['platform'] : array();
        $org = isset($_POST['org']) ? $_POST['org'] : array();


        // Insert new post into the database
        $stmt = mysqli_prepare($link, "INSERT INTO posts (naslov, besedilo, datum, lektorirano, user_id, lektor_email) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssssis", $title, $body, $date, $lektorirano, $user_id, $lektor_email);
        mysqli_stmt_execute($stmt);

        // Get ID of the new post
        $post_id = mysqli_insert_id($link);

        // Insert new checkboxes into the database
        foreach ($platform as $platform_value) {
            $stmt = mysqli_prepare($link, "INSERT INTO mesto_posts (post_id, mesto_id) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ii", $post_id, $platform_value);
            mysqli_stmt_execute($stmt);
        }

        foreach ($org as $org_value) {
            $stmt = mysqli_prepare($link, "INSERT INTO sola_posts (sola_id, post_id) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ii", $org_value, $post_id);
            mysqli_stmt_execute($stmt);
        }
        
        // Check if any images were uploaded
        if (count($_FILES['images']['name']) > 0 && $_FILES['images']['name'][0] != '') {
            $images = $_FILES['images'];

            // Loop through each uploaded image
            for ($i = 0; $i < count($images['name']); $i++) {
                $image_name = $images['name'][$i];
                $image_size = $images['size'][$i];
                $image_type = $images['type'][$i];
                $image_error = $images['error'][$i];
                $image_tmp_name = $images['tmp_name'][$i];

                // Read image data into a variable
                $image_data = file_get_contents($image_tmp_name);

                // Insert new photo into the database
                $stmt = mysqli_prepare($link, "INSERT INTO photos (path, post_id) VALUES (?, ?)");
                mysqli_stmt_bind_param($stmt, "si", $image_data, $post_id);
                mysqli_stmt_send_long_data($stmt, 0, $image_data);
                mysqli_stmt_execute($stmt);
            }
        } 

        if ($lektor_emailll != '') {
            $subject = 'Nova objava pripravljena na urejanje';
            $message = "Pozdravljeni,\n\n" .
               "Prejeli ste nov zapis, ki ga je potrebno lektorirati.\n" .
               "Za več informacij se prijavite v vaš račun in preglejte zapis.\n\n" .
               "Lep pozdrav,\n" .
               "Ekipa ObjaveSCV";
    
            mail($lektor_emailll, $subject, $message);
        }
    
        if ($pisatelj_emailll  != '') {
            $subject = 'Nova objava je bila poslana';
            $message = "Spoštovani,\n\nHvala vam za oddajo vašega prispevka. Vaša objava je bila uspešno sprejeta in bo pregledana s strani našega uredništva. V kolikor bo vaš prispevek primeren za objavo, boste obveščeni o datumu objave.\n\nLep pozdrav,\nEkipa ObjaveSCV";
            $headers = 'From: _@gmail.com' . "\r\n" .
                'Reply-To: _@gmail.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
    
            mail($pisatelj_emailll, $subject, $message);
        }

        if ($admin_emailll  != '') {
            $subject = 'Nova objava pripravljena za ogled';
            $message = "Spoštovani administrator,\n\nNekdo je oddal nov prispevek na spletno stran. Prosimo, da preverite prispevek in ga potrdite, če ustreza standardom.\n\nLep pozdrav,\nEkipa spletne strani";
            $headers = 'From: _@gmail.com' . "\r\n" .
                'Reply-To: _@gmail.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
    
            mail($admin_emailll, $subject, $message);
        }

        header("Location: ../pisatelj/home_pisatelj.php");
        exit();

    } else {
        // Required fields are missing
        echo "Required fields are missing.";
    }


}
?>
